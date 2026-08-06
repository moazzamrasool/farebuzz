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

// The "FAQs" sheet of the Holiday Packages bulk upload.
class FaqsSheetImport implements ToCollection, WithChunkReading, WithHeadingRow
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
            $label = "FAQs #{$excelRow}";
            $data = $row->toArray();

            $validator = Validator::make($data, [
                'package_title' => 'required|string|max:255',
                'question' => 'required|string|max:255',
                'answer' => 'nullable|string',
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

            try {
                DB::transaction(function () use ($package, $data) {
                    $sortOrder = trim((string) ($data['sort_order'] ?? '')) !== ''
                        ? (int) $data['sort_order']
                        : ($this->sortCounters[$package->id] ??= 0);

                    $package->faqs()->create([
                        'question' => trim($data['question']),
                        'answer' => trim((string) ($data['answer'] ?? '')) !== '' ? trim($data['answer']) : null,
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
