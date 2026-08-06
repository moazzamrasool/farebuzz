<?php

namespace App\Imports\HolidayPackages;

use App\Imports\Concerns\CollectsRowErrors;
use App\Models\HolidayPackage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

// The "Itinerary" sheet of the Holiday Packages bulk upload. Relies on the
// "Package Core" sheet (PackageCoreSheetImport) having already run in the
// same import — WithMultipleSheets processes sheets sequentially in the
// order returned by HolidayPackagesImport::sheets(), so every package_title
// here can already be looked up by a plain tenant-scoped query.
class PackageItinerarySheetImport implements ToCollection, WithChunkReading, WithHeadingRow
{
    use CollectsRowErrors;

    private const VALID_MEAL_TAGS = ['breakfast', 'lunch', 'dinner'];

    public function chunkSize(): int
    {
        return 200;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $excelRow = $index + 2;
            $label = "Itinerary #{$excelRow}";
            $data = $row->toArray();

            $validator = Validator::make($data, [
                'package_title' => 'required|string|max:255',
                'day_number' => 'required|integer|min:1',
                'day_title' => 'required|string|max:255',
                'route_summary' => 'nullable|string|max:255',
                'detail' => 'nullable|string',
                'bullet_points' => 'nullable|string',
                'meal_tags' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $this->addRowError($label, $validator->errors()->all());
                continue;
            }

            $rowErrors = [];

            $package = HolidayPackage::whereRaw('LOWER(title) = ?', [strtolower(trim($data['package_title']))])->first();
            if (!$package) {
                $rowErrors[] = "Holiday package '{$data['package_title']}' not found — check it matches a title on the Package Core sheet exactly.";
            }

            $mealTags = collect(explode(',', (string) ($data['meal_tags'] ?? '')))
                ->map(fn ($tag) => strtolower(trim($tag)))
                ->filter(fn ($tag) => $tag !== '')
                ->values();

            $invalidTags = $mealTags->diff(self::VALID_MEAL_TAGS);
            if ($invalidTags->isNotEmpty()) {
                $rowErrors[] = 'Invalid meal_tags: '.$invalidTags->implode(', ').' (valid values: '.implode(', ', self::VALID_MEAL_TAGS).').';
            }

            if (!empty($rowErrors)) {
                $this->addRowError($label, $rowErrors);
                continue;
            }

            $bulletPoints = collect(explode(';', (string) ($data['bullet_points'] ?? '')))
                ->map(fn ($point) => trim($point))
                ->filter(fn ($point) => $point !== '')
                ->values()
                ->all();

            try {
                DB::transaction(function () use ($package, $data, $bulletPoints, $mealTags) {
                    $dayNumber = (int) $data['day_number'];

                    $package->itineraries()->create([
                        'day_number' => $dayNumber,
                        'title' => trim($data['day_title']),
                        'route_summary' => $this->nullableString($data['route_summary'] ?? null),
                        'detail' => $this->nullableString($data['detail'] ?? null),
                        'bullet_points' => $bulletPoints,
                        'meal_tags' => $mealTags->values()->all(),
                        'sort_order' => $dayNumber - 1,
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
