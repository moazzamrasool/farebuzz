<?php

namespace Database\Seeders;

use App\Models\TravelCategory;
use Database\Seeders\Concerns\GeneratesDemoImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TravelCategorySeeder extends Seeder
{
    use GeneratesDemoImages;

    public function run(): void
    {
        $categories = [
            'Beaches'     => '#0d6efd',
            'Mountains'   => '#6c757d',
            'Honeymoon'   => '#db2777',
            'Adventure'   => '#f47b20',
            'Best Seller' => '#16a34a',
            'International' => '#dc2626',
            'MICE' => '#0891b2',
            'Family'      => '#0ea5e9',
            'Wildlife'    => '#166534',
            'Luxury'      => '#a16207',
            'Pilgrimage'  => '#9333ea',
        ];

        foreach (array_keys($categories) as $index => $name) {
            $category = TravelCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name'        => $name,
                    'description' => "Explore our curated {$name} experiences.",
                    'badge_color' => $categories[$name],
                    'status'      => 'active',
                    'sort_order'  => $index,
                ]
            );

            if (!$category->image) {
                $category->update([
                    'image' => $this->placeholderImage(300, 300, $name, 'travel-categories', $categories[$name]),
                ]);
            }
        }
    }
}
