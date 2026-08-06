<?php

namespace App\Imports\HolidayPackages;

use App\Imports\Concerns\CollectsRowErrors;
use App\Imports\Concerns\ResolvesImportValues;
use App\Models\Activity;
use App\Models\HolidayPackage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

// The "Day Activities" sheet — same reasoning as DayHotelsSheetImport: writes
// straight to the package_activity pivot with a hand-stamped unique_id, and
// allows the same activity to appear on multiple days.
class DayActivitiesSheetImport implements ToCollection, WithChunkReading, WithHeadingRow
{
    use CollectsRowErrors, ResolvesImportValues;

    private array $sortCounters = [];

    public function chunkSize(): int
    {
        return 200;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $excelRow = $index + 2;
            $label = "Day Activities #{$excelRow}";
            $data = $row->toArray();

            $validator = Validator::make($data, [
                'package_title' => 'required|string|max:255',
                'day_number' => 'nullable|integer|min:1',
                'activity_name' => 'required|string|max:255',
                'price' => 'nullable|numeric|min:0',
                'is_optional' => 'nullable|string',
                'note' => 'nullable|string|max:255',
                'sort_order' => 'nullable|numeric',
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

            $activity = $this->findByName(Activity::class, $data['activity_name']);
            if (!$activity) {
                $rowErrors[] = "Activity '{$data['activity_name']}' not found in the Activities master.";
            }

            if (!empty($rowErrors)) {
                $this->addRowError($label, $rowErrors);
                continue;
            }

            try {
                DB::transaction(function () use ($package, $activity, $data) {
                    $sortOrder = trim((string) ($data['sort_order'] ?? '')) !== ''
                        ? (int) $data['sort_order']
                        : ($this->sortCounters[$package->id] ??= 0);

                    DB::table('package_activity')->insert([
                        'unique_id' => $package->unique_id,
                        'holiday_package_id' => $package->id,
                        'activity_id' => $activity->id,
                        'day_number' => trim((string) ($data['day_number'] ?? '')) !== '' ? (int) $data['day_number'] : null,
                        'price' => trim((string) ($data['price'] ?? '')) !== '' ? (float) $data['price'] : null,
                        'is_optional' => $this->toBoolean($data['is_optional'] ?? null),
                        'note' => trim((string) ($data['note'] ?? '')) !== '' ? trim($data['note']) : null,
                        'sort_order' => $sortOrder,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $this->sortCounters[$package->id] = $sortOrder + 1;
                });

                $this->markImported();
            } catch (\Throwable $e) {
                $this->addRowError($label, 'Unexpected error: '.$e->getMessage());
            }
        }
    }
}
