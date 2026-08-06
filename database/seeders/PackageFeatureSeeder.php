<?php

namespace Database\Seeders;

use App\Models\PackageFeature;
use Illuminate\Database\Seeder;

class PackageFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $inclusions = [
            'Return flights from Delhi (economy class)',
            '4 nights stay at 4-star hotel (double occupancy)',
            'Daily breakfast at the hotel',
            'Welcome dinner on Day 1 & Farewell dinner on Day 4',
            'AC vehicle for all airport/hotel transfers',
            'All sightseeing as per itinerary',
            'Spice plantation entry & traditional lunch',
            'Mandovi River Sunset Cruise tickets',
            'Dedicated FareBuzz travel guide',
            'All applicable taxes & GST',
        ];

        $exclusions = [
            'Lunches and dinners (except those listed)',
            'Water sports & adventure activities',
            'Personal expenses, tips & gratuities',
            'Travel insurance (recommended)',
            'Any services not mentioned above',
            'Entry fees to monuments & churches',
            'Porterage / laundry charges',
        ];

        foreach ($inclusions as $index => $title) {
            PackageFeature::firstOrCreate(
                ['title' => $title, 'type' => 'inclusion'],
                ['icon' => 'bi-check-circle-fill', 'status' => 'active', 'sort_order' => $index]
            );
        }

        foreach ($exclusions as $index => $title) {
            PackageFeature::firstOrCreate(
                ['title' => $title, 'type' => 'exclusion'],
                ['icon' => 'bi-x-circle-fill', 'status' => 'active', 'sort_order' => $index]
            );
        }
    }
}
