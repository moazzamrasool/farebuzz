<?php

namespace App\Imports\HolidayPackages;

use App\Imports\Concerns\CollectsRowErrors;
use App\Imports\Concerns\ResolvesImportValues;
use App\Models\Destination;
use App\Models\HolidayPackage;
use App\Models\TravelCategory;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

// The "Package Core" sheet of the Holiday Packages bulk upload — creates the
// package itself, its category badges, and one default "Standard" pricing
// tier (package_room_types requires at least one row for the app's own price
// display, mirroring what the normal create form enforces). Per-day content
// lives in the sibling Itinerary sheet (PackageItinerarySheetImport), which
// runs after this one finishes so it can look packages up by title.
class PackageCoreSheetImport implements ToCollection, WithChunkReading, WithHeadingRow
{
    use CollectsRowErrors, GeneratesUniqueSlug, ResolvesImportValues;

    private array $createdPackageIds = [];

    public function chunkSize(): int
    {
        return 200;
    }

    // IDs created by this sheet — the controller uses these to recompute
    // nights/days/hotel_category/meals once the Itinerary/Day Hotels sheets
    // (which run after this one) have finished attaching their rows.
    public function createdPackageIds(): array
    {
        return $this->createdPackageIds;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $excelRow = $index + 2;
            $label = "Package Core #{$excelRow}";
            $data = $row->toArray();

            $validator = Validator::make($data, [
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255',
                'destination' => 'required|string|max:255',
                'categories' => 'nullable|string',
                'hotel_category' => 'nullable|string|max:255',
                'hotel_category_override' => 'nullable|string',
                'meals' => 'nullable|string|max:255',
                'meals_override' => 'nullable|string',
                'language' => 'nullable|string|max:255',
                'places_to_visit' => 'nullable|string|max:255',
                'overview' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'discounted_price' => 'nullable|numeric|min:0',
                'booking_type' => 'nullable|in:enquiry_only,book_enquiry',
                'status' => 'nullable|in:active,inactive',
                'sort_order' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                $this->addRowError($label, $validator->errors()->all());
                continue;
            }

            $rowErrors = [];

            // Nights/Days, and Hotel Category/Meals when not overridden, are recalculated
            // from the Itinerary/Day Hotels sheets after the whole workbook finishes
            // importing (see HolidayPackageController::bulkUpload()) — this row's values
            // are only a placeholder to satisfy the NOT NULL nights/days columns until then.
            $hotelCategoryOverridden = $this->toBoolean($data['hotel_category_override'] ?? null);
            $mealsOverridden = $this->toBoolean($data['meals_override'] ?? null);

            $price = (float) $data['price'];
            $discountedPrice = trim((string) ($data['discounted_price'] ?? '')) !== '' ? (float) $data['discounted_price'] : null;
            if ($discountedPrice !== null && $discountedPrice >= $price) {
                $rowErrors[] = 'Discounted price must be less than price.';
            }

            $destination = $this->findByName(Destination::class, $data['destination']);
            if (!$destination) {
                $rowErrors[] = "Destination '{$data['destination']}' not found.";
            }

            $categoryModels = [];
            if (trim((string) ($data['categories'] ?? '')) !== '') {
                $resolved = $this->findManyByName(TravelCategory::class, $data['categories']);
                $categoryModels = $resolved['models'];
                if (!empty($resolved['missing'])) {
                    $rowErrors[] = 'Categories not found: '.implode(', ', $resolved['missing']);
                }
            }

            if (!empty($rowErrors)) {
                $this->addRowError($label, $rowErrors);
                continue;
            }

            try {
                DB::transaction(function () use ($data, $destination, $categoryModels, $price, $discountedPrice, $hotelCategoryOverridden, $mealsOverridden) {
                    $slugSeed = trim((string) ($data['slug'] ?? '')) !== '' ? $data['slug'] : $data['title'];

                    $package = HolidayPackage::create([
                        'destination_id' => $destination->id,
                        'title' => trim($data['title']),
                        'slug' => $this->generateUniqueSlug(HolidayPackage::class, $slugSeed),
                        'nights' => 0,
                        'days' => 0,
                        'hotel_category' => $hotelCategoryOverridden ? $this->nullableString($data['hotel_category'] ?? null) : null,
                        'hotel_category_overridden' => $hotelCategoryOverridden,
                        'meals' => $mealsOverridden ? $this->nullableString($data['meals'] ?? null) : null,
                        'meals_overridden' => $mealsOverridden,
                        'language' => $this->nullableString($data['language'] ?? null),
                        'places_to_visit' => $this->nullableString($data['places_to_visit'] ?? null),
                        'overview' => $this->nullableString($data['overview'] ?? null),
                        'price' => $price,
                        'discounted_price' => $discountedPrice,
                        'booking_type' => strtolower(trim((string) ($data['booking_type'] ?? ''))) ?: 'enquiry_only',
                        'status' => strtolower(trim((string) ($data['status'] ?? ''))) ?: 'active',
                        'featured' => $this->toBoolean($data['featured'] ?? null),
                        'is_best_seller' => $this->toBoolean($data['best_seller'] ?? null),
                        'sort_order' => (int) ($data['sort_order'] ?? 0),
                    ]);

                    $package->categories()->sync(collect($categoryModels)->pluck('id')->all());

                    $package->roomTypes()->create([
                        'name' => 'Standard',
                        'price' => $price,
                        'discounted_price' => $discountedPrice,
                        'sort_order' => 0,
                    ]);

                    $this->createdPackageIds[] = $package->id;
                });

                $this->markImported();
            } catch (\Throwable $e) {
                $this->addRowError($label, 'Unexpected error: '.$e->getMessage());
            }
        }
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
