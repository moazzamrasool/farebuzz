<?php

namespace App\Exports\Activities;

use App\Models\ActivityCategory;
use App\Models\Destination;
use App\Models\TravelCategory;
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
            ['travel_category', 'Optional', 'Must exactly match one of your existing travel category names below, or leave blank.'],
            ['category', 'Optional', 'Must exactly match one of your existing activity category names below, or leave blank. Categories are not created automatically — add missing ones under Master Data > Activity Categories first.'],
            ['description', 'Optional', 'Plain text.'],
            ['price', 'Optional', 'Number, e.g. 3500 or 3500.00.'],
            ['image_url', 'Optional', 'A direct URL to an image. If it can\'t be downloaded, the row still imports with no image.'],
            ['status', 'Required', 'Exactly "active" or "inactive".'],
            ['sort_order', 'Optional', 'Whole number. Defaults to 0.'],
            ['', '', ''],
            ['Your existing destination names:', '', ''],
        ];

        foreach (Destination::orderBy('name')->pluck('name') as $name) {
            $rows[] = [$name, '', ''];
        }

        $rows[] = ['', '', ''];
        $rows[] = ['Your existing travel category names:', '', ''];

        foreach (TravelCategory::orderBy('name')->pluck('name') as $name) {
            $rows[] = [$name, '', ''];
        }

        $rows[] = ['', '', ''];
        $rows[] = ['Your existing activity category names:', '', ''];

        foreach (ActivityCategory::orderBy('name')->pluck('name') as $name) {
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
