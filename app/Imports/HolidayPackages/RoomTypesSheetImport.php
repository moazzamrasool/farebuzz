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

// The "Room Types" sheet of the Holiday Packages bulk upload. Optional — a
// package with no rows here simply keeps the single auto-generated "Standard"
// tier created by PackageCoreSheetImport. The first row seen for a given
// package replaces that auto tier wholesale (mirroring how the manual edit
// screen's room-types section always replaces the full set on save), so the
// auto "Standard" tier and sheet-provided tiers never coexist.
class RoomTypesSheetImport implements ToCollection, WithChunkReading, WithHeadingRow
{
    use CollectsRowErrors;

    private array $clearedPackageIds = [];

    private array $sortCounters = [];

    public function chunkSize(): int
    {
        return 200;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $excelRow = $index + 2;
            $label = "Room Types #{$excelRow}";
            $data = $row->toArray();

            $validator = Validator::make($data, [
                'package_title' => 'required|string|max:255',
                'room_name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'discounted_price' => 'nullable|numeric|min:0',
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

            $price = (float) $data['price'];
            $discountedPrice = trim((string) ($data['discounted_price'] ?? '')) !== '' ? (float) $data['discounted_price'] : null;
            if ($discountedPrice !== null && $discountedPrice >= $price) {
                $this->addRowError($label, 'Discounted price must be less than price.');
                continue;
            }

            try {
                DB::transaction(function () use ($package, $data, $price, $discountedPrice) {
                    if (!in_array($package->id, $this->clearedPackageIds, true)) {
                        $package->roomTypes()->delete();
                        $this->clearedPackageIds[] = $package->id;
                    }

                    $sortOrder = trim((string) ($data['sort_order'] ?? '')) !== ''
                        ? (int) $data['sort_order']
                        : ($this->sortCounters[$package->id] ??= 0);

                    $package->roomTypes()->create([
                        'name' => trim($data['room_name']),
                        'price' => $price,
                        'discounted_price' => $discountedPrice,
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
