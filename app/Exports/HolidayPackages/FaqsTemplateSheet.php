<?php

namespace App\Exports\HolidayPackages;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class FaqsTemplateSheet implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return ['package_title', 'question', 'answer', 'sort_order'];
    }

    public function array(): array
    {
        return [
            ['Magical Goa Getaway', 'Is airport transfer included?', 'Yes, airport pickup and drop-off are included in the package.', 0],
            ['Magical Goa Getaway', 'Can I customize the itinerary?', 'Yes, contact our travel team after booking to customize your itinerary.', 1],
        ];
    }

    public function title(): string
    {
        return 'FAQs';
    }
}
