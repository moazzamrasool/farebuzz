<?php

namespace App\Exports\HolidayPackages;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ItineraryTemplateSheet implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return ['package_title', 'day_number', 'day_title', 'route_summary', 'detail', 'bullet_points', 'meal_tags', 'image_urls'];
    }

    public function array(): array
    {
        return [
            [
                'Magical Goa Getaway', 1, 'Arrival in Goa', 'Airport to Baga Beach',
                'Arrive in Goa, transfer to your hotel and spend the evening relaxing on Baga Beach.',
                'Airport pickup included;Welcome drink on arrival;Evening free for leisure',
                'dinner',
                'https://example.com/goa-arrival-1.jpg,https://example.com/goa-arrival-2.jpg',
            ],
            [
                'Magical Goa Getaway', 2, 'Old Goa Heritage Tour', 'Baga to Old Goa',
                'Visit the UNESCO-listed churches of Old Goa followed by a spice plantation tour.',
                'Guided church tour;Spice plantation lunch included',
                'breakfast,lunch',
                '',
            ],
        ];
    }

    public function title(): string
    {
        return 'Itinerary';
    }
}
