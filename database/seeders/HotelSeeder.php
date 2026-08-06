<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Destination;
use App\Models\Hotel;
use App\Support\SiteTenant;
use Database\Seeders\Concerns\GeneratesDemoImages;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    use GeneratesDemoImages;

    public function run(): void
    {
        $this->seedHotel([
            'name'            => 'Taj Vivanta Goa',
            'destination'     => 'Goa',
            'star_rating'     => 4,
            'address'         => 'Candolim Beach Road, North Goa',
            'latitude'        => 15.5185,
            'longitude'       => 73.7684,
            'description'     => "Nestled beside the shimmering Arabian Sea, Taj Vivanta offers luxurious rooms with sea views, a stunning infinity pool, and Goa's finest dining. Just a 5-minute walk from Candolim Beach.",
            'rating_score'    => 8.6,
            'review_count'    => 1240,
            'property_rules'  => "Check-in from 2:00 PM, check-out until 11:00 AM.\nValid photo ID required at check-in.\nSmoking is not permitted inside the rooms.\nPets are not allowed.",
            'color'           => '#0d6efd',
            'amenities'       => ['Free WiFi', 'Pool', 'Parking', 'Restaurant', 'AC', 'Spa', 'Beach Access'],
            'room_types'      => [
                ['name' => 'Deluxe Room (Sea View)', 'price' => 6999, 'discounted_price' => 5999, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 320, 'meal_plan' => 'breakfast', 'refundable' => true],
                ['name' => 'Superior Room',           'price' => 5499, 'discounted_price' => 4799, 'occupancy_adults' => 2, 'occupancy_children' => 0, 'bed_type' => 'Queen Bed', 'size_sqft' => 260, 'meal_plan' => 'room_only', 'refundable' => true],
                ['name' => 'Family Suite',             'price' => 9999, 'discounted_price' => null, 'occupancy_adults' => 3, 'occupancy_children' => 2, 'bed_type' => 'King Bed + Sofa Bed', 'size_sqft' => 480, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
            ],
            'reviews' => [
                ['name' => 'Ananya S.', 'rating' => 9.0, 'location' => 9.2, 'cleanliness' => 9.0, 'service' => 8.8, 'value' => 8.6, 'comment' => 'Gorgeous sea-facing rooms and the pool was spotless. Staff went out of their way to help.', 'days_ago' => 12],
                ['name' => 'Rohit M.',  'rating' => 8.4, 'location' => 8.8, 'cleanliness' => 8.5, 'service' => 8.0, 'value' => 8.0, 'comment' => 'Great location right by the beach, breakfast spread was excellent.', 'days_ago' => 28],
                ['name' => 'Priya K.',  'rating' => 7.8, 'location' => 8.5, 'cleanliness' => 8.0, 'service' => 7.5, 'value' => 7.0, 'comment' => 'Nice stay overall, room service was a bit slow during peak hours.', 'days_ago' => 45],
                ['name' => 'Karan D.',  'rating' => 9.4, 'location' => 9.5, 'cleanliness' => 9.5, 'service' => 9.3, 'value' => 9.0, 'comment' => 'Best hotel stay in Goa so far. Will definitely come back!', 'days_ago' => 60],
            ],
        ]);

        $this->seedHotel([
            'name'            => 'The Himalayan Retreat',
            'destination'     => 'Manali',
            'star_rating'     => 4,
            'address'         => 'Hadimba Road, Old Manali, Himachal Pradesh',
            'latitude'        => 32.2432,
            'longitude'       => 77.1892,
            'description'     => 'A cosy mountain retreat with panoramic Himalayan views, a bonfire deck, and rooms warmed by traditional Kashmiri decor — the perfect base for exploring Old Manali and Solang Valley.',
            'rating_score'    => 8.2,
            'review_count'    => 860,
            'property_rules'  => "Check-in from 12:00 PM, check-out until 10:00 AM.\nValid photo ID required at check-in.\nBonfire available on request, weather permitting.\nUnmarried couples are welcome with valid ID proof.",
            'color'           => '#6c757d',
            'image'           => 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?w=900&q=80',
            'amenities'       => ['Free WiFi', 'Parking', 'Restaurant', 'Room Service'],
            'room_types'      => [
                ['name' => 'Mountain View Room',  'price' => 4499, 'discounted_price' => 3799, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'Queen Bed', 'size_sqft' => 240, 'meal_plan' => 'breakfast', 'refundable' => true],
                ['name' => 'Deluxe Cottage Room', 'price' => 5999, 'discounted_price' => 5299, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 300, 'meal_plan' => 'breakfast_dinner', 'refundable' => true],
            ],
            'reviews' => [
                ['name' => 'Neha T.',   'rating' => 8.6, 'location' => 9.0, 'cleanliness' => 8.5, 'service' => 8.4, 'value' => 8.5, 'comment' => 'Waking up to snow-capped mountains right outside the window was magical.', 'days_ago' => 10],
                ['name' => 'Vikram S.', 'rating' => 7.6, 'location' => 8.2, 'cleanliness' => 7.5, 'service' => 7.4, 'value' => 7.8, 'comment' => 'Good value stay, the bonfire evening was a nice touch.', 'days_ago' => 33],
                ['name' => 'Simran K.', 'rating' => 8.8, 'location' => 9.2, 'cleanliness' => 8.8, 'service' => 8.6, 'value' => 8.5, 'comment' => 'Loved the cottage rooms, very close to Hadimba Temple.', 'days_ago' => 50],
            ],
        ]);

        $this->seedHotel([
            'name'            => 'Emerald Bay Resort & Spa',
            'destination'     => 'Maldives',
            'star_rating'     => 5,
            'address'         => 'North Male Atoll, Maldives',
            'latitude'        => 4.1755,
            'longitude'       => 73.5093,
            'description'     => 'An overwater paradise resort with private villas above turquoise lagoons, a house reef for snorkelling, and an award-winning spa — the definition of a barefoot-luxury escape.',
            'rating_score'    => 9.2,
            'review_count'    => 540,
            'property_rules'  => "Check-in from 3:00 PM, check-out until 12:00 PM.\nPassport required at check-in.\nAlcohol is served on resort premises only.\nAll-inclusive meal plans available on request.",
            'color'           => '#0ea5e9',
            'image'           => 'https://images.unsplash.com/photo-1573843981267-be1999ff37cd?w=900&q=80',
            'amenities'       => ['Free WiFi', 'Pool', 'Restaurant', 'Spa', 'Beach Access', 'AC'],
            'room_types'      => [
                ['name' => 'Beach Villa',      'price' => 24999, 'discounted_price' => 21999, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 650, 'meal_plan' => 'breakfast', 'refundable' => true],
                ['name' => 'Overwater Villa',   'price' => 38999, 'discounted_price' => 34999, 'occupancy_adults' => 2, 'occupancy_children' => 0, 'bed_type' => 'King Bed', 'size_sqft' => 850, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
            ],
            'reviews' => [
                ['name' => 'Sarah W.',  'rating' => 9.6, 'location' => 9.8, 'cleanliness' => 9.6, 'service' => 9.5, 'value' => 9.0, 'comment' => 'The overwater villa exceeded every expectation. Snorkelling right off the deck!', 'days_ago' => 8],
                ['name' => 'Arjun P.',  'rating' => 9.0, 'location' => 9.5, 'cleanliness' => 9.2, 'service' => 8.8, 'value' => 8.5, 'comment' => 'Spectacular sunsets and the spa treatments were world-class.', 'days_ago' => 20],
                ['name' => 'Meera J.',  'rating' => 8.8, 'location' => 9.4, 'cleanliness' => 9.0, 'service' => 8.6, 'value' => 8.0, 'comment' => 'Pricey but absolutely worth it for a honeymoon.', 'days_ago' => 40],
            ],
        ]);

        // Homepage content (Homes Guests Love, Top Unique Properties, Weekend Deals)
        // links to these by name — see HomepageContentSeeder. Prices/ratings shown on
        // the homepage cards are the seeded HomepageSectionItem fields, not these rows;
        // these just need to be real, bookable, correctly-typed hotels the links resolve to.
        $homepageHotels = [
            ['name' => 'Hotel Comfort Stay With RR Group - Main Bazar New Delhi', 'destination' => 'Delhi', 'property_type' => 'apartment', 'star_rating' => 3, 'address' => 'Main Bazar, Paharganj, New Delhi', 'rating_score' => 9.0, 'review_count' => 9, 'color' => '#dc2626', 'price' => 1035, 'image' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=900&q=80'],
            ['name' => 'Newly Built Guest House Sapphire', 'destination' => 'Delhi', 'property_type' => 'apartment', 'star_rating' => 4, 'address' => 'Karol Bagh, New Delhi', 'rating_score' => 8.1, 'review_count' => 8, 'color' => '#dc2626', 'price' => 896, 'image' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=900&q=80'],
            ['name' => 'Orania B & B by Atsar', 'destination' => 'Delhi', 'property_type' => 'villa', 'star_rating' => 4, 'address' => 'Safdarjung Enclave, New Delhi', 'rating_score' => 8.3, 'review_count' => 189, 'color' => '#dc2626', 'price' => 3585, 'image' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=900&q=80'],
            ['name' => 'Avatar Living @Safdarjung Enclave', 'destination' => 'Delhi', 'property_type' => 'apartment', 'star_rating' => 4, 'address' => 'Safdarjung Enclave, New Delhi', 'rating_score' => 8.5, 'review_count' => 697, 'color' => '#dc2626', 'price' => 2981, 'image' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=900&q=80'],
            ['name' => "Taj Fisherman's Cove Resort & Spa, Chennai", 'destination' => 'Mahabalipuram', 'property_type' => 'hotel', 'star_rating' => 5, 'address' => 'Mahabalipuram, Tamil Nadu', 'rating_score' => 8.2, 'review_count' => 704, 'color' => '#0e7490', 'price' => 12500, 'image' => 'https://images.unsplash.com/photo-1583037189850-1921ae7c6c22?w=900&q=80'],
            ['name' => 'Radisson Blu Resort Temple Bay Mamallapuram', 'destination' => 'Mahabalipuram', 'property_type' => 'resort', 'star_rating' => 5, 'address' => 'Kovalam Road, Mamallapuram, Tamil Nadu', 'rating_score' => 8.5, 'review_count' => 852, 'color' => '#0e7490', 'price' => 14500, 'image' => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=900&q=80'],
            ['name' => 'Grand Hyatt Goa', 'destination' => 'Goa', 'property_type' => 'hotel', 'star_rating' => 5, 'address' => 'Bambolim, Panaji, Goa', 'rating_score' => 8.8, 'review_count' => 809, 'color' => '#0d6efd', 'price' => 13620, 'image' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=900&q=80'],
            ['name' => 'Heritage Madurai', 'destination' => 'Madurai', 'property_type' => 'resort', 'star_rating' => 5, 'address' => 'Melakkal Road, Madurai, Tamil Nadu', 'rating_score' => 8.9, 'review_count' => 1220, 'color' => '#9333ea', 'price' => 7055, 'image' => 'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=900&q=80'],
            ['name' => 'The Radiant Near Delhi International Airport', 'destination' => 'Delhi', 'property_type' => 'hotel', 'star_rating' => 4, 'address' => 'Mahipalpur, near IGI Airport, New Delhi', 'rating_score' => 9.2, 'review_count' => 54, 'color' => '#dc2626', 'price' => 3941, 'image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=900&q=80'],
            ['name' => 'Umaid Bhawan - A Heritage Style Boutique Hotel', 'destination' => 'Jaipur', 'property_type' => 'hotel', 'star_rating' => 4, 'address' => 'Bani Park, Jaipur, Rajasthan', 'rating_score' => 8.8, 'review_count' => 1900, 'color' => '#be123c', 'price' => 12579, 'image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=900&q=80'],
            ['name' => 'Limewood Stay - Executive Huda City Center', 'destination' => 'Gurgaon', 'property_type' => 'apartment', 'star_rating' => 3, 'address' => 'Huda City Centre, Gurgaon, Haryana', 'rating_score' => 8.5, 'review_count' => 785, 'color' => '#0891b2', 'price' => 3952, 'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=900&q=80'],
            ['name' => 'Hotel Lime Pride By GK Group Near IGI Delhi Airport', 'destination' => 'Delhi', 'property_type' => 'hotel', 'star_rating' => 3, 'address' => 'Mahipalpur, near IGI Airport, New Delhi', 'rating_score' => 8.7, 'review_count' => 196, 'color' => '#dc2626', 'price' => 2352, 'image' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=900&q=80'],
        ];

        foreach ($homepageHotels as $data) {
            $this->seedSimpleHotel($data);
        }

        $this->realCatalogueHotels();
    }

    // Real, well-known hotels backing the 14-package SEO catalogue — one per
    // itinerary "leg" (see HolidayPackageSeeder's day-wise hotel attachments).
    // Reuses seedHotel() below exactly like the demo hotels above: amenities sync,
    // 2 room types, reviews and a styled placeholder cover/gallery (per the approved
    // image strategy — hotel photography isn't scraped from third-party sites, only
    // destination/landmark photos are sourced from Wikimedia Commons).
    private function realCatalogueHotels(): void
    {
        $hotels = [
            // Kashmir
            ['name' => 'Vivanta Dal View, Srinagar', 'destination' => 'Kashmir', 'star_rating' => 5, 'address' => 'Boulevard Road, Dal Lake, Srinagar, Jammu & Kashmir', 'latitude' => 34.0870, 'longitude' => 74.8570, 'color' => '#0369a1',
                'image' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Dal%20Lake%20Srinagar.jpg?width=900',
                'description' => 'Overlooking the Dal Lake and the Zabarwan hills, Vivanta Dal View offers spacious rooms, a multi-cuisine restaurant and easy access to Boulevard Road\'s shikara ghats.',
                'rating_score' => 8.7, 'review_count' => 640, 'property_rules' => "Check-in from 2:00 PM, check-out until 11:00 AM.\nValid photo ID required at check-in.\nHeating available in all rooms during winter.",
                'amenities' => ['Free WiFi', 'Restaurant', 'Room Service', 'Heater', 'Lake/Mountain View', 'Parking', '24hr Front Desk'],
                'room_types' => [
                    ['name' => 'Deluxe Room (Lake View)', 'price' => 8999, 'discounted_price' => 7499, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 280, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Premium Room', 'price' => 6999, 'discounted_price' => 5999, 'occupancy_adults' => 2, 'occupancy_children' => 0, 'bed_type' => 'Queen Bed', 'size_sqft' => 240, 'meal_plan' => 'room_only', 'refundable' => true],
                ],
                'reviews' => [
                    ['name' => 'Aditya R.', 'rating' => 8.8, 'location' => 9.2, 'cleanliness' => 8.7, 'service' => 8.5, 'value' => 8.4, 'comment' => 'The lake view from our room was unreal, especially at sunrise.', 'days_ago' => 20],
                    ['name' => 'Farah K.', 'rating' => 8.5, 'location' => 9.0, 'cleanliness' => 8.5, 'service' => 8.2, 'value' => 8.0, 'comment' => 'Well located for the shikara rides and Boulevard Road walks.', 'days_ago' => 45],
                ],
            ],
            ['name' => 'Khyber Himalayan Resort & Spa', 'destination' => 'Kashmir', 'star_rating' => 5, 'address' => 'Gulmarg, Baramulla, Jammu & Kashmir', 'latitude' => 34.0484, 'longitude' => 74.3805, 'color' => '#0284c7',
                'image' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Snowfall%20In%20Gulmarg.jpg?width=900',
                'description' => 'A stone-and-timber luxury resort at the base of the Gulmarg Gondola, with a spa, fireside lounges and rooms designed around the surrounding pine forest and snow peaks.',
                'rating_score' => 9.0, 'review_count' => 410, 'property_rules' => "Check-in from 2:00 PM, check-out until 12:00 PM.\nValid photo ID required at check-in.\nGondola tickets can be arranged at the front desk.",
                'amenities' => ['Free WiFi', 'Restaurant', 'Spa', 'Heater', 'Lake/Mountain View', 'Room Service', 'Bar'],
                'room_types' => [
                    ['name' => 'Mountain View Room', 'price' => 15999, 'discounted_price' => 13999, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 320, 'meal_plan' => 'breakfast_dinner', 'refundable' => true],
                    ['name' => 'Deluxe Suite', 'price' => 21999, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 450, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Vikas T.', 'rating' => 9.4, 'location' => 9.6, 'cleanliness' => 9.3, 'service' => 9.2, 'value' => 8.8, 'comment' => 'Ski-in ski-out feel, minutes from the Gondola base station.', 'days_ago' => 15],
                    ['name' => 'Meher S.', 'rating' => 8.8, 'location' => 9.2, 'cleanliness' => 8.7, 'service' => 8.6, 'value' => 8.5, 'comment' => 'Spa after a day in the snow was exactly what we needed.', 'days_ago' => 60],
                ],
            ],
            ['name' => 'The Pahalgam Hotel', 'destination' => 'Kashmir', 'star_rating' => 4, 'address' => 'Near Mini Market, Pahalgam, Anantnag, Jammu & Kashmir', 'latitude' => 34.0161, 'longitude' => 75.3152, 'color' => '#0e7490',
                'image' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Lidder%20at%20Pahalgam.jpg?width=900',
                'description' => 'A riverside property on the banks of the Lidder River, a short walk from Pahalgam\'s Mini Market and the base point for Betaab and Aru Valley excursions.',
                'rating_score' => 8.3, 'review_count' => 380, 'property_rules' => "Check-in from 12:00 PM, check-out until 10:00 AM.\nValid photo ID required at check-in.",
                'amenities' => ['Free WiFi', 'Restaurant', 'Room Service', 'Heater', 'Lake/Mountain View', 'Parking'],
                'room_types' => [
                    ['name' => 'River View Room', 'price' => 6499, 'discounted_price' => 5499, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'Queen Bed', 'size_sqft' => 250, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Deluxe Room', 'price' => 7999, 'discounted_price' => 6999, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 300, 'meal_plan' => 'breakfast_dinner', 'refundable' => true],
                ],
                'reviews' => [
                    ['name' => 'Ritika M.', 'rating' => 8.4, 'location' => 8.9, 'cleanliness' => 8.2, 'service' => 8.0, 'value' => 8.3, 'comment' => 'Loved falling asleep to the sound of the Lidder River.', 'days_ago' => 25],
                    ['name' => 'Owais A.', 'rating' => 8.0, 'location' => 8.5, 'cleanliness' => 7.9, 'service' => 7.8, 'value' => 8.0, 'comment' => 'Good base for the Betaab Valley trip, staff arranged cabs easily.', 'days_ago' => 50],
                ],
            ],

            // Himachal Pradesh
            ['name' => 'The Oberoi Cecil, Shimla', 'destination' => 'Himachal Pradesh', 'star_rating' => 5, 'address' => 'Chaura Maidan, Shimla, Himachal Pradesh', 'latitude' => 31.1048, 'longitude' => 77.1734, 'color' => '#334155',
                'image' => 'https://commons.wikimedia.org/wiki/Special:FilePath/The%20Mall%20Road,%20Shimla.jpg?width=900',
                'description' => 'A restored colonial-era heritage hotel above Shimla\'s Mall Road, blending Raj-era architecture with mountain views over the Chaura Maidan valley.',
                'rating_score' => 9.1, 'review_count' => 520, 'property_rules' => "Check-in from 2:00 PM, check-out until 12:00 PM.\nValid photo ID required at check-in.\nHeritage property — smoking permitted only in designated areas.",
                'amenities' => ['Free WiFi', 'Restaurant', 'Spa', 'Gym', 'Heater', 'Lake/Mountain View', 'Bar', '24hr Front Desk'],
                'room_types' => [
                    ['name' => 'Classic Room', 'price' => 12999, 'discounted_price' => 10999, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 300, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Heritage Suite', 'price' => 19999, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 500, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Kunal B.', 'rating' => 9.3, 'location' => 9.4, 'cleanliness' => 9.2, 'service' => 9.1, 'value' => 8.7, 'comment' => 'The heritage charm is unmatched, felt like stepping back in time.', 'days_ago' => 18],
                    ['name' => 'Ishita P.', 'rating' => 8.9, 'location' => 9.0, 'cleanliness' => 8.8, 'service' => 8.9, 'value' => 8.4, 'comment' => 'Short walk to Mall Road, wonderful breakfast spread.', 'days_ago' => 40],
                ],
            ],
            ['name' => 'Span Resort & Spa, Manali', 'destination' => 'Himachal Pradesh', 'star_rating' => 4, 'address' => 'Kullu-Manali Highway, near Katrain, Manali, Himachal Pradesh', 'latitude' => 32.1698, 'longitude' => 77.1449, 'color' => '#475569',
                'image' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Beas%20River%20at%20Nehru%20Kund,%20Manali%20(1).jpg?width=900',
                'description' => 'A riverside resort on the banks of the Beas River between Kullu and Manali, with orchards, a spa and easy access to Solang Valley and the Atal Tunnel.',
                'rating_score' => 8.6, 'review_count' => 470, 'property_rules' => "Check-in from 12:00 PM, check-out until 10:00 AM.\nValid photo ID required at check-in.\nBonfire available on request in season.",
                'amenities' => ['Free WiFi', 'Restaurant', 'Spa', 'Parking', 'Heater', 'Lake/Mountain View', 'Room Service'],
                'room_types' => [
                    ['name' => 'River View Room', 'price' => 7499, 'discounted_price' => 6499, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'Queen Bed', 'size_sqft' => 260, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Deluxe Cottage', 'price' => 9999, 'discounted_price' => 8499, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 340, 'meal_plan' => 'breakfast_dinner', 'refundable' => true],
                ],
                'reviews' => [
                    ['name' => 'Nitin G.', 'rating' => 8.7, 'location' => 8.8, 'cleanliness' => 8.6, 'service' => 8.5, 'value' => 8.5, 'comment' => 'Peaceful riverside setting, great base for the Solang Valley day trip.', 'days_ago' => 22],
                    ['name' => 'Priyanka V.', 'rating' => 8.5, 'location' => 8.6, 'cleanliness' => 8.4, 'service' => 8.3, 'value' => 8.4, 'comment' => 'Cottages are spacious and the spa was a nice touch after Rohtang.', 'days_ago' => 55],
                ],
            ],

            // Kerala
            ['name' => 'Grand Hyatt Kochi Bolgatty', 'destination' => 'Kerala', 'star_rating' => 5, 'address' => 'Bolgatty Island, Kochi, Kerala', 'latitude' => 9.9865, 'longitude' => 76.2757, 'color' => '#15803d',
                'description' => 'A waterfront resort on Bolgatty Island with private marina access, multiple pools and views across Kochi\'s backwaters and harbour.',
                'rating_score' => 9.0, 'review_count' => 980, 'property_rules' => "Check-in from 2:00 PM, check-out until 12:00 PM.\nValid photo ID required at check-in.",
                'amenities' => ['Free WiFi', 'Pool', 'Restaurant', 'Spa', 'Gym', 'Bar', 'Airport Transfer', 'Lake/Mountain View'],
                'room_types' => [
                    ['name' => 'Deluxe Room (Backwater View)', 'price' => 10999, 'discounted_price' => 9499, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 340, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Club Room', 'price' => 15999, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 420, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Sandeep N.', 'rating' => 9.2, 'location' => 9.0, 'cleanliness' => 9.3, 'service' => 9.2, 'value' => 8.8, 'comment' => 'The backwater views from the pool deck at sunset were stunning.', 'days_ago' => 12],
                    ['name' => 'Divya R.', 'rating' => 8.8, 'location' => 8.7, 'cleanliness' => 8.9, 'service' => 8.8, 'value' => 8.5, 'comment' => 'Great starting point before heading to Munnar.', 'days_ago' => 38],
                ],
            ],
            ['name' => 'Spice Tree Munnar', 'destination' => 'Kerala', 'star_rating' => 4, 'address' => 'Chinnakanal, Munnar, Kerala', 'latitude' => 10.0619, 'longitude' => 77.2417, 'color' => '#166534',
                'description' => 'A resort set among Munnar\'s tea and spice plantations, with private cottages, a spa and views over the surrounding hills.',
                'rating_score' => 8.7, 'review_count' => 610, 'property_rules' => "Check-in from 1:00 PM, check-out until 11:00 AM.\nValid photo ID required at check-in.",
                'amenities' => ['Free WiFi', 'Restaurant', 'Spa', 'Parking', 'Lake/Mountain View', 'Room Service'],
                'room_types' => [
                    ['name' => 'Plantation View Room', 'price' => 6999, 'discounted_price' => 5999, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'Queen Bed', 'size_sqft' => 260, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Cottage Suite', 'price' => 9499, 'discounted_price' => 8299, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 380, 'meal_plan' => 'breakfast_dinner', 'refundable' => true],
                ],
                'reviews' => [
                    ['name' => 'Alan J.', 'rating' => 8.9, 'location' => 9.1, 'cleanliness' => 8.8, 'service' => 8.6, 'value' => 8.5, 'comment' => 'Waking up to mist over the tea gardens was magical.', 'days_ago' => 28],
                    ['name' => 'Meenakshi S.', 'rating' => 8.4, 'location' => 8.6, 'cleanliness' => 8.3, 'service' => 8.2, 'value' => 8.3, 'comment' => 'Cottages are cosy, food at the in-house restaurant was excellent.', 'days_ago' => 52],
                ],
            ],
            ['name' => 'Cardamom County, Thekkady', 'destination' => 'Kerala', 'star_rating' => 4, 'address' => 'Thekkady-Kumily Road, Thekkady, Kerala', 'latitude' => 9.5920, 'longitude' => 77.1603, 'color' => '#14532d',
                'description' => 'A plantation-style resort close to the Periyar Tiger Reserve, run by CGH Earth, built around a working cardamom and spice estate.',
                'rating_score' => 8.5, 'review_count' => 340, 'property_rules' => "Check-in from 1:00 PM, check-out until 11:00 AM.\nValid photo ID required at check-in.",
                'amenities' => ['Free WiFi', 'Restaurant', 'Pool', 'Parking', 'Lake/Mountain View'],
                'room_types' => [
                    ['name' => 'Garden View Room', 'price' => 6499, 'discounted_price' => 5499, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'Queen Bed', 'size_sqft' => 240, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Deluxe Cottage', 'price' => 8499, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 320, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Rahul K.', 'rating' => 8.6, 'location' => 8.8, 'cleanliness' => 8.5, 'service' => 8.4, 'value' => 8.3, 'comment' => 'Close to the Periyar boat jetty, loved the spice garden walk.', 'days_ago' => 33],
                    ['name' => 'Anjali T.', 'rating' => 8.3, 'location' => 8.5, 'cleanliness' => 8.2, 'service' => 8.1, 'value' => 8.2, 'comment' => 'Quiet, green property — good value for the location.', 'days_ago' => 58],
                ],
            ],
            ['name' => 'Xandari Pearl Houseboat, Alleppey', 'destination' => 'Kerala', 'star_rating' => 4, 'address' => 'Alleppey Backwaters, Alappuzha, Kerala', 'latitude' => 9.4981, 'longitude' => 76.3388, 'color' => '#0f766e',
                'description' => 'A traditional kettuvallam houseboat cruising the Alleppey backwaters, with an on-board chef, sundeck and air-conditioned bedrooms.',
                'rating_score' => 8.8, 'review_count' => 290, 'property_rules' => "Check-in from 12:00 PM (boarding), check-out by 9:00 AM the next day.\nValid photo ID required at check-in.\nMeals are cooked fresh on board.",
                'amenities' => ['AC', 'Restaurant', 'Room Service', 'Lake/Mountain View'],
                'room_types' => [
                    ['name' => 'Deluxe Cabin', 'price' => 8999, 'discounted_price' => 7999, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'Queen Bed', 'size_sqft' => 180, 'meal_plan' => 'breakfast_dinner', 'refundable' => true],
                    ['name' => 'Premium Cabin (Upper Deck)', 'price' => 11999, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 220, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Vishnu P.', 'rating' => 9.0, 'location' => 9.2, 'cleanliness' => 8.9, 'service' => 8.8, 'value' => 8.6, 'comment' => 'Watching the backwater sunset from the sundeck was the highlight of our trip.', 'days_ago' => 10],
                    ['name' => 'Lakshmi R.', 'rating' => 8.6, 'location' => 8.8, 'cleanliness' => 8.5, 'service' => 8.5, 'value' => 8.4, 'comment' => 'Food cooked on board was some of the best Kerala food we had.', 'days_ago' => 30],
                ],
            ],

            // Sikkim
            ['name' => 'Mayfair Spa Resort & Casino, Gangtok', 'destination' => 'Sikkim', 'star_rating' => 5, 'address' => 'Ranipool, Gangtok, Sikkim', 'latitude' => 27.3167, 'longitude' => 88.5865, 'color' => '#166534',
                'image' => 'https://commons.wikimedia.org/wiki/Special:FilePath/View%20of%20Gangtok%20city%20from%20Ropeway.jpg?width=900',
                'description' => 'A hillside luxury resort on the outskirts of Gangtok, with landscaped gardens, a spa and views over the Ranipool valley.',
                'rating_score' => 8.9, 'review_count' => 430, 'property_rules' => "Check-in from 2:00 PM, check-out until 11:00 AM.\nValid photo ID required at check-in.",
                'amenities' => ['Free WiFi', 'Restaurant', 'Spa', 'Bar', 'Lake/Mountain View', 'Heater', 'Parking'],
                'room_types' => [
                    ['name' => 'Deluxe Room', 'price' => 10999, 'discounted_price' => 9499, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 300, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Premier Suite', 'price' => 15999, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 420, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Tenzin L.', 'rating' => 9.0, 'location' => 8.9, 'cleanliness' => 9.1, 'service' => 8.9, 'value' => 8.6, 'comment' => 'Beautiful grounds, felt like a proper mountain retreat.', 'days_ago' => 24],
                    ['name' => 'Shreya D.', 'rating' => 8.7, 'location' => 8.6, 'cleanliness' => 8.8, 'service' => 8.6, 'value' => 8.4, 'comment' => 'Good base before heading up to Tsomgo Lake.', 'days_ago' => 47],
                ],
            ],
            ['name' => 'The Elgin Mount Pandim, Pelling', 'destination' => 'Sikkim', 'star_rating' => 4, 'address' => 'Upper Pelling, West Sikkim', 'latitude' => 27.2986, 'longitude' => 88.2384, 'color' => '#15803d',
                'image' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Kanchenjunga%20View%20from%20Pelling%20Hotel.jpg?width=900',
                'description' => 'A colonial-era heritage property in Pelling with unobstructed views of the Kanchenjunga range, set within a working orange orchard.',
                'rating_score' => 8.6, 'review_count' => 260, 'property_rules' => "Check-in from 12:00 PM, check-out until 10:00 AM.\nValid photo ID required at check-in.",
                'amenities' => ['Free WiFi', 'Restaurant', 'Heater', 'Lake/Mountain View', 'Room Service'],
                'room_types' => [
                    ['name' => 'Mountain View Room', 'price' => 7999, 'discounted_price' => 6999, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'Queen Bed', 'size_sqft' => 260, 'meal_plan' => 'breakfast_dinner', 'refundable' => true],
                    ['name' => 'Heritage Room', 'price' => 9999, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 320, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Karma T.', 'rating' => 8.8, 'location' => 9.0, 'cleanliness' => 8.6, 'service' => 8.5, 'value' => 8.4, 'comment' => 'Kanchenjunga view from breakfast was unforgettable on a clear morning.', 'days_ago' => 35],
                    ['name' => 'Ruchi B.', 'rating' => 8.4, 'location' => 8.6, 'cleanliness' => 8.3, 'service' => 8.2, 'value' => 8.2, 'comment' => 'Quiet, homely heritage property away from the crowds.', 'days_ago' => 62],
                ],
            ],
            ['name' => 'Yarlam Resort, Lachung', 'destination' => 'Sikkim', 'star_rating' => 3, 'address' => 'Lachung, North Sikkim', 'latitude' => 27.6893, 'longitude' => 88.7434, 'color' => '#0f766e',
                'image' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Yumthang%20Valley,%20Lachung%20Sikkim.jpg?width=900',
                'description' => 'A simple, cosy mountain lodge in the alpine village of Lachung, the overnight base for visiting the Yumthang Valley and nearby glacial landscapes.',
                'rating_score' => 8.0, 'review_count' => 150, 'property_rules' => "Check-in from 12:00 PM, check-out until 10:00 AM.\nValid photo ID required at check-in.\nRoom heaters provided due to high altitude.",
                'amenities' => ['Restaurant', 'Heater', 'Lake/Mountain View', 'Room Service'],
                'room_types' => [
                    ['name' => 'Standard Room', 'price' => 4999, 'discounted_price' => 4299, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'Twin Bed', 'size_sqft' => 200, 'meal_plan' => 'breakfast_dinner', 'refundable' => true],
                    ['name' => 'Deluxe Room', 'price' => 6499, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'Queen Bed', 'size_sqft' => 240, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Pemba S.', 'rating' => 8.2, 'location' => 8.6, 'cleanliness' => 8.0, 'service' => 8.1, 'value' => 8.3, 'comment' => 'Basic but warm and welcoming, great stop before Yumthang Valley.', 'days_ago' => 40],
                    ['name' => 'Neha J.', 'rating' => 7.8, 'location' => 8.2, 'cleanliness' => 7.7, 'service' => 7.6, 'value' => 8.0, 'comment' => 'Rooms are simple but the heaters worked well in the cold.', 'days_ago' => 65],
                ],
            ],

            // Andaman
            ['name' => 'Fortune Resort Bay Island, Port Blair', 'destination' => 'Andaman & Nicobar Islands', 'star_rating' => 4, 'address' => 'Marine Hill, Port Blair, Andaman & Nicobar Islands', 'latitude' => 11.6234, 'longitude' => 92.7265, 'color' => '#0e7490',
                'description' => 'Perched on its own promontory overlooking the harbour, this is one of Port Blair\'s most established resorts, close to the Cellular Jail.',
                'rating_score' => 8.4, 'review_count' => 520, 'property_rules' => "Check-in from 2:00 PM, check-out until 12:00 PM.\nValid photo ID required at check-in.",
                'amenities' => ['Free WiFi', 'Pool', 'Restaurant', 'Beach Access', 'Airport Transfer', 'Bar'],
                'room_types' => [
                    ['name' => 'Deluxe Room (Sea View)', 'price' => 7999, 'discounted_price' => 6999, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 280, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Premium Room', 'price' => 9999, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 340, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Ashwin V.', 'rating' => 8.5, 'location' => 8.8, 'cleanliness' => 8.4, 'service' => 8.3, 'value' => 8.2, 'comment' => 'Great harbour views and a short drive to the Cellular Jail show.', 'days_ago' => 20],
                    ['name' => 'Pooja N.', 'rating' => 8.2, 'location' => 8.5, 'cleanliness' => 8.1, 'service' => 8.0, 'value' => 8.0, 'comment' => 'Comfortable stopover before heading to Havelock.', 'days_ago' => 44],
                ],
            ],
            ['name' => 'Taj Exotica Resort & Spa, Havelock', 'destination' => 'Andaman & Nicobar Islands', 'star_rating' => 5, 'address' => 'Radhanagar Beach Road, Havelock Island (Swaraj Dweep), Andaman & Nicobar Islands', 'latitude' => 12.0184, 'longitude' => 92.9508, 'color' => '#0891b2',
                'description' => 'A beachfront luxury resort near Radhanagar Beach, with private pool villas, a spa and direct access to Havelock\'s dive sites.',
                'rating_score' => 9.3, 'review_count' => 480, 'property_rules' => "Check-in from 2:00 PM, check-out until 12:00 PM.\nValid photo ID required at check-in.",
                'amenities' => ['Free WiFi', 'Pool', 'Restaurant', 'Spa', 'Beach Access', 'Bar', 'Airport Transfer'],
                'room_types' => [
                    ['name' => 'Beach Villa', 'price' => 22999, 'discounted_price' => 19999, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 600, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Pool Villa', 'price' => 32999, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 800, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Rohan D.', 'rating' => 9.5, 'location' => 9.7, 'cleanliness' => 9.5, 'service' => 9.4, 'value' => 8.9, 'comment' => 'Steps away from one of the best beaches in Asia — worth every rupee.', 'days_ago' => 8],
                    ['name' => 'Kavya S.', 'rating' => 9.1, 'location' => 9.4, 'cleanliness' => 9.2, 'service' => 9.0, 'value' => 8.6, 'comment' => 'Perfect honeymoon stay, the pool villa was stunning.', 'days_ago' => 27],
                ],
            ],
            ['name' => 'Tango Beach Resort, Neil Island', 'destination' => 'Andaman & Nicobar Islands', 'star_rating' => 3, 'address' => 'Bharatpur Beach, Neil Island (Shaheed Dweep), Andaman & Nicobar Islands', 'latitude' => 11.8331, 'longitude' => 93.0475, 'color' => '#0d9488',
                'description' => 'A relaxed beachfront property on Neil Island\'s Bharatpur Beach, with simple cottages and easy access to the island\'s coral reefs.',
                'rating_score' => 8.1, 'review_count' => 180, 'property_rules' => "Check-in from 12:00 PM, check-out until 10:00 AM.\nValid photo ID required at check-in.",
                'amenities' => ['Restaurant', 'Beach Access', 'Parking', 'Room Service'],
                'room_types' => [
                    ['name' => 'Standard Cottage', 'price' => 4999, 'discounted_price' => 4299, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'Queen Bed', 'size_sqft' => 200, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Deluxe Cottage (Beach Facing)', 'price' => 6499, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 260, 'meal_plan' => 'breakfast', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Manoj I.', 'rating' => 8.2, 'location' => 8.6, 'cleanliness' => 8.0, 'service' => 7.9, 'value' => 8.4, 'comment' => 'Simple but right on the beach, loved the laid-back island vibe.', 'days_ago' => 32],
                    ['name' => 'Swati R.', 'rating' => 7.9, 'location' => 8.4, 'cleanliness' => 7.7, 'service' => 7.6, 'value' => 8.1, 'comment' => 'Good value stay, close to Bharatpur\'s glass-bottom boat rides.', 'days_ago' => 55],
                ],
            ],

            // Vietnam
            ['name' => 'Hanoi La Siesta Hotel & Spa', 'destination' => 'Vietnam', 'star_rating' => 4, 'address' => 'Hang Be Street, Old Quarter, Hanoi, Vietnam', 'latitude' => 21.0324, 'longitude' => 105.8523, 'color' => '#b91c1c', 'image' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Hoan%20Kiem%20Lake.jpg?width=900',
                'description' => 'A boutique hotel in Hanoi\'s Old Quarter, with a rooftop restaurant and easy walking access to Hoan Kiem Lake and the night market.',
                'rating_score' => 9.0, 'review_count' => 720, 'property_rules' => "Check-in from 2:00 PM, check-out until 12:00 PM.\nPassport required at check-in.",
                'amenities' => ['Free WiFi', 'Restaurant', 'Spa', 'Room Service', 'AC', 'Airport Transfer'],
                'room_types' => [
                    ['name' => 'Deluxe Room', 'price' => 6499, 'discounted_price' => 5499, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'Queen Bed', 'size_sqft' => 260, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Premium Room', 'price' => 8499, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 320, 'meal_plan' => 'breakfast', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'James L.', 'rating' => 9.2, 'location' => 9.5, 'cleanliness' => 9.1, 'service' => 9.3, 'value' => 8.8, 'comment' => 'Perfectly located to walk the Old Quarter, staff were incredibly helpful.', 'days_ago' => 14],
                    ['name' => 'Aarav M.', 'rating' => 8.8, 'location' => 9.0, 'cleanliness' => 8.7, 'service' => 8.9, 'value' => 8.5, 'comment' => 'Rooftop breakfast overlooking the Old Quarter was a great start each day.', 'days_ago' => 36],
                ],
            ],
            ['name' => 'Paradise Elegance Cruise, Ha Long Bay', 'destination' => 'Vietnam', 'star_rating' => 5, 'address' => 'Tuan Chau Marina, Ha Long Bay, Quang Ninh, Vietnam', 'latitude' => 20.9101, 'longitude' => 107.0522, 'color' => '#0369a1', 'image' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Ha%20Long%20Bay.jpg?width=900',
                'description' => 'An overnight cruise ship exploring the limestone karsts of Ha Long Bay, with en-suite cabins, a sundeck and included kayaking.',
                'rating_score' => 9.2, 'review_count' => 390, 'property_rules' => "Check-in from 11:00 AM (boarding at Tuan Chau Marina), check-out by 11:00 AM the next day.\nPassport required at check-in.",
                'amenities' => ['Free WiFi', 'Restaurant', 'AC', 'Room Service', 'Lake/Mountain View'],
                'room_types' => [
                    ['name' => 'Deluxe Ocean View Cabin', 'price' => 9999, 'discounted_price' => 8499, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'Queen Bed', 'size_sqft' => 200, 'meal_plan' => 'breakfast_dinner', 'refundable' => true],
                    ['name' => 'Suite with Balcony', 'price' => 13999, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 260, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Emily C.', 'rating' => 9.4, 'location' => 9.6, 'cleanliness' => 9.3, 'service' => 9.3, 'value' => 8.9, 'comment' => 'Waking up surrounded by the karsts was one of the best travel moments ever.', 'days_ago' => 9],
                    ['name' => 'Rajesh S.', 'rating' => 9.0, 'location' => 9.3, 'cleanliness' => 9.0, 'service' => 8.9, 'value' => 8.6, 'comment' => 'Kayaking through the limestone caves included in the cruise was a highlight.', 'days_ago' => 29],
                ],
            ],
            ['name' => 'InterContinental Danang Sun Peninsula Resort', 'destination' => 'Vietnam', 'star_rating' => 5, 'address' => 'Bai Bac, Son Tra Peninsula, Da Nang, Vietnam', 'latitude' => 16.1233, 'longitude' => 108.2772, 'color' => '#0e7490', 'image' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Son%20Tra%20Peninsula.jpg?width=900',
                'description' => 'A cliffside resort on the Son Tra Peninsula near Da Nang, designed by Bill Bensley, with private beach coves and views over the South China Sea — a comfortable base for Hoi An and Ba Na Hills day trips.',
                'rating_score' => 9.4, 'review_count' => 610, 'property_rules' => "Check-in from 2:00 PM, check-out until 12:00 PM.\nPassport required at check-in.",
                'amenities' => ['Free WiFi', 'Pool', 'Restaurant', 'Spa', 'Beach Access', 'Bar', 'Airport Transfer'],
                'room_types' => [
                    ['name' => 'Ocean View Room', 'price' => 18999, 'discounted_price' => 16499, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 450, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Peninsula Suite', 'price' => 27999, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 650, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Sophie W.', 'rating' => 9.6, 'location' => 9.5, 'cleanliness' => 9.6, 'service' => 9.5, 'value' => 9.0, 'comment' => 'The private cove beach felt completely secluded, exceptional service throughout.', 'days_ago' => 6],
                    ['name' => 'Manish A.', 'rating' => 9.2, 'location' => 9.3, 'cleanliness' => 9.2, 'service' => 9.1, 'value' => 8.7, 'comment' => 'Perfect base for Hoi An and Ba Na Hills — architecture is stunning.', 'days_ago' => 31],
                ],
            ],

            // Malaysia
            ['name' => 'Sunway Resort Hotel, Kuala Lumpur', 'destination' => 'Malaysia', 'star_rating' => 5, 'address' => 'Bandar Sunway, Petaling Jaya, Kuala Lumpur, Malaysia', 'latitude' => 3.0654, 'longitude' => 101.6068, 'color' => '#7c3aed',
                'description' => 'A large integrated resort next to Sunway Lagoon theme park and Sunway Pyramid mall, with multiple pools and easy access to Kuala Lumpur city centre.',
                'rating_score' => 8.9, 'review_count' => 1450, 'property_rules' => "Check-in from 3:00 PM, check-out until 12:00 PM.\nPassport required at check-in.",
                'amenities' => ['Free WiFi', 'Pool', 'Restaurant', 'Gym', 'Bar', 'Airport Transfer', 'Room Service'],
                'room_types' => [
                    ['name' => 'Deluxe Room', 'price' => 9999, 'discounted_price' => 8499, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 320, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Premier Room', 'price' => 13999, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 400, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Grace T.', 'rating' => 9.0, 'location' => 9.1, 'cleanliness' => 9.0, 'service' => 8.9, 'value' => 8.6, 'comment' => 'Kids loved being right next to Sunway Lagoon, pools were fantastic.', 'days_ago' => 16],
                    ['name' => 'Harish P.', 'rating' => 8.7, 'location' => 8.8, 'cleanliness' => 8.7, 'service' => 8.6, 'value' => 8.4, 'comment' => 'Easy taxi ride to Petronas Towers and Batu Caves.', 'days_ago' => 41],
                ],
            ],
            ['name' => 'Genting Grand, Resorts World Genting', 'destination' => 'Malaysia', 'star_rating' => 5, 'address' => 'Resorts World Genting, Genting Highlands, Pahang, Malaysia', 'latitude' => 3.4227, 'longitude' => 101.7935, 'color' => '#6d28d9',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/Genting_Grand_Hotel.jpg/960px-Genting_Grand_Hotel.jpg',
                'description' => 'A hilltop resort at Genting Highlands, connected to the SkyWay cable car station, casino and theme parks, with cool mountain-top weather year-round.',
                'rating_score' => 8.5, 'review_count' => 980, 'property_rules' => "Check-in from 3:00 PM, check-out until 12:00 PM.\nPassport required at check-in.",
                'amenities' => ['Free WiFi', 'Restaurant', 'Bar', 'Gym', 'Room Service', 'Lake/Mountain View'],
                'room_types' => [
                    ['name' => 'Grand Deluxe Room', 'price' => 8999, 'discounted_price' => 7499, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 300, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Premier Room', 'price' => 11999, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 360, 'meal_plan' => 'breakfast', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Chong W.', 'rating' => 8.6, 'location' => 9.0, 'cleanliness' => 8.6, 'service' => 8.4, 'value' => 8.2, 'comment' => 'Cool weather up top was a welcome break, SkyWay is right there.', 'days_ago' => 21],
                    ['name' => 'Isha K.', 'rating' => 8.3, 'location' => 8.7, 'cleanliness' => 8.3, 'service' => 8.1, 'value' => 8.0, 'comment' => 'Great for families with the theme parks so close by.', 'days_ago' => 48],
                ],
            ],
            ['name' => 'Berjaya Langkawi Resort', 'destination' => 'Malaysia', 'star_rating' => 4, 'address' => 'Burau Bay, Langkawi, Kedah, Malaysia', 'latitude' => 6.3178, 'longitude' => 99.7031, 'color' => '#9333ea',
                'image' => 'https://images.unsplash.com/photo-1753190550747-c56d10ff6d35?w=900&q=80',
                'description' => 'Overwater and hillside chalets set in a rainforest reserve on Burau Bay, Langkawi, with a private beach and easy access to the Sky Bridge cable car.',
                'rating_score' => 8.6, 'review_count' => 720, 'property_rules' => "Check-in from 3:00 PM, check-out until 12:00 PM.\nPassport required at check-in.",
                'amenities' => ['Free WiFi', 'Pool', 'Restaurant', 'Beach Access', 'Bar', 'Room Service'],
                'room_types' => [
                    ['name' => 'Hillside Chalet', 'price' => 9499, 'discounted_price' => 7999, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 340, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Overwater Chalet', 'price' => 15999, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 420, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Farid H.', 'rating' => 8.8, 'location' => 9.0, 'cleanliness' => 8.7, 'service' => 8.6, 'value' => 8.4, 'comment' => 'Overwater chalet with sunset views was worth the upgrade.', 'days_ago' => 19],
                    ['name' => 'Trisha N.', 'rating' => 8.4, 'location' => 8.6, 'cleanliness' => 8.3, 'service' => 8.2, 'value' => 8.2, 'comment' => 'Rainforest setting is gorgeous, monkeys visit in the morning!', 'days_ago' => 44],
                ],
            ],

            // Dubai
            ['name' => 'Grand Millennium Dubai', 'destination' => 'Dubai', 'star_rating' => 4, 'address' => 'Sheikh Zayed Road, Barsha Heights, Dubai, United Arab Emirates', 'latitude' => 25.1124, 'longitude' => 55.1990, 'color' => '#b45309',
                'image' => 'https://images.unsplash.com/photo-1669463827790-0fa8a233221e?w=900&q=80',
                'description' => 'A centrally located hotel on Sheikh Zayed Road with easy Metro access to Downtown Dubai, the Burj Khalifa and Dubai Mall.',
                'rating_score' => 8.6, 'review_count' => 1620, 'property_rules' => "Check-in from 3:00 PM, check-out until 12:00 PM.\nPassport required at check-in.",
                'amenities' => ['Free WiFi', 'Pool', 'Restaurant', 'Gym', 'Bar', 'Airport Transfer', 'Room Service'],
                'room_types' => [
                    ['name' => 'Deluxe Room', 'price' => 8999, 'discounted_price' => 7499, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'King Bed', 'size_sqft' => 320, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Premier Room', 'price' => 11999, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 400, 'meal_plan' => 'breakfast', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Omar F.', 'rating' => 8.7, 'location' => 8.8, 'cleanliness' => 8.7, 'service' => 8.6, 'value' => 8.4, 'comment' => 'Metro station right outside made getting to the Burj Khalifa effortless.', 'days_ago' => 17],
                    ['name' => 'Reema A.', 'rating' => 8.4, 'location' => 8.5, 'cleanliness' => 8.4, 'service' => 8.3, 'value' => 8.2, 'comment' => 'Comfortable rooms and a good breakfast spread.', 'days_ago' => 39],
                ],
            ],
            ['name' => 'Atlantis The Palm', 'destination' => 'Dubai', 'star_rating' => 5, 'address' => 'Crescent Road, Palm Jumeirah, Dubai, United Arab Emirates', 'latitude' => 25.1305, 'longitude' => 55.1173, 'color' => '#c2410c',
                'description' => 'An iconic resort at the apex of Palm Jumeirah, with a private beach, the Aquaventure Waterpark and an underwater suite view into The Lost Chambers Aquarium — offered as a premium optional upgrade stay.',
                'rating_score' => 9.3, 'review_count' => 2100, 'property_rules' => "Check-in from 3:00 PM, check-out until 12:00 PM.\nPassport required at check-in.",
                'amenities' => ['Free WiFi', 'Pool', 'Restaurant', 'Beach Access', 'Spa', 'Bar', 'Airport Transfer'],
                'room_types' => [
                    ['name' => 'Ocean Deluxe Room', 'price' => 32999, 'discounted_price' => 28999, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 500, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Imperial Club Room', 'price' => 45999, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 620, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Zainab M.', 'rating' => 9.5, 'location' => 9.6, 'cleanliness' => 9.5, 'service' => 9.4, 'value' => 8.8, 'comment' => 'Aquaventure Waterpark access made this the highlight of our whole trip.', 'days_ago' => 5],
                    ['name' => 'Karan L.', 'rating' => 9.1, 'location' => 9.3, 'cleanliness' => 9.1, 'service' => 9.0, 'value' => 8.5, 'comment' => 'Splurged for the upgrade and it was completely worth it for the views alone.', 'days_ago' => 26],
                ],
            ],

            // Singapore
            ['name' => 'Ibis Singapore on Bencoolen', 'destination' => 'Singapore', 'star_rating' => 3, 'address' => 'Bencoolen Street, Bugis, Singapore', 'latitude' => 1.2989, 'longitude' => 103.8517, 'color' => '#e11d48',
                'description' => 'A well-connected budget-friendly hotel in the Bugis area, a short MRT ride from Gardens by the Bay, Sentosa and Orchard Road.',
                'rating_score' => 8.3, 'review_count' => 1980, 'property_rules' => "Check-in from 2:00 PM, check-out until 12:00 PM.\nPassport required at check-in.",
                'amenities' => ['Free WiFi', 'Restaurant', 'AC', 'Room Service', '24hr Front Desk'],
                'room_types' => [
                    ['name' => 'Standard Room', 'price' => 8999, 'discounted_price' => 7499, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'Queen Bed', 'size_sqft' => 200, 'meal_plan' => 'room_only', 'refundable' => true],
                    ['name' => 'Superior Room', 'price' => 10499, 'discounted_price' => 9499, 'occupancy_adults' => 2, 'occupancy_children' => 1, 'bed_type' => 'Queen Bed', 'size_sqft' => 240, 'meal_plan' => 'breakfast', 'refundable' => true],
                ],
                'reviews' => [
                    ['name' => 'Wei L.', 'rating' => 8.4, 'location' => 8.8, 'cleanliness' => 8.3, 'service' => 8.1, 'value' => 8.5, 'comment' => 'Great value, MRT station right around the corner.', 'days_ago' => 13],
                    ['name' => 'Anushka D.', 'rating' => 8.1, 'location' => 8.5, 'cleanliness' => 8.0, 'service' => 7.9, 'value' => 8.3, 'comment' => 'Clean, compact rooms, easy access to everywhere we wanted to see.', 'days_ago' => 34],
                ],
            ],
            ['name' => 'Marina Bay Sands', 'destination' => 'Singapore', 'star_rating' => 5, 'address' => '10 Bayfront Avenue, Marina Bay, Singapore', 'latitude' => 1.2834, 'longitude' => 103.8607, 'color' => '#dc2626',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/33/Marina_Bay_Sands_Hotel_3_%2831345110894%29.jpg/960px-Marina_Bay_Sands_Hotel_3_%2831345110894%29.jpg',
                'description' => 'Singapore\'s most iconic hotel, topped by the world-famous Infinity Pool, overlooking Marina Bay and Gardens by the Bay — offered as a premium optional upgrade stay.',
                'rating_score' => 9.2, 'review_count' => 3400, 'property_rules' => "Check-in from 3:00 PM, check-out until 11:00 AM.\nPassport required at check-in.",
                'amenities' => ['Free WiFi', 'Pool', 'Restaurant', 'Spa', 'Gym', 'Bar', 'Airport Transfer'],
                'room_types' => [
                    ['name' => 'Deluxe Room', 'price' => 42999, 'discounted_price' => 37999, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 450, 'meal_plan' => 'breakfast', 'refundable' => true],
                    ['name' => 'Club Room', 'price' => 55999, 'discounted_price' => null, 'occupancy_adults' => 2, 'occupancy_children' => 2, 'bed_type' => 'King Bed', 'size_sqft' => 550, 'meal_plan' => 'breakfast_dinner', 'refundable' => false],
                ],
                'reviews' => [
                    ['name' => 'Michelle T.', 'rating' => 9.4, 'location' => 9.6, 'cleanliness' => 9.4, 'service' => 9.3, 'value' => 8.7, 'comment' => 'The Infinity Pool view over the skyline is every bit as good as the photos.', 'days_ago' => 4],
                    ['name' => 'Arjun B.', 'rating' => 9.0, 'location' => 9.3, 'cleanliness' => 9.0, 'service' => 8.9, 'value' => 8.4, 'comment' => 'Splurged for one night and it made the whole trip feel special.', 'days_ago' => 22],
                ],
            ],
        ];

        foreach ($hotels as $data) {
            $this->seedHotel($data);
        }
    }

    // Lighter-weight than seedHotel() above — these exist purely so homepage cards
    // (Homes Guests Love / Top Unique Properties / Weekend Deals) have a real hotel
    // detail page to link to; one room type is enough for hotels.show to render.
    private function seedSimpleHotel(array $data): void
    {
        $destination = Destination::where('name', $data['destination'])->first();

        $hotel = Hotel::firstOrCreate(
            ['name' => $data['name']],
            [
                'unique_id'      => SiteTenant::id(),
                'destination_id' => $destination?->id,
                'slug'           => Str::slug($data['name']),
                'star_rating'    => $data['star_rating'],
                'property_type'  => $data['property_type'],
                'address'        => $data['address'],
                'check_in_time'  => '14:00',
                'check_out_time' => '11:00',
                'description'    => "Discover a comfortable stay at {$data['name']}.",
                'rating_score'   => $data['rating_score'],
                'review_count'   => $data['review_count'],
                'status'         => 'active',
                'sort_order'     => 0,
            ]
        );

        // Always resync to the real photo — these hotels previously only had a
        // generated color-block placeholder (no actual photo of the property).
        $hotel->update(['cover_image' => $data['image']]);

        if ($hotel->roomTypes()->doesntExist()) {
            $hotel->roomTypes()->create([
                'name' => 'Standard Room',
                'price' => $data['price'],
                'discounted_price' => null,
                'occupancy_adults' => 2,
                'occupancy_children' => 1,
                'bed_type' => 'Queen Bed',
                'size_sqft' => 260,
                'meal_plan' => 'room_only',
                'refundable' => true,
                'sort_order' => 0,
                'images' => [$data['image']],
            ]);
        } else {
            $hotel->roomTypes()->update(['images' => [$data['image']]]);
        }
    }

    private function seedHotel(array $data): void
    {
        $destination = Destination::where('name', $data['destination'])->first();

        $hotel = Hotel::firstOrCreate(
            ['name' => $data['name']],
            [
                'unique_id'       => SiteTenant::id(),
                'destination_id'  => $destination?->id,
                'slug'            => Str::slug($data['name']),
                'star_rating'     => $data['star_rating'],
                'address'         => $data['address'],
                'latitude'        => $data['latitude'],
                'longitude'       => $data['longitude'],
                'check_in_time'   => '14:00',
                'check_out_time'  => '11:00',
                'property_rules'  => $data['property_rules'],
                'description'     => $data['description'],
                'rating_score'    => $data['rating_score'],
                'review_count'    => $data['review_count'],
                'status'          => 'active',
                'sort_order'      => 0,
            ]
        );

        // firstOrCreate() only fills these on the very first insert — a hotel that
        // already existed before these columns were added (e.g. the original demo
        // "Taj Vivanta Goa" row) would otherwise be stuck with them permanently empty.
        if (!$hotel->destination_id && $destination) {
            $hotel->update(['destination_id' => $destination->id]);
        }
        if (!$hotel->latitude && !$hotel->longitude) {
            $hotel->update(['latitude' => $data['latitude'], 'longitude' => $data['longitude']]);
        }
        if (!$hotel->check_in_time && !$hotel->check_out_time) {
            $hotel->update(['check_in_time' => '14:00', 'check_out_time' => '11:00']);
        }
        if (!$hotel->property_rules) {
            $hotel->update(['property_rules' => $data['property_rules']]);
        }

        $amenityIds = Amenity::whereIn('name', $data['amenities'])->pluck('id');
        $hotel->amenities()->sync($amenityIds);

        if ($hotel->roomTypes()->doesntExist()) {
            foreach ($data['room_types'] as $index => $room) {
                $hotel->roomTypes()->create(array_merge($room, [
                    'sort_order' => $index,
                    'images'     => [$this->placeholderImage(700, 460, $room['name'], 'hotels/rooms', $data['color'])],
                ]));
            }
        }

        if ($hotel->reviews()->doesntExist()) {
            foreach ($data['reviews'] as $index => $review) {
                $hotel->reviews()->create([
                    'reviewer_name'      => $review['name'],
                    'rating'             => $review['rating'],
                    'location_rating'    => $review['location'],
                    'cleanliness_rating' => $review['cleanliness'],
                    'service_rating'     => $review['service'],
                    'value_rating'       => $review['value'],
                    'comment'            => $review['comment'],
                    'review_date'        => now()->subDays($review['days_ago']),
                    'verified'           => true,
                    'sort_order'         => $index,
                ]);
            }
        }

        if (isset($data['image'])) {
            // A real photo is available for this hotel — always resync to it,
            // overwriting any earlier generated color-block placeholder.
            $hotel->update(['cover_image' => $data['image']]);
        } elseif (!$hotel->cover_image) {
            $hotel->update([
                'cover_image' => $this->placeholderImage(900, 550, $hotel->name, 'hotels', $data['color']),
            ]);
        }

        if (empty($hotel->gallery_images)) {
            $hotel->update([
                'gallery_images' => [
                    $this->placeholderImage(800, 500, $hotel->name.' Pool', 'hotels/gallery', $data['color']),
                    $this->placeholderImage(800, 500, $hotel->name.' Lobby', 'hotels/gallery', $data['color']),
                    $this->placeholderImage(800, 500, $hotel->name.' Room', 'hotels/gallery', $data['color']),
                ],
            ]);
        }
    }
}
