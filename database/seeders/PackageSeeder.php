<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\TravelCategory;
use Database\Seeders\Concerns\GeneratesDemoImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PackageSeeder extends Seeder
{
    use GeneratesDemoImages;

    public function run(): void
    {
        $rows = [
            ['name' => 'India Beach Packages',              'category' => 'beaches',   'type' => 'domestic',      'color' => '#0d6efd'],
            ['name' => 'Honeymoon Specials',                 'category' => 'honeymoon', 'type' => 'domestic',      'color' => '#db2777'],
            ['name' => 'International Adventure Packages',   'category' => 'adventure', 'type' => 'international', 'color' => '#f47b20'],
        ];

        foreach ($rows as $index => $row) {
            $category = TravelCategory::where('slug', $row['category'])->first();
            if (!$category) continue;

            $package = Package::firstOrCreate(
                ['slug' => Str::slug($row['name'])],
                [
                    'travel_category_id' => $category->id,
                    'name'        => $row['name'],
                    'type'        => $row['type'],
                    'description' => 'Curated '.$row['name'].' for every traveller.',
                    'status'      => 'active',
                    'sort_order'  => $index,
                ]
            );

            if (!$package->image) {
                $package->update(['image' => $this->placeholderImage(216, 270, $row['name'], 'packages', $row['color'])]);
            }
            if (!$package->banner_image) {
                $package->update(['banner_image' => $this->placeholderImage(1366, 396, $row['name'], 'packages/banners', $row['color'])]);
            }
        }
    }
}
