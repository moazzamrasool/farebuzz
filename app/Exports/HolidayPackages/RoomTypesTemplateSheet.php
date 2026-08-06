<?php

namespace App\Exports\HolidayPackages;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RoomTypesTemplateSheet implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return ['package_title', 'room_name', 'price', 'discounted_price', 'sort_order'];
    }

    public function array(): array
    {
        return [
            ['Magical Goa Getaway', 'Standard Room', 24999, 19999, 0],
            ['Magical Goa Getaway', 'Deluxe Sea View', 28999, 23999, 1],
            ['Maldives Overwater Escape', 'Overwater Villa', 89999, '', 0],
        ];
    }

    public function title(): string
    {
        return 'Room Types';
    }
}
