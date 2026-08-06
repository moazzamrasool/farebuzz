<?php

namespace App\Imports\HolidayPackages;

use App\Models\HolidayPackage;
use App\Imports\Concerns\CollectsRowErrors;
use App\Models\PackageFeature;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

// The "Inclusions & Exclusions" sheet. If item_title matches an existing
// PackageFeature of the same type, the package is linked to that curated
// master row (pivot write, unique_id stamped by hand — same reasoning as
// DayHotelsSheetImport); otherwise a free-text HolidayPackageCustomFeature
// line is created, mirroring what the manual edit screen calls "custom
// inclusions/exclusions".
class InclusionsExclusionsSheetImport implements ToCollection, WithChunkReading, WithHeadingRow
{
    use CollectsRowErrors;

    private array $sortCounters = [];

    public function chunkSize(): int
    {
        return 200;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $excelRow = $index + 2;
            $label = "Inclusions & Exclusions #{$excelRow}";
            $data = $row->toArray();

            $validator = Validator::make($data, [
                'package_title' => 'required|string|max:255',
                'type' => 'required|in:inclusion,exclusion',
                'item_title' => 'required|string|max:255',
                'sort_order' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                $this->addRowError($label, $validator->errors()->all());
                continue;
            }

            $package = HolidayPackage::whereRaw('LOWER(title) = ?', [strtolower(trim($data['package_title']))])->first();
            if (!$package) {
                $this->addRowError($label, "Holiday package '{$data['package_title']}' not found — check it matches a title on the Package Core sheet exactly.");
                continue;
            }

            $type = strtolower(trim($data['type']));
            $title = trim($data['item_title']);

            $feature = PackageFeature::where('type', $type)
                ->whereRaw('LOWER(title) = ?', [strtolower($title)])
                ->first();

            try {
                DB::transaction(function () use ($package, $feature, $type, $title, $data) {
                    $sortOrder = trim((string) ($data['sort_order'] ?? '')) !== ''
                        ? (int) $data['sort_order']
                        : ($this->sortCounters[$package->id] ??= 0);

                    if ($feature) {
                        $alreadyLinked = DB::table('holiday_package_feature')
                            ->where('holiday_package_id', $package->id)
                            ->where('package_feature_id', $feature->id)
                            ->exists();

                        if (!$alreadyLinked) {
                            DB::table('holiday_package_feature')->insert([
                                'unique_id' => $package->unique_id,
                                'holiday_package_id' => $package->id,
                                'package_feature_id' => $feature->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    } else {
                        $package->customFeatures()->create([
                            'type' => $type,
                            'title' => $title,
                            'sort_order' => $sortOrder,
                        ]);
                    }

                    $this->sortCounters[$package->id] = $sortOrder + 1;
                });

                $this->markImported();
            } catch (\Throwable $e) {
                $this->addRowError($label, 'Unexpected error: '.$e->getMessage());
            }
        }
    }
}
