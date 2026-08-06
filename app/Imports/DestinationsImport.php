<?php

namespace App\Imports;

use App\Imports\Concerns\CollectsRowErrors;
use App\Imports\Concerns\ResolvesImportValues;
use App\Models\Destination;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DestinationsImport implements ToCollection, WithChunkReading, WithHeadingRow
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
                'local' => 'nullable|in:domestic,international',
                'country' => 'required|string|max:255',
                'city' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'meta' => 'nullable|string|max:1000',
                'status' => 'nullable|in:active,inactive',
                'sort_order' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                $this->addRowError($excelRow, $validator->errors()->all());
                continue;
            }

            try {
                DB::transaction(function () use ($data) {
                    $slugSeed = trim((string) ($data['slug'] ?? '')) !== '' ? $data['slug'] : $data['name'];

                    Destination::create([
                        'name' => trim($data['name']),
                        'slug' => $this->generateUniqueSlug(Destination::class, $slugSeed),
                        'local' => strtolower(trim((string) ($data['local'] ?? ''))) ?: 'domestic',
                        'country' => trim($data['country']),
                        'city' => $this->nullableString($data['city'] ?? null),
                        'description' => $this->nullableString($data['description'] ?? null),
                        'meta' => $this->nullableString($data['meta'] ?? null),
                        'cover_image' => $this->downloadImage($data['cover_image_url'] ?? null, 'destinations'),
                        'status' => strtolower(trim((string) ($data['status'] ?? ''))) ?: 'active',
                        'sort_order' => (int) ($data['sort_order'] ?? 0),
                        'featured' => $this->toBoolean($data['featured'] ?? null),
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
