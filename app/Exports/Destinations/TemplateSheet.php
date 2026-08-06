<?php

namespace App\Exports\Destinations;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TemplateSheet implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return [
            'name', 'slug', 'local', 'country', 'city', 'description', 'meta',
            'cover_image_url', 'featured', 'status', 'sort_order',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Goa', '', 'domestic', 'India', 'Goa',
                'Sun-kissed beaches, vibrant nightlife and Portuguese heritage.',
                'Best Goa holiday packages — beaches, nightlife & heritage tours.',
                '', 'yes', 'active', 1,
            ],
            [
                'Maldives', 'maldives', 'international', 'Maldives', 'Male',
                'Overwater villas and crystal-clear lagoons.',
                '', '', 'no', 'active', 2,
            ],
        ];
    }

    public function title(): string
    {
        return 'Template';
    }
}
