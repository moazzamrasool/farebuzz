<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            ['name' => 'Free WiFi',        'icon' => 'bi-wifi'],
            ['name' => 'Pool',             'icon' => 'bi-water'],
            ['name' => 'Parking',          'icon' => 'bi-p-circle'],
            ['name' => 'Restaurant',       'icon' => 'bi-cup-hot'],
            ['name' => 'AC',               'icon' => 'bi-snow2'],
            ['name' => 'Spa',              'icon' => 'bi-heart-pulse'],
            ['name' => 'Beach Access',     'icon' => 'bi-bicycle'],
            ['name' => 'Room Service',     'icon' => 'bi-bell'],
            ['name' => 'Gym',              'icon' => 'bi-heart'],
            ['name' => 'Bar',              'icon' => 'bi-cup-straw'],
            ['name' => 'Airport Transfer', 'icon' => 'bi-airplane'],
            ['name' => 'Power Backup',     'icon' => 'bi-battery-charging'],
            ['name' => 'Laundry',          'icon' => 'bi-basket2'],
            ['name' => '24hr Front Desk',  'icon' => 'bi-clock-history'],
            ['name' => 'Heater',           'icon' => 'bi-thermometer-sun'],
            ['name' => 'Lake/Mountain View', 'icon' => 'bi-triangle'],
        ];

        foreach ($amenities as $index => $amenity) {
            Amenity::firstOrCreate(
                ['name' => $amenity['name']],
                [
                    'icon'       => $amenity['icon'],
                    'status'     => 'active',
                    'sort_order' => $index,
                ]
            );
        }
    }
}
