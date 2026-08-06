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

    public function chunkSize(): int
    {
        return 200;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $excelRow = $index + 2;
            $label = "Package Core #{$excelRow}";
            $data = $row->toArray();

            $nights = (int) ($data['nights'] ?? 0);

            $validator = Validator::make($data, [
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255',
                'destination' => 'required|string|max:255',
                'categories' => 'nullable|string',
                'nights' => 'required|integer|min:0',
                'days' => 'nullable|integer|min:1',
                'hotel_category' => 'nullable|string|max:255',
                'meals' => 'nullable|string|max:255',
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

            $days = trim((string) ($data['days'] ?? '')) !== '' ? (int) $data['days'] : $nights + 1;
            if ($days !== $nights + 1) {
                $rowErrors[] = "Days must equal Nights + 1 ({$nights} + 1 = ".($nights + 1).').';
            }

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
                DB::transaction(function () use ($data, $destination, $categoryModels, $days, $nights, $price, $discountedPrice) {
                    $slugSeed = trim((string) ($data['slug'] ?? '')) !== '' ? $data['slug'] : $data['title'];

                    $package = HolidayPackage::create([
                        'destination_id' => $destination->id,
                        'title' => trim($data['title']),
                        'slug' => $this->generateUniqueSlug(HolidayPackage::class, $slugSeed),
                        'nights' => $nights,
                        'days' => $days,
                        'hotel_category' => $this->nullableString($data['hotel_category'] ?? null),
                        'meals' => $this->nullableString($data['meals'] ?? null),
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
