<?php

namespace App\Exports\HolidayPackages;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class InclusionsExclusionsTemplateSheet implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return ['package_title', 'type', 'item_title', 'sort_order'];
    }

    public function array(): array
    {
        return [
            ['Magical Goa Getaway', 'inclusion', 'Daily breakfast at the hotel', 0],
            ['Magical Goa Getaway', 'inclusion', 'Airport lounge access on arrival', 1],
            ['Magical Goa Getaway', 'exclusion', 'Water sports & adventure activities', 0],
        ];
    }

    public function title(): string
    {
        return 'Inclusions & Exclusions';
    }
}
