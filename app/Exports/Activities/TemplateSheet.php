<?php

namespace App\Exports\Activities;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TemplateSheet implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return [
            'name', 'slug', 'destination', 'travel_category', 'category',
            'description', 'price', 'image_url', 'status', 'sort_order',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Scuba Diving at Grand Island', '', 'Goa', 'Adventure', 'Water Sports',
                'Explore vibrant coral reefs with a certified instructor.', 3500, '', 'active', 1,
            ],
            [
                'Sunset Cruise', '', 'Goa', '', '',
                'A relaxed evening cruise along the coast.', 1200, '', 'active', 2,
            ],
        ];
    }

    public function title(): string
    {
        return 'Template';
    }
}
