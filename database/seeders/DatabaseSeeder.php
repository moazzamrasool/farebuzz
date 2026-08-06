<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            RolePermissionSeeder::class,
            SearchTabSettingSeeder::class,
            TravelCategorySeeder::class,
            DestinationSeeder::class,
            ActivitySeeder::class,
            AmenitySeeder::class,
            PackageFeatureSeeder::class,
            HotelSeeder::class,
            PackageSeeder::class,
            HolidayPackageSeeder::class,
            CouponSeeder::class,
            HomepageContentSeeder::class,
            CmsPageSeeder::class,
            AboutPageSeeder::class,
            NavbarMenuSeeder::class,
        ]);
    }
}
