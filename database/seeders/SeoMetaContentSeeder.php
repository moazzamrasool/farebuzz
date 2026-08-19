<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\ListingPageSeo;
use App\Support\SiteTenant;
use Illuminate\Database\Seeder;

// Applies the SEO agency's approved meta_title/meta_description for 13 public URLs
// (india/international packages, hotels, activities landing pages + 9 destination
// package listings). Idempotent — safe to re-run; only meta_title/meta_description
// are touched, every other SEO field (keywords, canonical, OG, robots) is left alone
// so values already set through the admin screens are never overwritten.
class SeoMetaContentSeeder extends Seeder
{
    // page_key (see ListingPageSeo::PAGES) => [meta_title, meta_description]
    private const LISTING_PAGES = [
        'india-packages' => [
            'India Tour Packages - Book Holiday Packages in India',
            "India Tour Packages for popular destinations with itineraries, hotels, sightseeing and flexible travel options. Plan your holiday with FareBuzzer Travel.",
        ],
        'international-packages' => [
            'International Tour Packages for Enjoying Global Holidays',
            'Explore International Tour Packages with curated itineraries, hotels, sightseeing and travel options. Plan your next overseas holiday with FareBuzzer Travel.',
        ],
        'hotels' => [
            'Book your Hotels in India for Every Kind of Traveller',
            'Find hotels in India for holidays, business trips and family stays. Compare options across popular destinations and plan your trip with FareBuzzer Travel.',
        ],
        'activities' => [
            'Here is Best Travel Activities & Experiences - FareBuzzer Travel',
            'Discover travel activities for sightseeing, adventure and local experiences. Explore things to do at popular destinations with FareBuzzer Travel.',
        ],
    ];

    // destination slug => [meta_title, meta_description] for the /destinations/{slug}/packages page
    private const DESTINATION_PACKAGE_PAGES = [
        'kashmir' => [
            'Kashmir Tour Packages Get 50% off on Kashmir Trip',
            'Explore Kashmir Tour Packages with curated itineraries, hotels, sightseeing and transfers. Plan holidays for couples, families or groups with FareBuzzer Travel.',
        ],
        'himachal-pradesh' => [
            'Get 30% Off on Himachal Pradesh Tour Packages - Book Now!',
            'Discover Himachal Pradesh Tour Packages with flexible itineraries, hotels, sightseeing and transfers. Plan a memorable mountain holiday with FareBuzzer Travel.',
        ],
        'andaman' => [
            'Andaman Tour Packages Starting from 11499/-pp [2026]',
            'Explore Andaman Tour Packages with island stays, sightseeing, transfers and flexible itineraries. Plan your beach getaway with FareBuzzer Travel.',
        ],
        'kerala' => [
            'Book Kerala Tour Packages from Anywhere India with Family',
            'Discover Kerala Tour Packages with scenic itineraries, hotels, sightseeing and transfers. Plan a relaxed Kerala holiday with FareBuzzer Travel.',
        ],
        'sikkim' => [
            'Sikkim Tour Packages Starting with @ Rs. 6200',
            'Explore Sikkim Tour Packages with mountain stays, sightseeing, transfers and flexible itineraries. Plan your Sikkim getaway with FareBuzzer Travel.',
        ],
        'vietnam' => [
            'Book Vietnam Tour Packages from India in Just @₹49999',
            'Explore Vietnam Tour Packages with curated itineraries, hotels, sightseeing and transfers. Plan your Vietnam holiday with FareBuzzer Travel.',
        ],
        'malaysia' => [
            'Malaysia Tour Packages from India - Malaysia Trip Deals',
            'Discover Malaysia Tour Packages with city stays, sightseeing, transfers and flexible itineraries. Plan your Malaysia holiday with FareBuzzer Travel.',
        ],
        'dubai' => [
            'Dubai Tour Packages - Flights, Hotels & Sightseeing',
            'Explore Dubai Tour Packages with hotels, sightseeing, transfers and flexible itineraries. Plan your Dubai holiday with FareBuzzer Travel.',
        ],
        'singapore' => [
            'Singapore Tour Packages From New Delhi at ₹32516',
            'Explore Singapore Tour Packages with hotels, sightseeing, transfers and flexible itineraries. Plan your Singapore holiday with FareBuzzer Travel.',
        ],
    ];

    public function run(): void
    {
        $tenantId = SiteTenant::id();

        if (!$tenantId) {
            $this->command?->error('COMPANY_UNIQUE_ID is not set — aborting, nothing seeded.');
            return;
        }

        foreach (self::LISTING_PAGES as $pageKey => [$title, $description]) {
            ListingPageSeo::updateOrCreate(
                ['unique_id' => $tenantId, 'page_key' => $pageKey],
                ['meta_title' => $title, 'meta_description' => $description]
            );
        }

        foreach (self::DESTINATION_PACKAGE_PAGES as $slug => [$title, $description]) {
            $destination = Destination::where('unique_id', $tenantId)->where('slug', $slug)->first();

            if (!$destination) {
                $this->command?->warn("Destination with slug \"{$slug}\" not found for this tenant — skipped.");
                continue;
            }

            $destination->update([
                'packages_meta_title' => $title,
                'packages_meta_description' => $description,
            ]);
        }
    }
}
