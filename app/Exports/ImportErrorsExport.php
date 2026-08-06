<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ImportErrorsExport implements FromArray, WithHeadings
{
    public function __construct(private readonly array $rowErrors)
    {
    }

    public function array(): array
    {
        return collect($this->rowErrors)
            ->map(fn (array $entry) => [$entry['row'], implode('; ', $entry['errors'])])
            ->all();
    }

    public function headings(): array
    {
        return ['Row', 'Reason'];
    }
}
