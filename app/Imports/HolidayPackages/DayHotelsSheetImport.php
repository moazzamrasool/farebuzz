<?php

namespace App\Imports\HolidayPackages;

use App\Imports\Concerns\CollectsRowErrors;
use App\Imports\Concerns\ResolvesImportValues;
use App\Models\HolidayPackage;
use App\Models\Hotel;
use App\Models\HotelRoomType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

// The "Day Hotels" sheet — attaches a hotel to a specific day (or the whole
// trip, if day_number is left blank) of an already-imported package. Writes
// straight to the holiday_package_hotel pivot table with a hand-stamped
// unique_id rather than going through HolidayPackage::hotels()->attach()/
// sync(), because: (a) BelongsToTenant's creating hook never fires for pivot
// rows written by Eloquent's pivot helpers, exactly like
// HolidayPackageController::syncHotels() already has to work around; and
// (b) sync() is keyed by hotel_id and can only hold one row per hotel per
// package, whereas this sheet intentionally allows the same hotel to appear
// on several different days.
class DayHotelsSheetImport implements ToCollection, WithChunkReading, WithHeadingRow
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
            $label = "Day Hotels #{$excelRow}";
            $data = $row->toArray();

            $validator = Validator::make($data, [
                'package_title' => 'required|string|max:255',
                'day_number' => 'nullable|integer|min:1',
                'hotel_name' => 'required|string|max:255',
                'room_type_name' => 'nullable|string|max:255',
                'price' => 'nullable|numeric|min:0',
                'is_optional' => 'nullable|string',
                'nights' => 'nullable|integer|min:1',
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

            $hotel = $this->findByName(Hotel::class, $data['hotel_name']);
            if (!$hotel) {
                $rowErrors[] = "Hotel '{$data['hotel_name']}' not found in the Hotels master.";
            }

            $roomType = null;
            if ($hotel && trim((string) ($data['room_type_name'] ?? '')) !== '') {
                $roomType = HotelRoomType::where('hotel_id', $hotel->id)
                    ->whereRaw('LOWER(name) = ?', [strtolower(trim($data['room_type_name']))])
                    ->first();
                if (!$roomType) {
                    $rowErrors[] = "Room type '{$data['room_type_name']}' not found on hotel '{$data['hotel_name']}'.";
                }
            }

            if (!empty($rowErrors)) {
                $this->addRowError($label, $rowErrors);
                continue;
            }

            try {
                DB::transaction(function () use ($package, $hotel, $roomType, $data) {
                    $sortOrder = trim((string) ($data['sort_order'] ?? '')) !== ''
                        ? (int) $data['sort_order']
                        : ($this->sortCounters[$package->id] ??= 0);

                    DB::table('holiday_package_hotel')->insert([
                        'unique_id' => $package->unique_id,
                        'holiday_package_id' => $package->id,
                        'hotel_id' => $hotel->id,
                        'day_number' => trim((string) ($data['day_number'] ?? '')) !== '' ? (int) $data['day_number'] : null,
                        'room_type_id' => $roomType?->id,
                        'price' => trim((string) ($data['price'] ?? '')) !== '' ? (float) $data['price'] : null,
                        'is_optional' => $this->toBoolean($data['is_optional'] ?? null),
                        'nights' => trim((string) ($data['nights'] ?? '')) !== '' ? (int) $data['nights'] : 1,
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
