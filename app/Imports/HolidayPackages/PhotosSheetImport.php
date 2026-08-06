<?php

namespace App\Imports\HolidayPackages;

use App\Imports\Concerns\CollectsRowErrors;
use App\Imports\Concerns\ResolvesImportValues;
use App\Models\HolidayPackage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

// The "Photos" sheet — downloads each image_url via ResolvesImportValues::
// downloadImage() (best-effort, never throws) into the same
// holiday-packages/photos disk folder the manual edit screen's upload uses.
class PhotosSheetImport implements ToCollection, WithChunkReading, WithHeadingRow
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
            $label = "Photos #{$excelRow}";
            $data = $row->toArray();

            $validator = Validator::make($data, [
                'package_title' => 'required|string|max:255',
                'image_url' => 'required|string|max:2048',
                'is_cover' => 'nullable|string',
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

            $path = $this->downloadImage($data['image_url'], 'holiday-packages/photos');
            if (!$path) {
                $this->addRowError($label, "Could not download image from '{$data['image_url']}' — check the URL is a direct, publicly reachable image link.");
                continue;
            }

            try {
                DB::transaction(function () use ($package, $path, $data) {
                    $sortOrder = trim((string) ($data['sort_order'] ?? '')) !== ''
                        ? (int) $data['sort_order']
                        : ($this->sortCounters[$package->id] ??= 0);

                    $package->photos()->create([
                        'path' => $path,
                        'is_cover' => $this->toBoolean($data['is_cover'] ?? null),
                        'sort_order' => $sortOrder,
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
