<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Destination;
use App\Models\HomepageSection;
use App\Support\SiteTenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

// Seeds the homepage_sections / homepage_section_items rows behind the CRM-driven
// homepage. Most sections are still manually-curated static cards (image URLs
// transcribed verbatim — Unsplash/flagcdn hotlinks, local frontend/img template
// assets; see App\Support\MediaUrl for how these are resolved alongside genuine
// admin uploads). The 4 hotel/package/activity sliders are live-data sections
// instead (data_source set, no items) — see App\Services\Homepage\HomepageSliderData.
class HomepageContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->retireReplacedSections();

        $this->hero();
        $this->offers();
        $this->travelPros();
        $this->domesticPackages();
        $this->internationalPackages();
        $this->topActivities();
        $this->handpickedHotels();
        $this->trendingDestinations();
        $this->tripPlanner();
        $this->exploreIndia();
        $this->propertyTypes();
        $this->countrySpotlight();
        $this->footer();
    }

    // The manually-curated hotel/property sections replaced by the 4 live-data
    // sliders below (domestic/international packages, activities, hotels).
    // firstOrCreate() in section() never deletes stale rows on its own, so
    // environments that already seeded the old 14 sections need this explicit,
    // idempotent cleanup — cascadeOnDelete() on homepage_section_items handles
    // the DB rows, but uploaded image files need deleting separately.
    private function retireReplacedSections(): void
    {
        HomepageSection::whereIn('key', [
            'flagship_hotels', 'explore_stays', 'homes_guests_love', 'top_unique_properties', 'weekend_deals',
        ])->get()->each(function (HomepageSection $section) {
            $section->items->each(function ($item) {
                if ($item->image) {
                    Storage::disk('public')->delete($item->image);
                }
            });
            $section->delete();
        });
    }

    private function section(string $key, string $name, ?string $heading, ?string $subheading, int $sortOrder, array $extra = [], ?string $dataSource = null, ?int $itemLimit = null): HomepageSection
    {
        return HomepageSection::firstOrCreate(
            ['key' => $key],
            [
                'unique_id' => SiteTenant::id(),
                'name' => $name,
                'heading' => $heading,
                'subheading' => $subheading,
                'extra' => $extra,
                'data_source' => $dataSource,
                'item_limit' => $itemLimit,
                'sort_order' => $sortOrder,
                'status' => 'active',
            ]
        );
    }

    private function item(HomepageSection $section, array $attrs): void
    {
        $section->items()->create(array_merge([
            'unique_id' => SiteTenant::id(),
            'status' => 'active',
        ], $attrs));
    }

    private function seedOnce(HomepageSection $section, array $rows): void
    {
        if ($section->items()->exists()) {
            return;
        }
        foreach ($rows as $i => $row) {
            $row['sort_order'] = $i;
            $this->item($section, $row);
        }
    }

    // Unlike seedOnce() above, this always fixes up rows that already exist — used
    // by the sections whose `link` used to be a '#' placeholder, so re-running the
    // seeder repairs already-seeded homepages instead of skipping them. Keyed by
    // title, which is unique within each of these sections.
    private function upsertItems(HomepageSection $section, array $rows): void
    {
        foreach ($rows as $i => $row) {
            $row['sort_order'] = $i;
            $section->items()->updateOrCreate(
                ['title' => $row['title']],
                array_merge(['unique_id' => SiteTenant::id(), 'status' => 'active'], $row)
            );
        }
    }

    private function destinationLink(string $name): string
    {
        $destination = Destination::where('name', $name)->first();

        return $destination ? route('destinations.show', $destination->slug) : '#';
    }

    private function hero(): void
    {
        $section = $this->section(
            'hero',
            'Hero / Banner',
            'Your Journey Starts with FareBuzz',
            'Flights · Hotels · Trains · Buses · Cabs & More — All in One Place',
            0
        );

        $this->seedOnce($section, [
            ['group_key' => 'banner', 'image' => 'https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?w=1800&q=80'],
        ]);
    }

    private function offers(): void
    {
        $section = $this->section('offers', 'Offers', 'Offers', null, 1);

        // Coupons seeded by CouponSeeder (which runs before this seeder) — cards below
        // reference their ids so the code badge + real discount rules show on the
        // homepage rather than being purely decorative copy.
        $welcome10 = Coupon::where('code', 'WELCOME10')->first();
        $flat500 = Coupon::where('code', 'FLAT500')->first();
        $goa15 = Coupon::where('code', 'GOA15')->first();

        $this->seedOnce($section, [
            ['group_key' => 'holidays', 'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&q=60', 'label' => 'ALL BOOKINGS', 'title' => 'For Your Summer Trips: Get Up to 10% OFF*', 'description' => 'on Flights, Stays, Packages & More.', 'button_label' => 'BOOK NOW', 'link' => '#', 'coupon_id' => $welcome10?->id],
            ['group_key' => 'hotels', 'image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=400&q=60', 'label' => 'DOM HOTELS', 'title' => 'FOR THE PERFECT STAYS IN THE HILLS:', 'description' => 'Book Hotels @ Flat ₹500 OFF*', 'button_label' => 'BOOK NOW', 'link' => '#', 'coupon_id' => $flat500?->id],
            ['group_key' => 'holidays', 'image' => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=400&q=60', 'label' => 'GOA PACKAGES', 'title' => 'Grab Up to 15% OFF*', 'description' => 'on the Goa Beach Escape package — heritage stays, sun & sand!', 'button_label' => 'VIEW DETAILS', 'link' => '#', 'coupon_id' => $goa15?->id],
            ['group_key' => 'flights', 'image' => 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=400&q=60', 'label' => 'DOM FLIGHTS', 'title' => 'LIVE NOW: Great Connections Fest by IndiGo', 'description' => 'With Connecting Flights Starting @ ₹3,999* & More!', 'button_label' => 'BOOK NOW', 'link' => '#'],
            ['group_key' => 'hotels', 'image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=400&q=60', 'label' => 'DOM HOTELS', 'title' => 'ENJOY SUMMIT HOTELS:', 'description' => 'Book Summit Hotels & Resorts @ Up to 30% OFF*', 'button_label' => 'VIEW DETAILS', 'link' => '#'],
            ['group_key' => 'hotels', 'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&q=60', 'label' => 'DOM HOTELS', 'title' => "Today's Deals on OYO Rooms", 'description' => 'Up to 40% OFF on select OYO-Serviced Hotels.', 'button_label' => 'BOOK NOW', 'link' => '#'],
        ]);
    }

    private function travelPros(): void
    {
        $section = $this->section('travel_pros', 'For Travel Pros', 'For travel pros', null, 2);

        $this->seedOnce($section, [
            ['image' => 'frontend/img/pro_ai.png', 'title' => 'Plan with AI', 'subtitle' => 'Get travel questions answered', 'link' => '#'],
            ['image' => 'frontend/img/pro_time.png', 'title' => 'Best Time to Travel', 'subtitle' => 'Know when to save', 'link' => '#'],
            ['image' => 'frontend/img/pro_explore.png', 'title' => 'Explore', 'subtitle' => 'See destinations on your budget', 'link' => '#'],
            ['image' => 'frontend/img/pro_trips.png', 'title' => 'Trips', 'subtitle' => 'Keep all your plans in one place', 'link' => '#'],
        ]);
    }

    private function domesticPackages(): void
    {
        $this->section(
            'domestic_packages',
            'Domestic Holiday Packages',
            'Domestic Holiday Packages',
            'Handpicked holiday packages across India',
            3,
            [],
            'domestic_packages',
            10
        );
    }

    private function internationalPackages(): void
    {
        $this->section(
            'international_packages',
            'International Holiday Packages',
            'International Holidays',
            'Explore the world with our curated international packages',
            4,
            [],
            'international_packages',
            10
        );
    }

    private function topActivities(): void
    {
        $this->section(
            'top_activities',
            'Top Activities',
            'Top Activities & Experiences',
            'Unforgettable experiences handpicked for you',
            5,
            [],
            'top_activities',
            10
        );
    }

    private function handpickedHotels(): void
    {
        $this->section(
            'handpicked_hotels',
            'Handpicked Stays',
            'Handpicked Stays',
            'Comfortable stays, handpicked for every trip',
            6,
            [],
            'handpicked_hotels',
            10
        );
    }

    private function trendingDestinations(): void
    {
        $section = $this->section('trending_destinations', 'Trending Destinations', 'Trending destinations', null, 7);

        $this->upsertItems($section, [
            ['image' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800&q=70', 'title' => 'Singapore', 'label' => 'sg', 'link' => $this->destinationLink('Singapore'), 'meta' => ['tile_size' => 'large']],
            ['image' => 'https://images.unsplash.com/photo-1582510003544-4d00b7f74220?w=800&q=70', 'title' => 'Chennai', 'label' => 'in', 'link' => $this->destinationLink('Chennai'), 'meta' => ['tile_size' => 'large']],
            ['image' => 'https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=600&q=70', 'title' => 'Bengaluru', 'label' => 'in', 'link' => $this->destinationLink('Bengaluru'), 'meta' => ['tile_size' => 'small']],
            ['image' => 'https://images.unsplash.com/photo-1529655683826-aba9b3e77383?w=600&q=70', 'title' => 'London', 'label' => 'gb', 'link' => $this->destinationLink('London'), 'meta' => ['tile_size' => 'small']],
            ['image' => 'https://images.unsplash.com/photo-1528127269322-539801943592?w=600&q=70', 'title' => 'Bangkok', 'label' => 'th', 'link' => $this->destinationLink('Bangkok'), 'meta' => ['tile_size' => 'small']],
        ]);
    }

    private function tripPlanner(): void
    {
        $section = $this->section('trip_planner', 'Quick and Easy Trip Planner', 'Quick and easy trip planner', 'Pick a vibe and explore the top destinations in India', 9, [
            'tabs' => [
                ['key' => 'historical_tours', 'label' => 'Historical Tours'],
                ['key' => 'adventure_exploration', 'label' => 'Adventure & Exploration'],
                ['key' => 'historical_expeditions', 'label' => 'Historical Expeditions'],
                ['key' => 'wildlife_nature', 'label' => 'Wildlife & Nature'],
                ['key' => 'shopping', 'label' => 'Shopping'],
                ['key' => 'beach_trips', 'label' => 'Beach Trips'],
                ['key' => 'more', 'label' => 'More'],
            ],
        ]);

        $this->upsertItems($section, [
            // Historical Tours
            ['group_key' => 'historical_tours', 'image' => 'https://images.unsplash.com/photo-1587474260584-136574528ed5?w=300&q=60', 'title' => 'New Delhi', 'link' => $this->destinationLink('Delhi'), 'meta' => ['distance_km' => '2.6']],
            ['group_key' => 'historical_tours', 'image' => 'https://images.unsplash.com/photo-1564507592333-c60657eea523?w=300&q=60', 'title' => 'Agra', 'link' => $this->destinationLink('Agra'), 'meta' => ['distance_km' => '181']],
            ['group_key' => 'historical_tours', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/c/c3/Hawa_Mahal_Jaipur.jpg', 'title' => 'Jaipur', 'link' => $this->destinationLink('Jaipur'), 'meta' => ['distance_km' => '238']],
            ['group_key' => 'historical_tours', 'image' => 'https://images.unsplash.com/photo-1609920658906-8223bd289001?w=300&q=60', 'title' => 'Lucknow', 'link' => $this->destinationLink('Lucknow'), 'meta' => ['distance_km' => '416']],
            ['group_key' => 'historical_tours', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/a/a7/Taj-ul-Masajid_Bhopal.JPG', 'title' => 'Bhopal', 'link' => $this->destinationLink('Bhopal'), 'meta' => ['distance_km' => '599']],
            ['group_key' => 'historical_tours', 'image' => 'https://images.unsplash.com/photo-1561361058-c24cecae35ca?w=300&q=60', 'title' => 'Varanasi', 'link' => $this->destinationLink('Varanasi'), 'meta' => ['distance_km' => '680']],
            // Adventure & Exploration
            ['group_key' => 'adventure_exploration', 'image' => 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?w=300&q=60', 'title' => 'Manali', 'link' => $this->destinationLink('Manali'), 'meta' => ['distance_km' => '537']],
            ['group_key' => 'adventure_exploration', 'image' => 'https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?w=300&q=60', 'title' => 'Goa', 'link' => $this->destinationLink('Goa'), 'meta' => ['distance_km' => '1875']],
            ['group_key' => 'adventure_exploration', 'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=300&q=60', 'title' => 'Bali', 'link' => $this->destinationLink('Bali'), 'meta' => ['distance_km' => '4300']],
            // Historical Expeditions
            ['group_key' => 'historical_expeditions', 'image' => 'https://images.unsplash.com/photo-1600093463592-8e36ae95ef56?w=300&q=60', 'title' => 'Hyderabad', 'link' => $this->destinationLink('Hyderabad'), 'meta' => ['distance_km' => '1580']],
            ['group_key' => 'historical_expeditions', 'image' => 'https://images.unsplash.com/photo-1558431382-27e303142255?w=300&q=60', 'title' => 'Kolkata', 'link' => $this->destinationLink('Kolkata'), 'meta' => ['distance_km' => '1470']],
            ['group_key' => 'historical_expeditions', 'image' => 'https://images.unsplash.com/photo-1529253355930-ddbe423a2ac7?w=300&q=60', 'title' => 'Mumbai', 'link' => $this->destinationLink('Mumbai'), 'meta' => ['distance_km' => '1400']],
            // Wildlife & Nature
            ['group_key' => 'wildlife_nature', 'image' => 'https://images.unsplash.com/photo-1544979590-37e9b47eb705?w=300&q=60', 'title' => 'Ranthambore', 'link' => $this->destinationLink('Ranthambore'), 'meta' => ['distance_km' => '400']],
            ['group_key' => 'wildlife_nature', 'image' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=300&q=60', 'title' => 'Kaziranga', 'link' => $this->destinationLink('Kaziranga'), 'meta' => ['distance_km' => '2350']],
            // Shopping
            ['group_key' => 'shopping', 'image' => 'https://images.unsplash.com/photo-1587474260584-136574528ed5?w=300&q=60', 'title' => 'New Delhi Markets', 'link' => $this->destinationLink('Delhi'), 'meta' => ['distance_km' => '2.6']],
            ['group_key' => 'shopping', 'image' => 'https://images.unsplash.com/photo-1529253355930-ddbe423a2ac7?w=300&q=60', 'title' => 'Mumbai', 'link' => $this->destinationLink('Mumbai'), 'meta' => ['distance_km' => '1400']],
            ['group_key' => 'shopping', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/c/c3/Hawa_Mahal_Jaipur.jpg', 'title' => 'Jaipur Bazaars', 'link' => $this->destinationLink('Jaipur'), 'meta' => ['distance_km' => '238']],
            // Beach Trips
            ['group_key' => 'beach_trips', 'image' => 'https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?w=300&q=60', 'title' => 'Goa Beaches', 'link' => $this->destinationLink('Goa'), 'meta' => ['distance_km' => '1875']],
            ['group_key' => 'beach_trips', 'image' => 'https://images.unsplash.com/photo-1582510003544-4d00b7f74220?w=300&q=60', 'title' => 'Mahabalipuram', 'link' => $this->destinationLink('Mahabalipuram'), 'meta' => ['distance_km' => '2185']],
            ['group_key' => 'beach_trips', 'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=300&q=60', 'title' => 'Bali', 'link' => $this->destinationLink('Bali'), 'meta' => ['distance_km' => '4300']],
            // More
            ['group_key' => 'more', 'image' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=300&q=60', 'title' => 'Singapore', 'link' => $this->destinationLink('Singapore'), 'meta' => ['distance_km' => '4150']],
            ['group_key' => 'more', 'image' => 'https://images.unsplash.com/photo-1528127269322-539801943592?w=300&q=60', 'title' => 'Bangkok', 'link' => $this->destinationLink('Bangkok'), 'meta' => ['distance_km' => '3000']],
            ['group_key' => 'more', 'image' => 'https://images.unsplash.com/photo-1529655683826-aba9b3e77383?w=300&q=60', 'title' => 'London', 'link' => $this->destinationLink('London'), 'meta' => ['distance_km' => '6700']],
        ]);
    }

    private function exploreIndia(): void
    {
        $section = $this->section('explore_india', 'Explore India', 'Explore India', 'These popular destinations have a lot to offer', 10);

        $this->upsertItems($section, [
            ['image' => 'https://images.unsplash.com/photo-1582510003544-4d00b7f74220?w=300&q=60', 'title' => 'Chennai', 'link' => $this->destinationLink('Chennai'), 'meta' => ['property_count' => 1404]],
            ['image' => 'https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=300&q=60', 'title' => 'Bengaluru', 'link' => $this->destinationLink('Bengaluru'), 'meta' => ['property_count' => 3344]],
            ['image' => 'https://images.unsplash.com/photo-1600093463592-8e36ae95ef56?w=300&q=60', 'title' => 'Hyderabad', 'link' => $this->destinationLink('Hyderabad'), 'meta' => ['property_count' => 2027]],
            ['image' => 'https://images.unsplash.com/photo-1558431382-27e303142255?w=300&q=60', 'title' => 'Kolkata', 'link' => $this->destinationLink('Kolkata'), 'meta' => ['property_count' => 920]],
            ['image' => 'https://upload.wikimedia.org/wikipedia/commons/d/d1/Ram_Janmbhoomi_Mandir%2C_Ayodhya_Dham.jpg', 'title' => 'Ayodhya', 'link' => $this->destinationLink('Ayodhya'), 'meta' => ['property_count' => 977]],
            ['image' => 'https://images.unsplash.com/photo-1529253355930-ddbe423a2ac7?w=300&q=60', 'title' => 'Mumbai', 'link' => $this->destinationLink('Mumbai'), 'meta' => ['property_count' => 1813]],
        ]);
    }

    private function propertyTypes(): void
    {
        $section = $this->section('property_types', 'Browse by Property Type', 'Browse by property type', null, 11);

        $this->upsertItems($section, [
            ['image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=400&q=60', 'title' => 'Hotels', 'link' => route('hotels.index', ['property_type' => 'hotel'])],
            ['image' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=400&q=60', 'title' => 'Apartments', 'link' => route('hotels.index', ['property_type' => 'apartment'])],
            ['image' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=400&q=60', 'title' => 'Resorts', 'link' => route('hotels.index', ['property_type' => 'resort'])],
            ['image' => 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=400&q=60', 'title' => 'Villas', 'link' => route('hotels.index', ['property_type' => 'villa'])],
        ]);
    }

    private function countrySpotlight(): void
    {
        $section = $this->section('country_spotlight', 'Featured Country Spotlight', 'Radiant Dubai', 'Desert Dunes & Dazzling Skylines', 12);

        $link = $this->destinationLink('Dubai');

        $this->upsertItems($section, [
            ['image' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Dubai%20Marina%20Skyline.jpg?width=600', 'title' => 'Dubai Marina Skyline', 'description' => 'Glittering towers along the world\'s largest man-made marina', 'link' => $link],
            ['image' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Dune%20bashing%20in%20Dubai.jpg?width=600', 'title' => 'Desert Dune Safari', 'description' => 'Dune bashing and a BBQ dinner under the stars', 'link' => $link],
            ['image' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Dubai%20Frame.jpg?width=600', 'title' => 'Dubai Frame Views', 'description' => 'Panoramic views over Old and New Dubai from the sky deck', 'link' => $link],
        ]);
    }

    private function footer(): void
    {
        $section = $this->section('footer', 'Footer', null, null, 13, [
            'blurb' => 'Your trusted travel companion for flights, hotels, holidays, trains and more. Best prices guaranteed.',
            'social_facebook' => '#',
            'social_twitter' => '#',
            'social_instagram' => '#',
            'social_youtube' => '#',
            'copyright_text' => '© 2025 FareBuzz. All rights reserved.',
        ]);

        $this->seedOnce($section, [
            // Company
            ['group_key' => 'company', 'title' => 'About Us', 'link' => 'about-us'],
            ['group_key' => 'company', 'title' => 'Careers', 'link' => 'careers'],
            ['group_key' => 'company', 'title' => 'News & Blog', 'link' => 'news-blog'],
            ['group_key' => 'company', 'title' => 'Investor Relations', 'link' => 'investor-relations'],
            ['group_key' => 'company', 'title' => 'Partner with us', 'link' => 'partner-with-us'],
            // Products
            ['group_key' => 'products', 'title' => 'Flights', 'link' => '#'],
            ['group_key' => 'products', 'title' => 'Hotels', 'link' => '#'],
            ['group_key' => 'products', 'title' => 'Holiday Packages', 'link' => 'holiday-packages'],
            ['group_key' => 'products', 'title' => 'Trains', 'link' => '#'],
            ['group_key' => 'products', 'title' => 'Buses & Cabs', 'link' => '#'],
            // Support
            ['group_key' => 'support', 'title' => 'Help Center', 'link' => 'help-center'],
            ['group_key' => 'support', 'title' => 'My Trips', 'link' => '#'],
            ['group_key' => 'support', 'title' => 'Cancellation Policy', 'link' => 'cancellation-policy'],
            ['group_key' => 'support', 'title' => 'Travel Insurance', 'link' => 'travel-insurance'],
            ['group_key' => 'support', 'title' => 'Contact Us', 'link' => 'contact-us'],
            // Download App
            ['group_key' => 'download_app', 'title' => 'App Store', 'link' => '#', 'meta' => ['icon' => 'apple']],
            ['group_key' => 'download_app', 'title' => 'Google Play', 'link' => '#', 'meta' => ['icon' => 'google-play']],
            // Legal (bottom bar)
            ['group_key' => 'legal', 'title' => 'Privacy Policy', 'link' => 'privacy-policy'],
            ['group_key' => 'legal', 'title' => 'Terms of Service', 'link' => 'terms-of-service'],
            ['group_key' => 'legal', 'title' => 'Cookie Policy', 'link' => 'cookie-policy'],
            ['group_key' => 'legal', 'title' => 'Sitemap', 'link' => 'sitemap'],
        ]);
    }
}
