<?php

namespace App\Exports\Destinations;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class InstructionsSheet implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return ['Column', 'Required?', 'Format / Notes'];
    }

    public function array(): array
    {
        return [
            ['name', 'Required', 'Plain text.'],
            ['slug', 'Optional', 'Leave blank to auto-generate from name. A duplicate slug gets -2, -3, ... appended automatically.'],
            ['local', 'Required', 'Exactly "domestic" or "international". Defaults to "domestic" if left blank.'],
            ['country', 'Required', 'Plain text.'],
            ['city', 'Optional', 'Plain text.'],
            ['description', 'Optional', 'Plain text (rich formatting is not imported — add it later from the edit screen if needed).'],
            ['meta', 'Optional', 'SEO meta description, max 1000 characters.'],
            ['cover_image_url', 'Optional', 'A direct URL to an image. If it can\'t be downloaded, the row still imports with no cover image.'],
            ['featured', 'Optional', '"yes" or "no". Defaults to "no".'],
            ['status', 'Required', 'Exactly "active" or "inactive". Defaults to "active" if left blank.'],
            ['sort_order', 'Optional', 'Whole number. Defaults to 0.'],
        ];
    }

    public function title(): string
    {
        return 'Instructions';
    }
}
