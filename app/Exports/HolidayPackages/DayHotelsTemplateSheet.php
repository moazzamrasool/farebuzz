<?php

namespace App\Exports\HolidayPackages;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class DayHotelsTemplateSheet implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return [
            'package_title', 'day_number', 'hotel_name', 'room_type_name',
            'price', 'is_optional', 'nights', 'note', 'sort_order',
        ];
    }

    public function array(): array
    {
        return [
            ['Magical Goa Getaway', 1, 'Taj Vivanta Goa', 'Superior Room', '', 'no', 4, 'Check-in from 2 PM', 0],
            ['Maldives Overwater Escape', 1, 'Emerald Bay Resort & Spa', 'Overwater Villa', '', 'no', 3, '', 0],
        ];
    }

    public function title(): string
    {
        return 'Day Hotels';
    }
}
