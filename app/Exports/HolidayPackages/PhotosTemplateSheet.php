<?php

namespace App\Exports\HolidayPackages;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PhotosTemplateSheet implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return ['package_title', 'image_url', 'is_cover', 'sort_order'];
    }

    public function array(): array
    {
        return [
            ['Magical Goa Getaway', 'https://picsum.photos/id/1043/1200/800', 'yes', 0],
            ['Magical Goa Getaway', 'https://picsum.photos/id/1044/1200/800', 'no', 1],
        ];
    }

    public function title(): string
    {
        return 'Photos';
    }
}
