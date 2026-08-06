<?php

namespace App\Exports\Hotels;

use App\Models\Amenity;
use App\Models\Destination;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class InstructionsSheet implements FromArray, WithHeadings, WithTitle
{
    public function array(): array
    {
        $rows = [
            ['name', 'Required', 'Plain text.'],
            ['slug', 'Optional', 'Leave blank to auto-generate from name.'],
            ['destination', 'Optional', 'Must exactly match one of your existing destination names below, or leave blank.'],
            ['star_rating', 'Optional', 'Whole number, 0-5.'],
            ['address', 'Optional', 'Plain text.'],
            ['latitude / longitude', 'Optional', 'Decimal numbers.'],
            ['check_in_time / check_out_time', 'Optional', 'Free text, e.g. "14:00".'],
            ['property_rules', 'Optional', 'Plain text.'],
            ['description', 'Optional', 'Plain text.'],
            ['rating_score', 'Optional', 'Decimal number, 0-10.'],
            ['amenities', 'Optional', 'Comma-separated list of amenity names — every name must match one of your existing amenities below.'],
            ['room_types', 'Optional', 'Pipe-separated list of "Name:Price:DiscountedPrice", e.g. "Deluxe Room:8000:6500|Suite:15000:". DiscountedPrice may be left blank.'],
            ['cover_image_url', 'Optional', 'A direct URL to an image. If it can\'t be downloaded, the row still imports with no cover image.'],
            ['status', 'Required', 'Exactly "active" or "inactive".'],
            ['sort_order', 'Optional', 'Whole number. Defaults to 0.'],
            ['', '', ''],
            ['Your existing destination names:', '', ''],
        ];

        foreach (Destination::orderBy('name')->pluck('name') as $name) {
            $rows[] = [$name, '', ''];
        }

        $rows[] = ['', '', ''];
        $rows[] = ['Your existing amenity names:', '', ''];

        foreach (Amenity::orderBy('name')->pluck('name') as $name) {
            $rows[] = [$name, '', ''];
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['Column', 'Required?', 'Format / Notes'];
    }

    public function title(): string
    {
        return 'Instructions';
    }
}
