<?php

namespace App\Http\Controllers\Admin;

use App\Exports\HotelsTemplateExport;
use App\Http\Controllers\Admin\Concerns\HandlesBulkImportResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HotelRequest;
use App\Imports\HotelsImport;
use App\Models\Amenity;
use App\Models\Destination;
use App\Models\Hotel;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class HotelController extends Controller
{
    use GeneratesUniqueSlug, HandlesBulkImportResponse;

    public function index()
    {
        $hotels = Hotel::with('amenities', 'destination')->orderBy('sort_order')->latest()->paginate(15);
        return view('admin.hotels.index', compact('hotels'));
    }

    public function create()
    {
        return view('admin.hotels.create', $this->formOptions());
    }

    public function store(HotelRequest $request)
    {
        $data = $this->prepareData($request);
        $data['slug'] = $this->generateUniqueSlug(Hotel::class, $request->filled('slug') ? $request->slug : $request->name);
        $hotel = Hotel::create($data);
        $hotel->amenities()->sync($request->input('amenity_ids', []));
        $this->syncRoomTypes($request, $hotel);

        return redirect()->route('crm.hotels.edit', $hotel->id)
            ->with('success', 'Hotel created successfully.');
    }

    public function edit(Hotel $hotel)
    {
        $hotel->load('amenities', 'roomTypes');

        return view('admin.hotels.edit', array_merge(['hotel' => $hotel], $this->formOptions()));
    }

    public function update(HotelRequest $request, Hotel $hotel)
    {
        $data = $this->prepareData($request, $hotel);
        if ($request->filled('slug') && $hotel->slug !== $request->slug) {
            $data['slug'] = $this->generateUniqueSlug(Hotel::class, $request->slug, $hotel->id);
        } elseif (!$request->filled('slug') && $hotel->name !== $request->name) {
            $data['slug'] = $this->generateUniqueSlug(Hotel::class, $request->name, $hotel->id);
        }
        $hotel->update($data);
        $hotel->amenities()->sync($request->input('amenity_ids', []));
        $this->syncRoomTypes($request, $hotel);

        return redirect()->route('crm.hotels.edit', $hotel->id)
            ->with('success', 'Hotel updated successfully.');
    }

    public function destroy(Hotel $hotel)
    {
        if ($hotel->holidayPackages()->exists()) {
            return redirect()->route('crm.hotels.index')
                ->with('error', 'Cannot delete this hotel — it is still linked to one or more holiday packages.');
        }

        $this->deleteImages($hotel);
        $hotel->amenities()->detach();
        $hotel->delete();

        return redirect()->route('crm.hotels.index')
            ->with('success', 'Hotel deleted successfully.');
    }

    public function toggleStatus(Hotel $hotel)
    {
        $hotel->update([
            'status' => $hotel->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('crm.hotels.index')
            ->with('success', 'Status updated successfully.');
    }

    public function downloadTemplate()
    {
        return Excel::download(new HotelsTemplateExport, 'hotels-template.xlsx');
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $import = new HotelsImport;
        Excel::import($import, $request->file('file'));

        return $this->bulkImportResponse($import->importedCount(), $import->rowErrors());
    }

    public function downloadErrorReport(string $token)
    {
        return $this->streamImportErrorReport($token);
    }

    protected function bulkUploadErrorRouteName(): string
    {
        return 'crm.hotels.bulk-upload.errors';
    }

    private function formOptions(): array
    {
        return [
            'amenities'    => Amenity::where('status', 'active')->orderBy('name')->get(),
            'destinations' => Destination::where('status', 'active')->orderBy('name')->get(),
        ];
    }

    private function prepareData(HotelRequest $request, ?Hotel $hotel = null): array
    {
        $data = $request->validated();
        unset($data['cover_image'], $data['gallery_images'], $data['amenity_ids'], $data['room_types'], $data['slug'], $data['og_image']);

        $data['robots_index'] = $request->boolean('robots_index');
        $data['robots_follow'] = $request->boolean('robots_follow');

        if ($request->hasFile('og_image')) {
            if ($hotel?->og_image) {
                Storage::disk('public')->delete($hotel->og_image);
            }
            $data['og_image'] = $request->file('og_image')->store('hotels/og', 'public');
        }

        if ($request->hasFile('cover_image')) {
            if ($hotel?->cover_image) {
                Storage::disk('public')->delete($hotel->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('hotels', 'public');
        }

        if ($request->hasFile('gallery_images')) {
            if ($hotel?->gallery_images) {
                Storage::disk('public')->delete($hotel->gallery_images);
            }
            $data['gallery_images'] = collect($request->file('gallery_images'))
                ->map(fn ($file) => $file->store('hotels/gallery', 'public'))
                ->all();
        }

        return $data;
    }

    // Priced per night, entirely optional. Existing rows are matched by id and updated
    // in place (same keep/update/delete pattern as syncPhotos()/syncFaqs() on the
    // holiday-package form) — unlike a blanket delete-and-recreate, this means a room's
    // uploaded images survive an unrelated edit elsewhere on the form; new files only
    // replace them when the admin actually re-uploads for that row.
    private function syncRoomTypes(HotelRequest $request, Hotel $hotel): void
    {
        $keepIds = [];

        foreach ($request->input('room_types', []) as $index => $row) {
            if (empty($row['name'])) continue;

            $attributes = [
                'name' => $row['name'],
                'price' => $row['price'] ?? 0,
                'discounted_price' => $row['discounted_price'] ?? null,
                'occupancy_adults' => $row['occupancy_adults'] ?? 2,
                'occupancy_children' => $row['occupancy_children'] ?? 0,
                'bed_type' => $row['bed_type'] ?? null,
                'size_sqft' => $row['size_sqft'] ?? null,
                'meal_plan' => $row['meal_plan'] ?? 'room_only',
                'refundable' => $request->boolean("room_types.{$index}.refundable"),
                'sort_order' => $index,
            ];

            $newFiles = collect($request->file("room_types.{$index}.images", []));
            $existing = !empty($row['id']) ? $hotel->roomTypes()->find($row['id']) : null;

            if ($newFiles->isNotEmpty()) {
                if ($existing?->images) {
                    Storage::disk('public')->delete($existing->images);
                }
                $attributes['images'] = $newFiles->map(fn ($file) => $file->store('hotels/rooms', 'public'))->values()->all();
            }

            if ($existing) {
                $existing->update($attributes);
                $keepIds[] = $existing->id;
            } else {
                $room = $hotel->roomTypes()->create($attributes);
                $keepIds[] = $room->id;
            }
        }

        $removed = $hotel->roomTypes()->whereNotIn('id', $keepIds)->get();
        foreach ($removed as $room) {
            if ($room->images) {
                Storage::disk('public')->delete($room->images);
            }
            $room->delete();
        }
    }

    private function deleteImages(Hotel $hotel): void
    {
        if ($hotel->cover_image) {
            Storage::disk('public')->delete($hotel->cover_image);
        }
        if ($hotel->gallery_images) {
            Storage::disk('public')->delete($hotel->gallery_images);
        }
    }
}
