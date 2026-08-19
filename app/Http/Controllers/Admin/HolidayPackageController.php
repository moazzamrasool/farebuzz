<?php

namespace App\Http\Controllers\Admin;

use App\Exports\HolidayPackagesTemplateExport;
use App\Http\Controllers\Admin\Concerns\HandlesBulkImportResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HolidayPackageRequest;
use App\Imports\HolidayPackagesImport;
use App\Models\Activity;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\HolidayPackage;
use App\Models\PackageFeature;
use App\Models\TravelCategory;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class HolidayPackageController extends Controller
{
    use GeneratesUniqueSlug, HandlesBulkImportResponse;

    public function index()
    {
        $holidayPackages = HolidayPackage::with(['destination', 'categories', 'photos'])
            ->orderBy('sort_order')->latest()->paginate(15);

        return view('admin.holiday-packages.index', compact('holidayPackages'));
    }

    public function create()
    {
        return view('admin.holiday-packages.create', $this->formOptions());
    }

    public function store(HolidayPackageRequest $request)
    {
        $package = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data = array_intersect_key($data, array_flip((new HolidayPackage)->getFillable()));
            $data['featured'] = $request->boolean('featured');
            $data['is_best_seller'] = $request->boolean('is_best_seller');
            $data['robots_index'] = $request->boolean('robots_index');
            $data['robots_follow'] = $request->boolean('robots_follow');
            $data['slug'] = $this->generateUniqueSlug(HolidayPackage::class, $request->filled('slug') ? $request->slug : $request->title);

            if ($request->hasFile('og_image')) {
                $data['og_image'] = $request->file('og_image')->store('holiday-packages/og', 'public');
            } else {
                unset($data['og_image']);
            }

            $package = HolidayPackage::create($data);
            $this->syncAllSections($request, $package);

            return $package;
        });

        return redirect()->route('crm.holiday-packages.edit', $package->id)
            ->with('success', 'Holiday package created successfully.');
    }

    public function edit(HolidayPackage $holidayPackage)
    {
        $holidayPackage->load([
            'categories', 'activities', 'optionalActivities', 'inclusionFeatures', 'exclusionFeatures',
            'customInclusions', 'customExclusions', 'hotels', 'itineraries',
            'photos', 'faqs', 'reviews', 'departureCities', 'roomTypes', 'relatedPackages',
        ]);

        return view('admin.holiday-packages.edit', array_merge(
            ['holidayPackage' => $holidayPackage],
            $this->formOptions($holidayPackage)
        ));
    }

    public function update(HolidayPackageRequest $request, HolidayPackage $holidayPackage)
    {
        DB::transaction(function () use ($request, $holidayPackage) {
            $data = $request->validated();
            $data = array_intersect_key($data, array_flip((new HolidayPackage)->getFillable()));
            $data['featured'] = $request->boolean('featured');
            $data['is_best_seller'] = $request->boolean('is_best_seller');
            $data['robots_index'] = $request->boolean('robots_index');
            $data['robots_follow'] = $request->boolean('robots_follow');

            if ($request->filled('slug') && $holidayPackage->slug !== $request->slug) {
                $data['slug'] = $this->generateUniqueSlug(HolidayPackage::class, $request->slug, $holidayPackage->id);
            } elseif (!$request->filled('slug') && $holidayPackage->title !== $request->title) {
                $data['slug'] = $this->generateUniqueSlug(HolidayPackage::class, $request->title, $holidayPackage->id);
            }

            if ($request->hasFile('og_image')) {
                if ($holidayPackage->og_image) {
                    Storage::disk('public')->delete($holidayPackage->og_image);
                }
                $data['og_image'] = $request->file('og_image')->store('holiday-packages/og', 'public');
            } else {
                unset($data['og_image']);
            }

            $holidayPackage->update($data);
            $this->syncAllSections($request, $holidayPackage);
        });

        return redirect()->route('crm.holiday-packages.edit', $holidayPackage->id)
            ->with('success', 'Holiday package updated successfully.');
    }

    public function destroy(HolidayPackage $holidayPackage)
    {
        foreach ($holidayPackage->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        $holidayPackage->delete();

        return redirect()->route('crm.holiday-packages.index')
            ->with('success', 'Holiday package deleted successfully.');
    }

    public function toggleStatus(HolidayPackage $holidayPackage)
    {
        $holidayPackage->update([
            'status' => $holidayPackage->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('crm.holiday-packages.index')
            ->with('success', 'Status updated successfully.');
    }

    public function downloadTemplate()
    {
        return Excel::download(new HolidayPackagesTemplateExport, 'holiday-packages-template.xlsx');
    }

    public function bulkUpload(Request $request)
    {
        // xlsx/xls only — CSV can't carry the Package Core + Itinerary two-sheet
        // format this module's template uses.
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $import = new HolidayPackagesImport;
        Excel::import($import, $request->file('file'));

        return $this->bulkImportResponse($import->importedCount(), $import->rowErrors());
    }

    public function downloadErrorReport(string $token)
    {
        return $this->streamImportErrorReport($token);
    }

    protected function bulkUploadErrorRouteName(): string
    {
        return 'crm.holiday-packages.bulk-upload.errors';
    }

    private function formOptions(?HolidayPackage $exclude = null): array
    {
        return [
            'destinations'       => Destination::where('status', 'active')->orderBy('name')->get(),
            'travelCategories'   => TravelCategory::where('status', 'active')->orderBy('name')->get(),
            'activities'         => Activity::where('status', 'active')->orderBy('name')->get(),
            'inclusionFeatures'  => PackageFeature::inclusions()->where('status', 'active')->orderBy('title')->get(),
            'exclusionFeatures'  => PackageFeature::exclusions()->where('status', 'active')->orderBy('title')->get(),
            'hotels'             => Hotel::where('status', 'active')->orderBy('name')->with('roomTypes')->get(),
            'candidatePackages'  => HolidayPackage::where('status', 'active')
                ->when($exclude, fn ($query) => $query->where('id', '!=', $exclude->id))
                ->orderBy('title')->get(),
        ];
    }

    // Wraps every child-relation sync for one section of the tabbed form.
    private function syncAllSections(HolidayPackageRequest $request, HolidayPackage $package): void
    {
        $package->categories()->sync($request->input('category_ids', []));
        $package->activities()->sync($request->input('activity_ids', []));
        $package->relatedPackages()->sync($request->input('related_ids', []));

        $this->syncFeatures($request, $package);
        $this->syncCustomFeatures($request, $package);
        $this->syncDepartureCities($request, $package);
        $this->syncRoomTypes($request, $package);
        $this->syncItineraries($request, $package);
        $this->syncHotels($request, $package);
        $this->syncPackageActivities($request, $package);
        $this->syncPhotos($request, $package);
        $this->syncFaqs($request, $package);
        $this->syncReviews($request, $package);
    }

    private function syncFeatures(HolidayPackageRequest $request, HolidayPackage $package): void
    {
        $ids = array_merge(
            $request->input('inclusion_feature_ids', []),
            $request->input('exclusion_feature_ids', [])
        );
        $package->features()->sync($ids);
    }

    // Custom one-off inclusion/exclusion lines are simple text lists — replaced wholesale each save.
    private function syncCustomFeatures(HolidayPackageRequest $request, HolidayPackage $package): void
    {
        $package->customFeatures()->delete();

        foreach ($request->input('custom_inclusions', []) as $index => $title) {
            if (trim($title) === '') continue;
            $package->customFeatures()->create(['type' => 'inclusion', 'title' => $title, 'sort_order' => $index]);
        }
        foreach ($request->input('custom_exclusions', []) as $index => $title) {
            if (trim($title) === '') continue;
            $package->customFeatures()->create(['type' => 'exclusion', 'title' => $title, 'sort_order' => $index]);
        }
    }

    private function syncDepartureCities(HolidayPackageRequest $request, HolidayPackage $package): void
    {
        $package->departureCities()->delete();

        foreach ($request->input('departure_cities', []) as $index => $city) {
            if (trim($city) === '') continue;
            $package->departureCities()->create(['city_name' => $city, 'sort_order' => $index]);
        }
    }

    private function syncRoomTypes(HolidayPackageRequest $request, HolidayPackage $package): void
    {
        $package->roomTypes()->delete();

        foreach ($request->input('room_types', []) as $index => $row) {
            if (empty($row['name'])) continue;
            $package->roomTypes()->create([
                'name' => $row['name'],
                'price' => $row['price'] ?? 0,
                'discounted_price' => $row['discounted_price'] ?? null,
                'sort_order' => $index,
            ]);
        }
    }

    private function syncItineraries(HolidayPackageRequest $request, HolidayPackage $package): void
    {
        $package->itineraries()->delete();

        foreach ($request->input('itineraries', []) as $index => $row) {
            if (empty($row['title'])) continue;

            $bulletPoints = collect(explode("\n", $row['bullet_points'] ?? ''))
                ->map(fn ($line) => trim($line))
                ->filter()
                ->values()
                ->all();

            $package->itineraries()->create([
                'day_number'    => $row['day_number'] ?? ($index + 1),
                'title'         => $row['title'],
                'route_summary' => $row['route_summary'] ?? null,
                'detail'        => $row['detail'] ?? null,
                'bullet_points' => $bulletPoints,
                'meal_tags'     => $row['meal_tags'] ?? [],
                'sort_order'    => $index,
            ]);
        }
    }

    // Hotels attached to the package, priced like optional paid add-ons — entirely
    // optional, an empty selection is valid. Rows are keyed by hotel_id (e.g.
    // hotels[5][price]); only rows with an "attach" flag are synced, so unchecking a
    // card removes it on save. Mirrors syncPackageActivities() below.
    private function syncHotels(HolidayPackageRequest $request, HolidayPackage $package): void
    {
        $validHotelIds = Hotel::pluck('id')->all();
        $sync = [];

        foreach ($request->input('hotels', []) as $hotelId => $row) {
            if (empty($row['attach']) || !in_array((int) $hotelId, $validHotelIds, true)) {
                continue;
            }

            $sync[(int) $hotelId] = [
                'unique_id'    => $package->unique_id,
                'room_type_id' => $row['room_type_id'] !== '' && isset($row['room_type_id']) ? $row['room_type_id'] : null,
                'price'        => $row['price'] !== '' && isset($row['price']) ? $row['price'] : null,
                'is_optional'  => !empty($row['is_optional']),
                'nights'       => $row['nights'] ?? 1,
                'sort_order'   => $row['sort_order'] ?? 0,
                'note'         => $row['note'] ?? null,
                'day_number'   => $row['day_number'] !== '' && isset($row['day_number']) ? $row['day_number'] : null,
            ];
        }

        $package->hotels()->sync($sync);
    }

    // Optional paid Activities (add-ons) — entirely optional, an empty selection is valid.
    // Rows are keyed by activity_id (e.g. activities[5][price]); only rows with an
    // "attach" flag are synced, so unchecking a card removes it on save.
    private function syncPackageActivities(HolidayPackageRequest $request, HolidayPackage $package): void
    {
        $validActivityIds = Activity::pluck('id')->all();
        $sync = [];

        foreach ($request->input('activities', []) as $activityId => $row) {
            if (empty($row['attach']) || !in_array((int) $activityId, $validActivityIds, true)) {
                continue;
            }

            $sync[(int) $activityId] = [
                'unique_id'   => $package->unique_id,
                'price'       => $row['price'] !== '' && isset($row['price']) ? $row['price'] : null,
                'is_optional' => !empty($row['is_optional']),
                'sort_order'  => $row['sort_order'] ?? 0,
                'note'        => $row['note'] ?? null,
                'day_number'  => $row['day_number'] !== '' && isset($row['day_number']) ? $row['day_number'] : null,
            ];
        }

        $package->optionalActivities()->sync($sync);
    }

    private function syncPhotos(HolidayPackageRequest $request, HolidayPackage $package): void
    {
        $rows = $request->input('photos', []);
        $files = $request->file('photos', []);
        $keepIds = [];

        foreach ($rows as $index => $row) {
            $isCover = !empty($row['is_cover']);
            $file = $files[$index]['file'] ?? null;

            if (!empty($row['id'])) {
                $photo = $package->photos()->find($row['id']);
                if ($photo) {
                    $attributes = ['is_cover' => $isCover, 'sort_order' => $index];

                    if ($file) {
                        Storage::disk('public')->delete($photo->path);
                        $attributes['path'] = $file->store('holiday-packages/photos', 'public');
                    }

                    $photo->update($attributes);
                    $keepIds[] = $photo->id;
                }
                continue;
            }

            if ($file) {
                $photo = $package->photos()->create([
                    'path' => $file->store('holiday-packages/photos', 'public'),
                    'is_cover' => $isCover,
                    'sort_order' => $index,
                ]);
                $keepIds[] = $photo->id;
            }
        }

        $removed = $package->photos()->whereNotIn('id', $keepIds)->get();
        foreach ($removed as $photo) {
            Storage::disk('public')->delete($photo->path);
            $photo->delete();
        }
    }

    private function syncFaqs(HolidayPackageRequest $request, HolidayPackage $package): void
    {
        $keepIds = [];

        foreach ($request->input('faqs', []) as $index => $row) {
            if (empty($row['question'])) continue;

            $attributes = [
                'question' => $row['question'],
                'answer' => $row['answer'] ?? null,
                'sort_order' => $index,
            ];

            if (!empty($row['id']) && ($faq = $package->faqs()->find($row['id']))) {
                $faq->update($attributes);
            } else {
                $faq = $package->faqs()->create($attributes);
            }
            $keepIds[] = $faq->id;
        }

        $package->faqs()->whereNotIn('id', $keepIds)->delete();
    }

    private function syncReviews(HolidayPackageRequest $request, HolidayPackage $package): void
    {
        $keepIds = [];

        foreach ($request->input('reviews', []) as $index => $row) {
            if (empty($row['reviewer_name'])) continue;

            $attributes = [
                'reviewer_name'       => $row['reviewer_name'],
                'rating'              => $row['rating'] ?? 0,
                'hotels_rating'       => $row['hotels_rating'] ?? null,
                'sightseeing_rating'  => $row['sightseeing_rating'] ?? null,
                'food_rating'         => $row['food_rating'] ?? null,
                'value_rating'        => $row['value_rating'] ?? null,
                'comment'             => $row['comment'] ?? null,
                'review_date'         => $row['review_date'] ?? now(),
                'verified'            => !empty($row['verified']),
                'sort_order'          => $index,
            ];

            if (!empty($row['id']) && ($review = $package->reviews()->find($row['id']))) {
                $review->update($attributes);
            } else {
                $review = $package->reviews()->create($attributes);
            }
            $keepIds[] = $review->id;
        }

        $package->reviews()->whereNotIn('id', $keepIds)->delete();
    }
}
