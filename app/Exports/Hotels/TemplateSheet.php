<?php

namespace App\Exports\Hotels;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TemplateSheet implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return [
            'name', 'slug', 'destination', 'star_rating', 'address', 'latitude', 'longitude',
            'check_in_time', 'check_out_time', 'property_rules', 'description', 'rating_score',
            'amenities', 'room_types', 'cover_image_url', 'status', 'sort_order',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Taj Exotica Resort & Spa', '', 'Goa', 5, 'Benaulim Beach Road, Goa', 15.2144, 73.9161,
                '14:00', '11:00', 'No smoking in rooms. Pets not allowed.',
                'A beachfront luxury resort with private villas and lagoon pools.', 4.5,
                'Free Wifi, Swimming Pool, Spa', 'Deluxe Room:8000:6500|Sea View Villa:15000:12500',
                '', 'active', 1,
            ],
            [
                'Ocean Pearl Retreat', '', 'Goa', 4, 'Calangute Beach, Goa', '', '',
                '13:00', '10:00', '',
                'A comfortable mid-range stay close to the beach.', '',
                'Free Wifi', 'Standard Room:4000:',
                '', 'active', 2,
            ],
        ];
    }

    public function title(): string
    {
        return 'Template';
    }
}
