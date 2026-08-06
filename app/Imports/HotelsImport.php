<?php

namespace App\Imports;

use App\Imports\Concerns\CollectsRowErrors;
use App\Imports\Concerns\ResolvesImportValues;
use App\Models\Amenity;
use App\Models\Destination;
use App\Models\Hotel;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HotelsImport implements ToCollection, WithChunkReading, WithHeadingRow
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
            $data = $row->toArray();

            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255',
                'destination' => 'nullable|string|max:255',
                'star_rating' => 'nullable|numeric|min:0|max:5',
                'address' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'check_in_time' => 'nullable|string|max:10',
                'check_out_time' => 'nullable|string|max:10',
                'property_rules' => 'nullable|string',
                'description' => 'nullable|string',
                'rating_score' => 'nullable|numeric|min:0|max:10',
                'status' => 'nullable|in:active,inactive',
                'sort_order' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                $this->addRowError($excelRow, $validator->errors()->all());
                continue;
            }

            $rowErrors = [];

            $destination = null;
            if (trim((string) ($data['destination'] ?? '')) !== '') {
                $destination = $this->findByName(Destination::class, $data['destination']);
                if (!$destination) {
                    $rowErrors[] = "Destination '{$data['destination']}' not found.";
                }
            }

            $amenities = [];
            if (trim((string) ($data['amenities'] ?? '')) !== '') {
                $resolved = $this->findManyByName(Amenity::class, $data['amenities']);
                $amenities = $resolved['models'];
                if (!empty($resolved['missing'])) {
                    $rowErrors[] = 'Amenities not found: '.implode(', ', $resolved['missing']);
                }
            }

            $roomTypes = [];
            if (trim((string) ($data['room_types'] ?? '')) !== '') {
                [$roomTypes, $parseErrors] = $this->parseRoomTypes($data['room_types']);
                $rowErrors = array_merge($rowErrors, $parseErrors);
            }

            if (!empty($rowErrors)) {
                $this->addRowError($excelRow, $rowErrors);
                continue;
            }

            try {
                DB::transaction(function () use ($data, $destination, $amenities, $roomTypes) {
                    $slugSeed = trim((string) ($data['slug'] ?? '')) !== '' ? $data['slug'] : $data['name'];

                    $hotel = Hotel::create([
                        'destination_id' => $destination?->id,
                        'name' => trim($data['name']),
                        'slug' => $this->generateUniqueSlug(Hotel::class, $slugSeed),
                        'star_rating' => (int) ($data['star_rating'] ?? 0),
                        'address' => $this->nullableString($data['address'] ?? null),
                        'latitude' => $this->nullableString($data['latitude'] ?? null),
                        'longitude' => $this->nullableString($data['longitude'] ?? null),
                        'check_in_time' => $this->nullableString($data['check_in_time'] ?? null),
                        'check_out_time' => $this->nullableString($data['check_out_time'] ?? null),
                        'property_rules' => $this->nullableString($data['property_rules'] ?? null),
                        'description' => $this->nullableString($data['description'] ?? null),
                        'rating_score' => $data['rating_score'] !== null && $data['rating_score'] !== '' ? $data['rating_score'] : null,
                        'cover_image' => $this->downloadImage($data['cover_image_url'] ?? null, 'hotels'),
                        'status' => strtolower(trim((string) ($data['status'] ?? ''))) ?: 'active',
                        'sort_order' => (int) ($data['sort_order'] ?? 0),
                    ]);

                    $hotel->amenities()->sync(collect($amenities)->pluck('id')->all());

                    foreach ($roomTypes as $sortOrder => $roomType) {
                        $hotel->roomTypes()->create([
                            'name' => $roomType['name'],
                            'price' => $roomType['price'],
                            'discounted_price' => $roomType['discounted_price'],
                            'sort_order' => $sortOrder,
                        ]);
                    }
                });

                $this->markImported();
            } catch (\Throwable $e) {
                $this->addRowError($excelRow, 'Unexpected error: '.$e->getMessage());
            }
        }
    }

    /**
     * Parses "RoomName:Price:DiscountedPrice|RoomName2:Price2:DiscountedPrice2"
     * (DiscountedPrice segment is optional). Returns [rows, errors].
     *
     * @return array{0: array<int, array{name: string, price: float, discounted_price: ?float}>, 1: array<int, string>}
     */
    private function parseRoomTypes(string $raw): array
    {
        $rows = [];
        $errors = [];

        foreach (explode('|', $raw) as $segment) {
            $segment = trim($segment);
            if ($segment === '') {
                continue;
            }

            $parts = explode(':', $segment);
            $name = trim($parts[0] ?? '');
            $price = trim($parts[1] ?? '');
            $discounted = trim($parts[2] ?? '');

            if ($name === '' || $price === '' || !is_numeric($price)) {
                $errors[] = "Malformed room type \"{$segment}\" (expected Name:Price or Name:Price:DiscountedPrice).";
                continue;
            }

            if ($discounted !== '' && !is_numeric($discounted)) {
                $errors[] = "Malformed room type \"{$segment}\" — discounted price is not numeric.";
                continue;
            }

            $rows[] = [
                'name' => $name,
                'price' => (float) $price,
                'discounted_price' => $discounted !== '' ? (float) $discounted : null,
            ];
        }

        return [$rows, $errors];
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
