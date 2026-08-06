<?php

namespace App\Imports;

use App\Imports\Concerns\CollectsRowErrors;
use App\Imports\Concerns\ResolvesImportValues;
use App\Models\Activity;
use App\Models\Destination;
use App\Models\TravelCategory;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ActivitiesImport implements ToCollection, WithChunkReading, WithHeadingRow
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
                'travel_category' => 'nullable|string|max:255',
                'category' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
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

            $travelCategory = null;
            if (trim((string) ($data['travel_category'] ?? '')) !== '') {
                $travelCategory = $this->findByName(TravelCategory::class, $data['travel_category']);
                if (!$travelCategory) {
                    $rowErrors[] = "Travel category '{$data['travel_category']}' not found.";
                }
            }

            if (!empty($rowErrors)) {
                $this->addRowError($excelRow, $rowErrors);
                continue;
            }

            try {
                DB::transaction(function () use ($data, $destination, $travelCategory) {
                    $slugSeed = trim((string) ($data['slug'] ?? '')) !== '' ? $data['slug'] : $data['name'];

                    Activity::create([
                        'destination_id' => $destination?->id,
                        'travel_category_id' => $travelCategory?->id,
                        'name' => trim($data['name']),
                        'slug' => $this->generateUniqueSlug(Activity::class, $slugSeed),
                        'category' => $this->nullableString($data['category'] ?? null),
                        'description' => $this->nullableString($data['description'] ?? null),
                        'price' => $data['price'] !== null && $data['price'] !== '' ? $data['price'] : null,
                        'image' => $this->downloadImage($data['image_url'] ?? null, 'activities'),
                        'status' => strtolower(trim((string) ($data['status'] ?? ''))) ?: 'active',
                        'sort_order' => (int) ($data['sort_order'] ?? 0),
                    ]);
                });

                $this->markImported();
            } catch (\Throwable $e) {
                $this->addRowError($excelRow, 'Unexpected error: '.$e->getMessage());
            }
        }
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
