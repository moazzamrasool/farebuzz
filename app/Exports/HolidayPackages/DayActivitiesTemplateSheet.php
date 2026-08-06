<?php

namespace App\Exports\HolidayPackages;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class DayActivitiesTemplateSheet implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return ['package_title', 'day_number', 'activity_name', 'price', 'is_optional', 'note', 'sort_order'];
    }

    public function array(): array
    {
        return [
            ['Magical Goa Getaway', 2, 'Scuba Diving', 2500, 'yes', 'Weather permitting', 0],
            ['Maldives Overwater Escape', 2, 'Snorkeling', '', 'no', '', 0],
        ];
    }

    public function title(): string
    {
        return 'Day Activities';
    }
}
