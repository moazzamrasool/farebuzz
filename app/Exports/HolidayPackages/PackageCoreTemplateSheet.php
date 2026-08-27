<?php

namespace App\Exports\HolidayPackages;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PackageCoreTemplateSheet implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return [
            'title', 'slug', 'destination', 'categories', 'hotel_category', 'hotel_category_override',
            'meals', 'meals_override', 'language', 'places_to_visit', 'overview', 'price', 'discounted_price',
            'booking_type', 'best_seller', 'featured', 'status', 'sort_order',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Magical Goa Getaway', '', 'Goa', 'Beaches, Honeymoon', '', 'no',
                '', 'no', 'English', 'Baga Beach, Fort Aguada, Old Goa Churches',
                'A relaxed beach holiday combining sun, heritage and nightlife.',
                24999, 19999, 'enquiry_only', 'yes', 'yes', 'active', 1,
            ],
            [
                'Maldives Overwater Escape', '', 'Maldives', 'Honeymoon, International', '4 & 5 Star Hotels + Overwater Villa', 'yes',
                '', 'no', 'English', 'Male City Tour, Sandbank Picnic',
                'A luxury escape in an overwater villa with private lagoon access.',
                89999, '', 'enquiry_only', 'no', 'yes', 'active', 2,
            ],
        ];
    }

    public function title(): string
    {
        return 'Package Core';
    }
}
