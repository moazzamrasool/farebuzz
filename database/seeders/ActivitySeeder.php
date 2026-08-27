<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityCategory;
use App\Models\Destination;
use App\Support\SiteTenant;
use App\Traits\GeneratesUniqueSlug;
use Database\Seeders\Concerns\GeneratesDemoImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ActivitySeeder extends Seeder
{
    use GeneratesDemoImages, GeneratesUniqueSlug;

    // Same names/icons as the activities:backfill-categories command's known set.
    private const CATEGORY_ICONS = [
        'water sports' => 'bi-water',
        'sightseeing'  => 'bi-binoculars-fill',
        'adventure'    => 'bi-lightning-charge-fill',
        'wildlife'     => 'bi-tree-fill',
        'beaches'      => 'bi-umbrella-fill',
    ];

    private array $activityCategoryCache = [];

    private function resolveActivityCategoryId(string $name, ?string $uniqueId): int
    {
        $cacheKey = ($uniqueId ?? '').'|'.$name;
        if (isset($this->activityCategoryCache[$cacheKey])) {
            return $this->activityCategoryCache[$cacheKey];
        }

        $category = ActivityCategory::withoutGlobalScopes()
            ->where('unique_id', $uniqueId)
            ->where('name', $name)
            ->first();

        if (!$category) {
            $category = ActivityCategory::withoutGlobalScopes()->forceCreate([
                'unique_id'  => $uniqueId,
                'name'       => $name,
                'slug'       => $this->generateUniqueSlug(ActivityCategory::class, $name),
                'icon'       => self::CATEGORY_ICONS[strtolower($name)] ?? null,
                'status'     => 'active',
                'sort_order' => 0,
            ]);
        }

        return $this->activityCategoryCache[$cacheKey] = $category->id;
    }

    public function run(): void
    {
        $goa      = Destination::where('slug', 'goa')->first();
        $maldives = Destination::where('slug', 'maldives')->first();
        $manali   = Destination::where('slug', 'manali')->first();

        $activities = [
            [
                'name' => 'Scuba Diving', 'destination' => $goa, 'category' => 'Water Sports', 'price' => 2500,
                'description' => 'Explore vibrant reefs and shipwrecks on a guided scuba dive, no experience necessary — a certified instructor takes you through basics before you descend.',
                'photo' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=900&q=75',
            ],
            [
                'name' => 'Sunset Cruise', 'destination' => $goa, 'category' => 'Sightseeing', 'price' => 1500,
                'description' => 'Sail out on the Arabian Sea as the sky turns gold, with music, snacks and a front-row seat to Goa\'s famous sunset.',
                'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/56/1.31.14_West_Coast_Club_Cruise_%2838%29_%2812482855033%29.jpg/960px-1.31.14_West_Coast_Club_Cruise_%2838%29_%2812482855033%29.jpg',
            ],
            [
                'name' => 'Snorkeling', 'destination' => $maldives, 'category' => 'Water Sports', 'price' => 3000,
                'description' => 'Glide over shallow coral gardens teeming with reef fish — a gentle, beginner-friendly way to see Maldivian marine life up close.',
                'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/14/Elkhorn_Coral_Biscayne_NP1.jpg/960px-Elkhorn_Coral_Biscayne_NP1.jpg',
            ],
            [
                'name' => 'Paragliding', 'destination' => $manali, 'category' => 'Adventure', 'price' => 3500,
                'description' => 'Launch off a Himalayan ridge and soar over the Solang Valley on a tandem paraglide with a trained pilot — pure adrenaline, unbeatable views.',
                'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Paragliding%2C_Cape_Town_%28P1050339%29.jpg/960px-Paragliding%2C_Cape_Town_%28P1050339%29.jpg',
            ],
            [
                'name' => 'River Rafting', 'destination' => $manali, 'category' => 'Adventure', 'price' => 1800,
                'description' => 'Paddle through the rapids of the Beas River with safety gear and an experienced rafting crew — a fast, splashy ride through the mountains.',
                'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fc/Whitewater_Rafting_Surfing.jpg/960px-Whitewater_Rafting_Surfing.jpg',
            ],
            [
                'name' => 'Sunrise Beach Walk', 'destination' => $goa, 'category' => 'Sightseeing', 'price' => null,
                'description' => 'Start the day with a quiet walk along the shore as the sun comes up over the water — free, unhurried, and a Goa Beach Escape favourite.',
                'photo' => 'https://images.unsplash.com/photo-1519046904884-53103b34b206?w=900&q=75',
            ],
            [
                'name' => 'Old Goa Heritage Tour', 'destination' => $goa, 'category' => 'Sightseeing', 'price' => 800,
                'description' => 'Walk through Old Goa\'s UNESCO-listed churches and convents, including the Basilica of Bom Jesus, with a local guide sharing 500 years of Portuguese-era history.',
                'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Front_Elevation_of_Basilica_of_Bom_Jesus.jpg/960px-Front_Elevation_of_Basilica_of_Bom_Jesus.jpg',
            ],
            [
                'name' => 'Water Sports', 'destination' => $goa, 'category' => 'Water Sports', 'price' => 2000,
                'description' => 'A mixed session of jet-skiing, banana boat rides and parasailing on Goa\'s beaches — book once, try it all.',
                'photo' => 'https://images.unsplash.com/photo-1502680390469-be75c86b636f?w=900&q=75',
            ],
            [
                'name' => 'Goa Nightlife', 'destination' => $goa, 'category' => 'Sightseeing', 'price' => null,
                'description' => 'Hit Goa\'s best-known beach shacks and clubs on a guided night out — included in the Goa Beach Escape package.',
                'photo' => 'https://images.unsplash.com/photo-1566737236500-c8ac43014a67?w=900&q=75',
            ],
            [
                'name' => 'Spice Plantation Visit', 'destination' => $goa, 'category' => 'Sightseeing', 'price' => 600,
                'description' => 'Tour a working spice plantation, see cardamom, pepper and nutmeg growing up close, and finish with a traditional Goan lunch.',
                'photo' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=900&q=75',
            ],
        ];

        foreach ($activities as $index => $activity) {
            $model = Activity::updateOrCreate(
                ['slug' => Str::slug($activity['name'])],
                [
                    'destination_id'       => $activity['destination']?->id,
                    'name'                 => $activity['name'],
                    'activity_category_id' => $this->resolveActivityCategoryId($activity['category'], null),
                    'description'          => $activity['description'],
                    'price'                => $activity['price'],
                    'status'               => 'active',
                    'sort_order'           => $index,
                ]
            );

            // Replace any previously seeded placeholder/broken image with a real photo.
            $newImage = $this->downloadImage($activity['photo'], 'activities')
                ?? $this->placeholderImage(900, 600, $activity['name'], 'activities', '#f47b20');

            if ($model->image && $model->image !== $newImage) {
                Storage::disk('public')->delete($model->image);
            }

            $model->update(['image' => $newImage]);
        }

        $this->realCatalogueActivities();
    }

    // The real, named activities backing the 14-package SEO catalogue — matches the
    // exact experiences called out per destination in the product brief. Slug-keyed
    // via updateOrCreate() so re-seeding never duplicates rows.
    private function realCatalogueActivities(): void
    {
        $destinations = Destination::whereIn('slug', [
            'kashmir', 'himachal-pradesh', 'kerala', 'sikkim', 'andaman', 'vietnam', 'malaysia', 'dubai', 'singapore',
        ])->get()->keyBy('slug');

        $activities = [
            // Kashmir
            ['name' => 'Shikara Ride on Dal Lake', 'destination' => 'kashmir', 'category' => 'Sightseeing', 'price' => 700, 'duration' => '1 hour', 'commons' => 'Shikara.jpg', 'description' => 'Glide across Dal Lake in a traditional wooden shikara, past floating gardens and houseboats, with the Zabarwan hills as a backdrop.'],
            ['name' => 'Gulmarg Gondola', 'destination' => 'kashmir', 'category' => 'Adventure', 'price' => 1800, 'duration' => '2-3 hours', 'commons' => 'Gulmarg.jpg', 'description' => 'Ride the world\'s second-highest cable car up to Apharwat Peak for panoramic views of the Pir Panjal range and (in winter) fresh powder snow.'],
            ['name' => 'Betaab Valley Sightseeing', 'destination' => 'kashmir', 'category' => 'Sightseeing', 'price' => 300, 'duration' => '2 hours', 'commons' => 'Betaab Valley.jpg', 'description' => 'Walk through the pine-forested Betaab Valley near Pahalgam, named after the Bollywood film shot here, with the Lidder River running through it.'],
            ['name' => 'Thajiwas Glacier Trek', 'destination' => 'kashmir', 'category' => 'Adventure', 'price' => 900, 'duration' => 'Half day', 'commons' => 'Thajiwas Glacier.jpg', 'description' => 'A pony or on-foot trek from Sonmarg to the Thajiwas Glacier, a permanently snow-covered patch surrounded by meadows and streams.'],

            // Himachal Pradesh
            ['name' => 'Solang Valley Adventure Activities', 'destination' => 'himachal-pradesh', 'category' => 'Adventure', 'price' => 1500, 'duration' => 'Half day', 'commons' => 'Solang Valley.jpg', 'description' => 'A hub for paragliding, zorbing, snow scooters and (in winter) skiing, framed by the peaks above Manali.'],
            ['name' => 'Atal Tunnel & Rohtang Pass Excursion', 'destination' => 'himachal-pradesh', 'category' => 'Sightseeing', 'price' => 1200, 'duration' => 'Full day', 'commons' => 'Atal Tunnel.jpg', 'description' => 'Drive through the Atal Tunnel, one of the world\'s longest high-altitude tunnels, en route to the snow-covered Rohtang Pass (permit required, subject to weather).'],
            ['name' => 'Hadimba Temple Visit', 'destination' => 'himachal-pradesh', 'category' => 'Sightseeing', 'price' => null, 'duration' => '1 hour', 'commons' => 'Hadimba Temple.jpg', 'description' => 'A 16th-century wooden cave temple set inside a cedar forest in Old Manali, dedicated to Hidimba Devi from the Mahabharata.'],
            ['name' => 'Mall Road Shimla Walk', 'destination' => 'himachal-pradesh', 'category' => 'Sightseeing', 'price' => null, 'duration' => '2 hours', 'commons' => 'Mall Road, Shimla.jpg', 'description' => 'Stroll Shimla\'s pedestrian-only colonial-era Mall Road and the Ridge, lined with cafes, shops and views of the Christ Church.'],

            // Kerala
            ['name' => 'Alleppey Houseboat Cruise', 'destination' => 'kerala', 'category' => 'Sightseeing', 'price' => 4500, 'duration' => 'Overnight', 'commons' => 'Alleppey.jpg', 'description' => 'Cruise the palm-fringed backwaters of Alleppey aboard a traditional kettuvallam houseboat, with meals cooked on board.'],
            ['name' => 'Munnar Tea Plantation Tour', 'destination' => 'kerala', 'category' => 'Sightseeing', 'price' => 500, 'duration' => '2-3 hours', 'commons' => 'Munnar.jpg', 'description' => 'Walk through Munnar\'s rolling tea estates and visit a working tea museum to see how the leaf becomes the cup.'],
            ['name' => 'Periyar Lake Boat Safari', 'destination' => 'kerala', 'category' => 'Wildlife', 'price' => 600, 'duration' => '2 hours', 'commons' => 'Periyar Tiger Reserve.jpg', 'description' => 'A boat cruise on Periyar Lake inside Thekkady\'s tiger reserve, with a good chance of spotting elephants, bison and birdlife on the shore.'],
            ['name' => 'Kathakali Dance Show', 'destination' => 'kerala', 'category' => 'Sightseeing', 'price' => 400, 'duration' => '1.5 hours', 'commons' => 'Kathakali.jpg', 'description' => 'Watch performers apply their elaborate face paint before a live Kathakali performance, Kerala\'s classical dance-drama form.'],
            ['name' => 'Fort Kochi Chinese Fishing Nets Walk', 'destination' => 'kerala', 'category' => 'Sightseeing', 'price' => null, 'duration' => '2 hours', 'commons' => 'Chinese fishing nets, Kochi.jpg', 'description' => 'Wander Fort Kochi\'s waterfront to see the giant cantilevered Chinese fishing nets, a legacy of 14th-century Chinese traders.'],

            // Sikkim
            ['name' => 'Tsomgo Lake Excursion', 'destination' => 'sikkim', 'category' => 'Sightseeing', 'price' => 1000, 'duration' => 'Half day', 'commons' => 'Tsomgo Lake.jpg', 'description' => 'A glacial lake at nearly 12,400 ft near Gangtok, often partly frozen in winter, reached by a scenic mountain drive (permit required).'],
            ['name' => 'Baba Mandir Visit', 'destination' => 'sikkim', 'category' => 'Sightseeing', 'price' => null, 'duration' => '1 hour', 'commons' => 'Baba Harbhajan Singh Memorial.jpg', 'description' => 'A revered shrine dedicated to Baba Harbhajan Singh near the India-China border, visited alongside Tsomgo Lake and Nathula Pass.'],
            ['name' => 'Nathula Pass Permit Trip', 'destination' => 'sikkim', 'category' => 'Adventure', 'price' => 1800, 'duration' => 'Full day', 'commons' => 'Nathu La.jpg', 'description' => 'A high-altitude trip to the Indo-China border trade route at 14,140 ft, requiring a special protected-area permit arranged in advance.'],
            ['name' => 'Rumtek Monastery Tour', 'destination' => 'sikkim', 'category' => 'Sightseeing', 'price' => null, 'duration' => '1.5 hours', 'commons' => 'Rumtek Monastery entrance.jpg', 'description' => 'One of Sikkim\'s largest and most important Buddhist monasteries, seat-in-exile of the Karmapa Lama, overlooking the Gangtok valley.'],

            // Andaman
            ['name' => 'Cellular Jail Light & Sound Show', 'destination' => 'andaman', 'category' => 'Sightseeing', 'price' => 200, 'duration' => '1 hour', 'commons' => 'Cellular Jail, Port Blair.jpg', 'description' => 'An evening light-and-sound retelling of the freedom struggle inside the colonial-era Cellular Jail in Port Blair.'],
            ['name' => 'Radhanagar Beach Visit', 'destination' => 'andaman', 'category' => 'Beaches', 'price' => null, 'duration' => 'Half day', 'commons' => 'Radhanagar Beach.jpg', 'description' => 'Havelock\'s (Swaraj Dweep\'s) powder-white Radhanagar Beach, repeatedly ranked among Asia\'s best beaches.'],
            ['name' => 'Havelock Scuba Diving & Snorkelling', 'destination' => 'andaman', 'category' => 'Water Sports', 'price' => 4500, 'duration' => '3-4 hours', 'commons' => 'Scuba diving.jpg', 'description' => 'Dive or snorkel Havelock\'s coral reefs, home to some of the clearest waters and richest marine life in the Andamans.'],
            ['name' => 'Ross & North Bay Island Tour', 'destination' => 'andaman', 'category' => 'Sightseeing', 'price' => 1200, 'duration' => 'Half day', 'commons' => 'Ross Island.jpg', 'description' => 'A boat trip to the former British administrative headquarters on Ross Island, now reclaimed by forest and deer, plus watersports at North Bay.'],

            // Vietnam
            ['name' => 'Ha Long Bay Cruise', 'destination' => 'vietnam', 'category' => 'Sightseeing', 'price' => 6500, 'duration' => 'Full/overnight', 'commons' => 'Ha Long Bay.jpg', 'description' => 'Cruise among thousands of limestone karsts and islets rising from the emerald waters of this UNESCO World Heritage bay.'],
            ['name' => 'Cu Chi Tunnels Tour', 'destination' => 'vietnam', 'category' => 'Sightseeing', 'price' => 1800, 'duration' => 'Half day', 'commons' => 'Cu Chi Tunnels Vietnam war.jpg', 'description' => 'Explore the underground tunnel network used by Viet Cong guerrillas during the Vietnam War, with a guide explaining the tactics and history.'],
            ['name' => 'Ba Na Hills Golden Bridge Tour', 'destination' => 'vietnam', 'category' => 'Sightseeing', 'price' => 2200, 'duration' => 'Full day', 'commons' => 'Golden Bridge at Ba Na Hills 20250718.jpg', 'description' => 'Ride a cable car up to Ba Na Hills near Da Nang to walk across the famous stone "hands" of the Golden Bridge, with the French Village and gardens nearby.'],
            ['name' => 'Hoi An Ancient Town Walk', 'destination' => 'vietnam', 'category' => 'Sightseeing', 'price' => null, 'duration' => '2-3 hours', 'commons' => 'Hoi An.jpg', 'description' => 'Wander Hoi An\'s UNESCO-listed lantern-lit streets, the Japanese Covered Bridge, and its famous tailor shops along the Thu Bon River.'],

            // Malaysia
            ['name' => 'Petronas Towers Observation Deck', 'destination' => 'malaysia', 'category' => 'Sightseeing', 'price' => 3200, 'duration' => '1.5 hours', 'commons' => 'Petronas Towers.jpg', 'description' => 'Cross the Skybridge and ride to the observation deck of the iconic 88-storey Petronas Twin Towers in downtown Kuala Lumpur.'],
            ['name' => 'Batu Caves Tour', 'destination' => 'malaysia', 'category' => 'Sightseeing', 'price' => null, 'duration' => '2 hours', 'commons' => 'Batu Caves.jpg', 'description' => 'Climb the 272 rainbow steps to this limestone cave temple, home to a giant golden statue of Lord Murugan and resident macaques.'],
            ['name' => 'Genting SkyWay Cable Car', 'destination' => 'malaysia', 'category' => 'Adventure', 'price' => 1400, 'duration' => '1 hour', 'commons' => 'Genting Highlands.jpg', 'description' => 'One of the fastest cable car systems in the world, climbing through cloud forest to the hilltop resort of Genting Highlands.'],
            ['name' => 'Langkawi Sky Bridge', 'destination' => 'malaysia', 'category' => 'Adventure', 'price' => 1800, 'duration' => '2-3 hours', 'commons' => 'Langkawi Sky Bridge.jpg', 'description' => 'A curved 125-metre pedestrian bridge suspended near the summit of Gunung Mat Cincang, reached by cable car, with views across the Andaman Sea.'],

            // Dubai
            ['name' => 'Desert Safari with BBQ Dinner', 'destination' => 'dubai', 'category' => 'Adventure', 'price' => 5500, 'duration' => '5-6 hours', 'commons' => 'Desert safari.jpg', 'description' => 'Dune bashing in a 4x4, camel riding, sandboarding and a BBQ dinner with live entertainment at a desert camp under the stars.'],
            ['name' => 'Burj Khalifa At The Top', 'destination' => 'dubai', 'category' => 'Sightseeing', 'price' => 4500, 'duration' => '1.5 hours', 'commons' => 'Burj Khalifa.jpg', 'description' => 'Ride to the observation deck of the world\'s tallest building for 360-degree views over Downtown Dubai and the Gulf.'],
            ['name' => 'Dhow Cruise Dinner', 'destination' => 'dubai', 'category' => 'Sightseeing', 'price' => 3500, 'duration' => '2 hours', 'commons' => 'Dhow.jpg', 'description' => 'A traditional wooden dhow cruise along Dubai Marina or Dubai Creek with a buffet dinner and live entertainment on board.'],
            ['name' => 'Dubai Frame', 'destination' => 'dubai', 'category' => 'Sightseeing', 'price' => 2200, 'duration' => '1 hour', 'commons' => 'Dubai Frame.jpg', 'description' => 'A 150-metre picture-frame-shaped landmark offering views of both Old and New Dubai from its glass sky deck.'],
            ['name' => 'Dubai Miracle Garden', 'destination' => 'dubai', 'category' => 'Sightseeing', 'price' => 1800, 'duration' => '2 hours', 'commons' => 'Dubai Miracle Garden.jpg', 'description' => 'The world\'s largest natural flower garden, with over 150 million flowers arranged into sculptures, arches and structures.'],

            // Singapore
            ['name' => 'Universal Studios Singapore', 'destination' => 'singapore', 'category' => 'Adventure', 'price' => 6500, 'duration' => 'Full day', 'commons' => 'Universal Studios Singapore.jpg', 'description' => 'Southeast Asia\'s only Universal Studios theme park on Sentosa Island, with rides themed around Hollywood blockbusters.'],
            ['name' => 'Gardens by the Bay', 'destination' => 'singapore', 'category' => 'Sightseeing', 'price' => 1800, 'duration' => '2-3 hours', 'commons' => 'Gardens by the Bay.jpg', 'description' => 'Walk the OCBC Skyway between the futuristic Supertrees and explore the Flower Dome and Cloud Forest conservatories.'],
            ['name' => 'Sentosa Cable Car', 'destination' => 'singapore', 'category' => 'Sightseeing', 'price' => 2200, 'duration' => '1 hour', 'commons' => 'Sentosa cable car.jpg', 'description' => 'A scenic cable car ride from Mount Faber over the harbour to Sentosa Island, Singapore\'s resort island.'],
            ['name' => 'Singapore Flyer', 'destination' => 'singapore', 'category' => 'Sightseeing', 'price' => 2500, 'duration' => '30-45 minutes', 'commons' => 'Singapore Flyer.jpg', 'description' => 'One of the world\'s largest observation wheels, offering sweeping views over Marina Bay and the city skyline.'],
            ['name' => 'Night Safari', 'destination' => 'singapore', 'category' => 'Wildlife', 'price' => 3200, 'duration' => '2-3 hours', 'commons' => 'Night Safari, Singapore.jpg', 'description' => 'The world\'s first nocturnal wildlife park — a tram or walking tour through habitats of over 100 species active after dark.'],
        ];

        foreach ($activities as $index => $activity) {
            $destination = $destinations->get($activity['destination']);
            $model = Activity::updateOrCreate(
                ['slug' => Str::slug($activity['name'])],
                [
                    'unique_id'            => SiteTenant::id(),
                    'destination_id'       => $destination?->id,
                    'name'                 => $activity['name'],
                    'activity_category_id' => $this->resolveActivityCategoryId($activity['category'], SiteTenant::id()),
                    'description'          => $activity['description'],
                    'duration'             => $activity['duration'],
                    'price'                => $activity['price'],
                    'status'               => 'active',
                    'sort_order'           => 200 + $index,
                ]
            );

            $newImage = $this->downloadFromCommons($activity['commons'], 'activities')
                ?? $this->placeholderImage(900, 600, $activity['name'], 'activities', '#0d6efd');

            if ($model->image && $model->image !== $newImage) {
                Storage::disk('public')->delete($model->image);
            }

            $model->update(['image' => $newImage]);
        }
    }
}
