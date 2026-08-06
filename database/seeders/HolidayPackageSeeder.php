<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\HolidayPackage;
use App\Models\TravelCategory;
use App\Support\SiteTenant;
use Database\Seeders\Concerns\GeneratesDemoImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds the real, SEO-optimized 14-package travel catalogue (5 domestic tour
 * packages, 5 honeymoon packages over the same 5 domestic destinations, and
 * 4 international packages). Replaces all previous demo/placeholder packages
 * (Goa Beach Escape, Kerala Backwaters, Andaman Islands, Bali Honeymoon,
 * Manali Snow Escape, and any admin/AI-generated rows) — see cleanup() below.
 *
 * PRICING NOTE: all `price`/`discounted_price` values are market-benchmarked
 * estimates against public EaseMyTrip starting prices as of mid-2026 for
 * comparable itineraries. They are a reasonable go-live starting point, not
 * a live rate-card sync — review and adjust against actual airfare/hotel
 * contract costs before launch.
 */
class HolidayPackageSeeder extends Seeder
{
    use GeneratesDemoImages;

    public function run(): void
    {
        $goa = Destination::where('slug', 'goa')->first();
        if ($goa) {
            $this->seedGoaDemoPackage($goa);
        }

        $this->cleanupOldDemoPackages();
        $this->seedRealCatalogue();
    }

    // Removes every previously-seeded/admin-created package before reseeding the
    // real 14. All child records cascade-delete (itineraries, photos, faqs, reviews,
    // room types, departure cities, hotel/activity/feature/category pivots — see
    // package_* migrations, all FK'd cascadeOnDelete to holiday_packages).
    // package_enquiries/bookings/quotations use nullOnDelete + keep their own
    // snapshot columns, so historical records stay intact. Runs first every time
    // this seeder executes, so it's safe to re-run.
    private function cleanupOldDemoPackages(): void
    {
        HolidayPackage::withoutGlobalScopes()->get()->each->delete();
    }

    // Note: seedGoaDemoPackage() below is the original demo-data builder, kept
    // only so cleanupOldDemoPackages() always has real prior rows to exercise its
    // cascade-delete path on a fresh database. It runs and is then immediately
    // deleted by cleanupOldDemoPackages() — the real catalogue is what remains.
    private function seedGoaDemoPackage(Destination $goa): void
    {
        HolidayPackage::updateOrCreate(
            ['slug' => Str::slug('Goa Beach Escape')],
            [
                'destination_id' => $goa->id,
                'title'          => 'Goa Beach Escape',
                'nights'         => 4,
                'days'           => 5,
                'price'          => 24000,
                'discounted_price' => 18999,
                'status'         => 'active',
            ]
        );
    }

    private function seedRealCatalogue(): void
    {
        $destinations = Destination::whereIn('slug', [
            'kashmir', 'himachal-pradesh', 'kerala', 'sikkim', 'andaman', 'vietnam', 'malaysia', 'dubai', 'singapore',
        ])->get()->keyBy('slug');

        $categories = TravelCategory::pluck('id', 'slug');
        $catalogue = $this->destinationCatalogue();

        foreach ($this->packageDefinitions() as $index => $def) {
            $destination = $destinations->get($def['destination']);
            if (!$destination) {
                continue;
            }

            $this->seedPackage($destination, $catalogue[$def['destination']], $def, $categories, $index);
        }
    }

    private function roomTiers(float $price, float $discounted): array
    {
        $round50 = fn (float $n) => (int) (round($n / 50) * 50);

        return [
            ['name' => 'Standard Room', 'price' => $round50($price), 'discounted_price' => $round50($discounted)],
            ['name' => 'Deluxe Room',   'price' => $round50($price * 1.22), 'discounted_price' => $round50($discounted * 1.20)],
            ['name' => 'Premium Suite', 'price' => $round50($price * 1.48), 'discounted_price' => $round50($discounted * 1.45)],
        ];
    }

    private function seedPackage(Destination $destination, array $cat, array $def, $categoryIds, int $sortOrder): void
    {
        $package = HolidayPackage::updateOrCreate(
            ['slug' => $def['slug']],
            [
                'unique_id'         => SiteTenant::id(),
                'destination_id'    => $destination->id,
                'title'             => $def['title'],
                'nights'            => $cat['nights'],
                'days'              => $cat['nights'] + 1,
                'hotel_category'    => $cat['hotel_category'],
                'meals'             => $cat['meals'],
                'language'          => 'English / Hindi',
                'places_to_visit'   => $cat['places_to_visit'],
                'overview'          => $def['overview'],
                'seo_content'       => $def['seo_content'],
                'meta_title'        => $def['meta_title'],
                'meta_description'  => $def['meta_description'],
                'meta_keywords'     => $def['meta_keywords'],
                'focus_keyword'     => $def['focus_keyword'],
                'price'             => $def['price'],
                'discounted_price'  => $def['discounted_price'],
                'booking_type'      => 'book_enquiry',
                'status'            => 'active',
                'featured'          => !($def['honeymoon'] ?? false),
                'is_best_seller'    => $def['is_best_seller'] ?? false,
                'sort_order'        => $sortOrder,
            ]
        );

        $catIds = array_values(array_filter(array_map(fn ($slug) => $categoryIds->get($slug), $def['categories'])));
        $package->categories()->sync($catIds);

        // Departure cities
        $package->departureCities()->delete();
        foreach ($cat['departure_cities'] as $i => $city) {
            $package->departureCities()->create(['city_name' => $city, 'sort_order' => $i]);
        }

        // Package-level room tiers, priced consistently off this package's own price
        $package->roomTypes()->delete();
        foreach ($this->roomTiers((float) $def['price'], (float) $def['discounted_price']) as $i => $room) {
            $package->roomTypes()->create(array_merge($room, ['sort_order' => $i]));
        }

        // Itinerary
        $package->itineraries()->delete();
        foreach ($cat['itinerary'] as $i => $day) {
            $package->itineraries()->create([
                'day_number'    => $day['day_number'],
                'title'         => $day['title'],
                'route_summary' => $day['route_summary'],
                'detail'        => $day['detail'],
                'bullet_points' => $day['bullet_points'],
                'meal_tags'     => $day['meal_tags'],
                'sort_order'    => $i,
            ]);
        }

        // Day-wise hotels
        $hotelSync = [];
        foreach ($cat['hotel_legs'] as $i => $leg) {
            $hotel = Hotel::where('name', $leg['hotel'])->with('roomTypes')->first();
            if (!$hotel) {
                continue;
            }
            $roomType = $hotel->roomTypes->firstWhere('name', $leg['room_type']);

            $hotelSync[$hotel->id] = [
                'unique_id'    => $package->unique_id,
                'room_type_id' => $roomType?->id,
                'price'        => $leg['price'] ?? null,
                'is_optional'  => $leg['is_optional'] ?? false,
                'nights'       => $leg['nights'],
                'sort_order'   => $i,
                'day_number'   => $leg['day_number'],
                'note'         => $leg['note'] ?? 'Hotel is subject to availability. A similar or better property may be provided.',
            ];
        }
        $package->hotels()->sync($hotelSync);

        // Day-wise + general optional activities
        $activitySync = [];
        foreach (array_merge($cat['day_activities'], $cat['general_activities'] ?? []) as $i => $act) {
            $activity = Activity::where('name', $act['activity'])->first();
            if (!$activity) {
                continue;
            }
            $activitySync[$activity->id] = [
                'unique_id'   => $package->unique_id,
                'price'       => $act['price'] ?? null,
                'is_optional' => $act['is_optional'] ?? false,
                'sort_order'  => $i,
                'day_number'  => $act['day_number'] ?? null,
                'note'        => $act['note'] ?? null,
            ];
        }
        $package->optionalActivities()->sync($activitySync);

        // Inclusions / exclusions — destination-specific, plus honeymoon extras
        $package->customFeatures()->delete();
        $inclusions = array_merge($cat['inclusions'], $def['extra_inclusions'] ?? []);
        foreach ($inclusions as $i => $title) {
            $package->customFeatures()->create(['type' => 'inclusion', 'title' => $title, 'sort_order' => $i]);
        }
        foreach ($cat['exclusions'] as $i => $title) {
            $package->customFeatures()->create(['type' => 'exclusion', 'title' => $title, 'sort_order' => $i]);
        }

        // FAQs
        $package->faqs()->delete();
        foreach ($def['faqs'] as $i => $faq) {
            $package->faqs()->create(array_merge($faq, ['sort_order' => $i]));
        }

        // Photos — reuse the destination's already-downloaded real Commons/placeholder
        // images (same place, same files already stored locally) rather than
        // re-downloading; keeps every package photo real and locally served.
        $package->photos()->delete();
        $photoPaths = array_filter(array_merge([$destination->cover_image], $destination->gallery_images ?? []));
        foreach (array_values($photoPaths) as $i => $path) {
            $package->photos()->create(['path' => $path, 'is_cover' => $i === 0, 'sort_order' => $i]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Destination-level shared data: itinerary, day-wise hotel/activity plan,
    // inclusions/exclusions. Reused by both the tour and honeymoon package
    // variants of the same destination (Kashmir, Himachal, Kerala, Sikkim,
    // Andaman) so the real-world day plan isn't duplicated.
    // ─────────────────────────────────────────────────────────────────────────
    private function destinationCatalogue(): array
    {
        return [
            'kashmir' => [
                'nights' => 5, 'hotel_category' => '4 & 5 Star Hotels', 'meals' => 'Daily Breakfast',
                'places_to_visit' => 'Srinagar, Gulmarg, Pahalgam, Sonmarg',
                'departure_cities' => ['Delhi (DEL)', 'Mumbai (BOM)', 'Bangalore (BLR)', 'Chennai (MAA)'],
                'itinerary' => [
                    ['day_number' => 1, 'title' => 'Arrival in Srinagar · Dal Lake Shikara Ride', 'route_summary' => 'Srinagar Airport → Houseboat/Hotel · Dal Lake', 'detail' => 'Arrive at Srinagar Airport and transfer to your houseboat or lakeside hotel on Dal Lake. In the evening, enjoy a traditional shikara ride across the lake, past floating gardens and vegetable markets, with the Zabarwan hills turning gold at sunset.', 'bullet_points' => ['Airport pickup in an AC vehicle', 'Check-in at a Dal Lake-facing hotel/houseboat', 'Evening Shikara Ride on Dal Lake', 'Welcome Kahwa tea on arrival'], 'meal_tags' => ['dinner']],
                    ['day_number' => 2, 'title' => 'Full-Day Gulmarg Excursion', 'route_summary' => 'Srinagar → Gulmarg → Srinagar', 'detail' => 'Drive to Gulmarg, the "Meadow of Flowers", for a day of high-altitude sightseeing. Ride the Gulmarg Gondola cable car up to Apharwat Peak for panoramic views of the Pir Panjal range, with skiing and snow activities available in winter.', 'bullet_points' => ['Scenic drive to Gulmarg via Tangmarg', 'Gulmarg Gondola cable car ride (Phase 1/2 as per season)', 'Optional skiing, snowboarding & snow scooter rides', 'Return to Srinagar for overnight stay'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 3, 'title' => 'Srinagar City Tour & Sonmarg Glacier', 'route_summary' => 'Nishat Bagh · Shalimar Bagh · Pari Mahal · Sonmarg', 'detail' => 'Visit Srinagar\'s Mughal gardens — Nishat Bagh, Shalimar Bagh and the hilltop Pari Mahal — before a day trip to Sonmarg, the "Meadow of Gold", for a pony ride toward the Thajiwas Glacier.', 'bullet_points' => ['Nishat Bagh & Shalimar Bagh Mughal gardens', 'Pari Mahal viewpoint', 'Sonmarg day trip with Thajiwas Glacier pony ride', 'Return to Srinagar for overnight stay'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 4, 'title' => 'Srinagar to Pahalgam · Betaab Valley', 'route_summary' => 'Srinagar → Awantipora → Pahalgam · Betaab Valley', 'detail' => 'Check out and drive to Pahalgam, the "Valley of Shepherds", stopping at the Awantipora ruins en route. On arrival, visit the pine-forested Betaab Valley, named after the Bollywood film shot here, with the Lidder River running through it.', 'bullet_points' => ['Check-out and drive to Pahalgam (~2.5 hrs)', 'Awantipora temple ruins en route', 'Betaab Valley sightseeing', 'Check-in at Pahalgam hotel'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 5, 'title' => 'Pahalgam Local Sightseeing', 'route_summary' => 'Aru Valley · Chandanwari · Lidder River', 'detail' => 'A full day exploring Pahalgam\'s surrounding valleys — Aru Valley\'s meadows and Chandanwari, the starting point of the Amarnath Yatra — with time by the Lidder River banks.', 'bullet_points' => ['Aru Valley excursion', 'Chandanwari sightseeing', 'Leisure time by the Lidder River', 'Optional horse/pony rides (extra cost)'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 6, 'title' => 'Departure from Srinagar', 'route_summary' => 'Pahalgam → Srinagar Airport', 'detail' => 'Check out after breakfast and drive back to Srinagar for your flight home, carrying back memories of the valley.', 'bullet_points' => ['Breakfast and hotel checkout', 'Drive to Srinagar Airport (~2.5 hrs)', 'Departure transfer'], 'meal_tags' => ['breakfast']],
                ],
                'hotel_legs' => [
                    ['hotel' => 'Vivanta Dal View, Srinagar', 'room_type' => 'Deluxe Room (Lake View)', 'day_number' => 1, 'nights' => 3],
                    ['hotel' => 'The Pahalgam Hotel', 'room_type' => 'River View Room', 'day_number' => 4, 'nights' => 2],
                ],
                'day_activities' => [
                    ['activity' => 'Shikara Ride on Dal Lake', 'day_number' => 1],
                    ['activity' => 'Gulmarg Gondola', 'day_number' => 2, 'is_optional' => true, 'price' => 1800, 'note' => 'Gondola Phase 1 ticket; Phase 2 chargeable separately'],
                    ['activity' => 'Thajiwas Glacier Trek', 'day_number' => 3, 'is_optional' => true, 'price' => 900],
                    ['activity' => 'Betaab Valley Sightseeing', 'day_number' => 4],
                ],
                'inclusions' => ['5 nights\' accommodation on double occupancy with daily breakfast', 'All transfers and sightseeing in a private AC vehicle', 'One evening Shikara ride on Dal Lake', 'Toll, parking and driver allowances', 'All applicable hotel taxes'],
                'exclusions' => ['Airfare/train fare to and from Srinagar', 'Gulmarg Gondola and other adventure activity charges unless mentioned', 'Entry fees to monuments and gardens', 'Personal expenses, tips and gratuities', 'Travel insurance (recommended)'],
            ],

            'himachal-pradesh' => [
                'nights' => 5, 'hotel_category' => '4 & 5 Star Hotels', 'meals' => 'Daily Breakfast',
                'places_to_visit' => 'Shimla, Manali, Kufri, Solang Valley',
                'departure_cities' => ['Delhi (DEL)', 'Chandigarh (IXC)', 'Mumbai (BOM)'],
                'itinerary' => [
                    ['day_number' => 1, 'title' => 'Arrival in Shimla · Mall Road Evening', 'route_summary' => 'Shimla Airport/Kalka → Hotel · Mall Road & The Ridge', 'detail' => 'Arrive in Shimla and check in to your hotel. In the evening, walk the pedestrian-only Mall Road and The Ridge, taking in views of the Christ Church and the surrounding pine-covered hills.', 'bullet_points' => ['Airport/station pickup in an AC vehicle', 'Hotel check-in', 'Evening Mall Road & Ridge walk', 'Views of Christ Church'], 'meal_tags' => ['dinner']],
                    ['day_number' => 2, 'title' => 'Shimla Sightseeing & Kufri', 'route_summary' => 'Jakhoo Temple · Kufri · Shimla', 'detail' => 'Visit the hilltop Jakhoo Temple for panoramic valley views, then drive to Kufri for pony rides and (in season) snow activities, before returning to Shimla for the night.', 'bullet_points' => ['Jakhoo Temple visit', 'Kufri excursion with pony rides', 'Optional Himalayan Nature Park visit', 'Return to Shimla for overnight stay'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 3, 'title' => 'Shimla to Manali', 'route_summary' => 'Shimla → Kullu Valley → Manali', 'detail' => 'A scenic drive from Shimla to Manali along the Beas River, through apple orchards and the Kullu Valley. Check in and spend the evening at leisure on Manali\'s Mall Road.', 'bullet_points' => ['Scenic drive to Manali (~7-8 hrs)', 'Kullu Valley views en route', 'Check-in at Manali hotel', 'Evening free on Mall Road'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 4, 'title' => 'Manali Local Sightseeing', 'route_summary' => 'Hadimba Temple · Old Manali · Vashisht', 'detail' => 'Visit the 16th-century wooden Hadimba Temple set inside a cedar forest, wander the cafes of Old Manali, and relax at the natural hot springs of Vashisht village.', 'bullet_points' => ['Hadimba Devi Temple', 'Old Manali café walk', 'Vashisht hot springs & temple', 'Van Vihar / Tibetan Monastery (optional)'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 5, 'title' => 'Solang Valley & Atal Tunnel Excursion', 'route_summary' => 'Manali → Solang Valley → Atal Tunnel', 'detail' => 'A full day of Himalayan adventure at Solang Valley — paragliding, zorbing and (in winter) skiing — followed by a drive through the Atal Tunnel, one of the world\'s longest high-altitude tunnels, toward Sissu.', 'bullet_points' => ['Solang Valley adventure activities', 'Atal Tunnel drive-through', 'Optional Rohtang Pass extension (permit & weather permitting)', 'Return to Manali for overnight stay'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 6, 'title' => 'Departure from Manali', 'route_summary' => 'Manali → Chandigarh/Delhi', 'detail' => 'Check out after breakfast for your onward journey, with the Himalayas visible in the rear-view mirror for most of the drive.', 'bullet_points' => ['Breakfast and hotel checkout', 'Transfer to Chandigarh/Bhuntar airport or onward drive', 'Departure'], 'meal_tags' => ['breakfast']],
                ],
                'hotel_legs' => [
                    ['hotel' => 'The Oberoi Cecil, Shimla', 'room_type' => 'Classic Room', 'day_number' => 1, 'nights' => 2],
                    ['hotel' => 'Span Resort & Spa, Manali', 'room_type' => 'River View Room', 'day_number' => 3, 'nights' => 3],
                ],
                'day_activities' => [
                    ['activity' => 'Mall Road Shimla Walk', 'day_number' => 1],
                    ['activity' => 'Hadimba Temple Visit', 'day_number' => 4],
                    ['activity' => 'Solang Valley Adventure Activities', 'day_number' => 5, 'is_optional' => true, 'price' => 1500],
                    ['activity' => 'Atal Tunnel & Rohtang Pass Excursion', 'day_number' => 5, 'is_optional' => true, 'price' => 1200],
                ],
                'inclusions' => ['5 nights\' accommodation on double occupancy with daily breakfast', 'All transfers and sightseeing in a private AC vehicle', 'Shimla-Manali inter-city transfer', 'Toll, parking and driver allowances', 'All applicable hotel taxes'],
                'exclusions' => ['Airfare/train fare', 'Rohtang Pass permit and adventure activity charges unless mentioned', 'Entry fees to monuments and parks', 'Personal expenses, tips and gratuities', 'Travel insurance (recommended)'],
            ],

            'kerala' => [
                'nights' => 5, 'hotel_category' => '4 & 5 Star Hotels + Houseboat', 'meals' => 'Daily Breakfast',
                'places_to_visit' => 'Kochi, Munnar, Thekkady, Alleppey',
                'departure_cities' => ['Delhi (DEL)', 'Mumbai (BOM)', 'Bangalore (BLR)', 'Chennai (MAA)'],
                'itinerary' => [
                    ['day_number' => 1, 'title' => 'Arrival in Kochi · Fort Kochi Heritage Walk', 'route_summary' => 'Kochi Airport → Hotel · Fort Kochi', 'detail' => 'Arrive in Kochi and check in. In the afternoon, explore Fort Kochi\'s colonial waterfront and the iconic Chinese fishing nets, a legacy of 14th-century Chinese traders. In the evening, watch a live Kathakali dance performance.', 'bullet_points' => ['Airport pickup in an AC vehicle', 'Fort Kochi heritage walk', 'Chinese fishing nets at sunset', 'Evening Kathakali dance show'], 'meal_tags' => ['dinner']],
                    ['day_number' => 2, 'title' => 'Kochi to Munnar', 'route_summary' => 'Kochi → Munnar (Tea Plantations)', 'detail' => 'Drive up to the hill station of Munnar, winding through spice plantations and waterfalls. Check in to your plantation-view resort and relax for the evening.', 'bullet_points' => ['Scenic drive to Munnar (~4 hrs)', 'Waterfall photo stops en route', 'Check-in at Munnar resort', 'Evening at leisure'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 3, 'title' => 'Munnar Tea Plantation Tour', 'route_summary' => 'Tea Museum · Tea Gardens · Eravikulam viewpoint', 'detail' => 'A full day exploring Munnar\'s rolling tea estates — visit a working tea museum and factory to see how the leaf becomes the cup, with stops at scenic viewpoints over the plantations.', 'bullet_points' => ['Munnar tea plantation & tea museum tour', 'Eravikulam viewpoint (seasonal)', 'Photo stops at Mattupetty/Echo Point (optional)', 'Overnight in Munnar'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 4, 'title' => 'Munnar to Thekkady · Periyar Boat Safari', 'route_summary' => 'Munnar → Thekkady · Periyar Lake', 'detail' => 'Drive to Thekkady, home to the Periyar Tiger Reserve, and take a boat cruise on Periyar Lake — a good chance to spot elephants, bison and birdlife on the shore.', 'bullet_points' => ['Drive to Thekkady (~3.5 hrs)', 'Periyar Lake boat safari', 'Optional spice plantation walk', 'Overnight in Thekkady'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 5, 'title' => 'Thekkady to Alleppey · Backwater Houseboat', 'route_summary' => 'Thekkady → Alleppey · Houseboat Cruise', 'detail' => 'Drive to Alleppey and board a traditional kettuvallam houseboat for an overnight cruise through the palm-fringed backwaters, with meals cooked fresh on board.', 'bullet_points' => ['Drive to Alleppey (~4 hrs)', 'Board private houseboat', 'Backwater cruise through paddy fields and villages', 'Overnight stay on the houseboat'], 'meal_tags' => ['breakfast', 'lunch', 'dinner']],
                    ['day_number' => 6, 'title' => 'Disembark & Departure', 'route_summary' => 'Alleppey → Kochi Airport', 'detail' => 'Disembark the houseboat after breakfast and transfer to Kochi Airport for your onward journey.', 'bullet_points' => ['Breakfast on the houseboat', 'Disembark and drive to Kochi Airport (~1.5 hrs)', 'Departure transfer'], 'meal_tags' => ['breakfast']],
                ],
                'hotel_legs' => [
                    ['hotel' => 'Grand Hyatt Kochi Bolgatty', 'room_type' => 'Deluxe Room (Backwater View)', 'day_number' => 1, 'nights' => 1],
                    ['hotel' => 'Spice Tree Munnar', 'room_type' => 'Plantation View Room', 'day_number' => 2, 'nights' => 2],
                    ['hotel' => 'Cardamom County, Thekkady', 'room_type' => 'Garden View Room', 'day_number' => 4, 'nights' => 1],
                    ['hotel' => 'Xandari Pearl Houseboat, Alleppey', 'room_type' => 'Deluxe Cabin', 'day_number' => 5, 'nights' => 1],
                ],
                'day_activities' => [
                    ['activity' => 'Fort Kochi Chinese Fishing Nets Walk', 'day_number' => 1],
                    ['activity' => 'Kathakali Dance Show', 'day_number' => 1, 'is_optional' => true, 'price' => 400],
                    ['activity' => 'Munnar Tea Plantation Tour', 'day_number' => 3],
                    ['activity' => 'Periyar Lake Boat Safari', 'day_number' => 4, 'is_optional' => true, 'price' => 600],
                    ['activity' => 'Alleppey Houseboat Cruise', 'day_number' => 5],
                ],
                'inclusions' => ['5 nights\' accommodation (hotels + 1 night houseboat) on double occupancy with daily breakfast', 'All transfers and sightseeing in a private AC vehicle', 'All meals on board the houseboat', 'Toll, parking and driver allowances', 'All applicable hotel taxes'],
                'exclusions' => ['Airfare/train fare to and from Kochi', 'Boat safari and adventure activity charges unless mentioned', 'Entry fees to museums and sanctuaries', 'Personal expenses, tips and gratuities', 'Travel insurance (recommended)'],
            ],

            'sikkim' => [
                'nights' => 5, 'hotel_category' => '3 & 4 Star Hotels', 'meals' => 'Daily Breakfast',
                'places_to_visit' => 'Gangtok, Pelling, Lachung',
                'departure_cities' => ['Delhi (DEL)', 'Kolkata (CCU)', 'Bagdogra (IXB)'],
                'itinerary' => [
                    ['day_number' => 1, 'title' => 'Arrival in Gangtok · Rumtek Monastery', 'route_summary' => 'Bagdogra Airport/NJP → Gangtok · Rumtek Monastery', 'detail' => 'Arrive at Bagdogra and drive up to Gangtok. On the way, visit Rumtek Monastery, one of Sikkim\'s largest and most important Buddhist monasteries, overlooking the Gangtok valley.', 'bullet_points' => ['Pickup from Bagdogra Airport/NJP station', 'Scenic drive to Gangtok (~4-5 hrs)', 'Rumtek Monastery visit', 'Check-in at Gangtok hotel'], 'meal_tags' => ['dinner']],
                    ['day_number' => 2, 'title' => 'Tsomgo Lake, Baba Mandir & Nathula Pass', 'route_summary' => 'Gangtok → Tsomgo Lake → Baba Mandir → Nathula Pass', 'detail' => 'A permit-only day trip along the East Sikkim border route — the glacial Tsomgo Lake at nearly 12,400 ft, the revered Baba Harbhajan Singh Memorial shrine, and (permits and weather permitting) the high-altitude Indo-China border crossing at Nathula Pass.', 'bullet_points' => ['Tsomgo Lake excursion', 'Baba Mandir visit', 'Nathula Pass trip (special permit required, subject to weather/border status)', 'Return to Gangtok for overnight stay'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 3, 'title' => 'Gangtok to Lachung', 'route_summary' => 'Gangtok → Lachung (North Sikkim)', 'detail' => 'Drive north to the alpine village of Lachung, passing waterfalls and pine forests along the Teesta river valley. Check in for two nights at your mountain lodge.', 'bullet_points' => ['Scenic drive to Lachung (~5-6 hrs, permit required)', 'Waterfall stops en route', 'Check-in at Lachung hotel'], 'meal_tags' => ['breakfast', 'dinner']],
                    ['day_number' => 4, 'title' => 'Yumthang Valley & Zero Point Excursion', 'route_summary' => 'Lachung → Yumthang Valley → Zero Point', 'detail' => 'An early start to the "Valley of Flowers", Yumthang, ringed by snow peaks and hot springs, with an optional extension to Zero Point (subject to road and weather conditions).', 'bullet_points' => ['Yumthang Valley excursion', 'Optional Zero Point extension', 'Hot springs visit (Yumthang Chu)', 'Return to Lachung for overnight stay'], 'meal_tags' => ['breakfast', 'dinner']],
                    ['day_number' => 5, 'title' => 'Lachung to Pelling', 'route_summary' => 'Lachung → Gangtok → Pelling', 'detail' => 'Drive from Lachung back through Gangtok and on to Pelling in West Sikkim, known for its unobstructed views of the Kanchenjunga range.', 'bullet_points' => ['Long scenic drive to Pelling (~7-8 hrs)', 'Kanchenjunga viewpoints en route (weather permitting)', 'Check-in at Pelling hotel'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 6, 'title' => 'Pelling Sightseeing & Departure', 'route_summary' => 'Pelling → Bagdogra Airport', 'detail' => 'A short Pelling sightseeing round before the drive back to Bagdogra for your onward flight.', 'bullet_points' => ['Pemayangtse Monastery / Kanchenjunga viewpoint (time permitting)', 'Drive to Bagdogra Airport (~5-6 hrs)', 'Departure transfer'], 'meal_tags' => ['breakfast']],
                ],
                'hotel_legs' => [
                    ['hotel' => 'Mayfair Spa Resort & Casino, Gangtok', 'room_type' => 'Deluxe Room', 'day_number' => 1, 'nights' => 2],
                    ['hotel' => 'Yarlam Resort, Lachung', 'room_type' => 'Standard Room', 'day_number' => 3, 'nights' => 2],
                    ['hotel' => 'The Elgin Mount Pandim, Pelling', 'room_type' => 'Mountain View Room', 'day_number' => 5, 'nights' => 1],
                ],
                'day_activities' => [
                    ['activity' => 'Rumtek Monastery Tour', 'day_number' => 1],
                    ['activity' => 'Tsomgo Lake Excursion', 'day_number' => 2],
                    ['activity' => 'Baba Mandir Visit', 'day_number' => 2],
                    ['activity' => 'Nathula Pass Permit Trip', 'day_number' => 2, 'is_optional' => true, 'price' => 1800, 'note' => 'Special protected-area permit required, subject to weather and border status'],
                ],
                'inclusions' => ['5 nights\' accommodation on double occupancy with daily breakfast', 'All transfers and sightseeing in a private vehicle', 'Protected-area permits for Tsomgo Lake, Yumthang & Lachung', 'Toll, parking and driver allowances', 'All applicable hotel taxes'],
                'exclusions' => ['Airfare/train fare to Bagdogra/NJP', 'Nathula Pass permit fee and inner-line permit charges', 'Entry fees to monasteries', 'Personal expenses, tips and gratuities', 'Travel insurance (recommended)'],
            ],

            'andaman' => [
                'nights' => 4, 'hotel_category' => '4 & 5 Star Hotels', 'meals' => 'Daily Breakfast',
                'places_to_visit' => 'Port Blair, Havelock, Neil Island',
                'departure_cities' => ['Delhi (DEL)', 'Chennai (MAA)', 'Kolkata (CCU)', 'Bangalore (BLR)'],
                'itinerary' => [
                    ['day_number' => 1, 'title' => 'Arrival in Port Blair · Cellular Jail Light & Sound Show', 'route_summary' => 'Port Blair Airport → Hotel · Cellular Jail', 'detail' => 'Arrive in Port Blair and check in. In the evening, visit the colonial-era Cellular Jail for an evening light-and-sound show retelling India\'s freedom struggle.', 'bullet_points' => ['Airport pickup in an AC vehicle', 'Hotel check-in', 'Evening Cellular Jail Light & Sound Show'], 'meal_tags' => ['dinner']],
                    ['day_number' => 2, 'title' => 'Ross & North Bay Islands · Ferry to Havelock', 'route_summary' => 'Port Blair → Ross Island → North Bay → Havelock', 'detail' => 'A morning boat trip to Ross Island, the former British administrative headquarters now reclaimed by forest and deer, and North Bay Island for its coral reefs, before an afternoon ferry to Havelock Island (Swaraj Dweep).', 'bullet_points' => ['Ross Island boat tour', 'North Bay Island coral viewing', 'Afternoon ferry to Havelock', 'Check-in at Havelock resort'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 3, 'title' => 'Havelock · Radhanagar Beach', 'route_summary' => 'Radhanagar Beach · Optional Scuba Diving', 'detail' => 'A full day at Havelock\'s Radhanagar Beach, repeatedly ranked among Asia\'s best beaches, with an optional scuba diving or snorkelling session on the island\'s coral reefs.', 'bullet_points' => ['Radhanagar Beach visit', 'Optional scuba diving & snorkelling', 'Leisure time by the beach', 'Overnight in Havelock'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 4, 'title' => 'Havelock to Neil Island', 'route_summary' => 'Havelock → Neil Island (Shaheed Dweep)', 'detail' => 'Ferry to the quieter Neil Island, with time to explore Bharatpur and Laxmanpur beaches and watch the sunset over the Bay of Bengal.', 'bullet_points' => ['Ferry to Neil Island', 'Bharatpur Beach visit', 'Laxmanpur Beach sunset point', 'Overnight in Neil Island'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 5, 'title' => 'Departure via Port Blair', 'route_summary' => 'Neil Island → Port Blair Airport', 'detail' => 'Ferry back to Port Blair after breakfast and transfer directly to the airport for your onward flight.', 'bullet_points' => ['Morning ferry to Port Blair', 'Departure transfer to airport'], 'meal_tags' => ['breakfast']],
                ],
                'hotel_legs' => [
                    ['hotel' => 'Fortune Resort Bay Island, Port Blair', 'room_type' => 'Deluxe Room (Sea View)', 'day_number' => 1, 'nights' => 1],
                    ['hotel' => 'Taj Exotica Resort & Spa, Havelock', 'room_type' => 'Beach Villa', 'day_number' => 2, 'nights' => 2],
                    ['hotel' => 'Tango Beach Resort, Neil Island', 'room_type' => 'Standard Cottage', 'day_number' => 4, 'nights' => 1],
                ],
                'day_activities' => [
                    ['activity' => 'Cellular Jail Light & Sound Show', 'day_number' => 1, 'is_optional' => true, 'price' => 200],
                    ['activity' => 'Ross & North Bay Island Tour', 'day_number' => 2, 'is_optional' => true, 'price' => 1200],
                    ['activity' => 'Radhanagar Beach Visit', 'day_number' => 3],
                    ['activity' => 'Havelock Scuba Diving & Snorkelling', 'day_number' => 3, 'is_optional' => true, 'price' => 4500],
                ],
                'inclusions' => ['4 nights\' accommodation on double occupancy with daily breakfast', 'Port Blair–Havelock–Neil–Port Blair ferry tickets', 'All island transfers and sightseeing', 'Toll, parking and driver allowances', 'All applicable hotel taxes'],
                'exclusions' => ['Airfare to and from Port Blair', 'Scuba diving, water sports and light & sound show tickets unless mentioned', 'Entry permits for foreign nationals (if applicable)', 'Personal expenses, tips and gratuities', 'Travel insurance (recommended)'],
            ],

            'vietnam' => [
                'nights' => 5, 'hotel_category' => '4 & 5 Star Hotels', 'meals' => 'Daily Breakfast',
                'places_to_visit' => 'Hanoi, Ha Long Bay, Da Nang, Hoi An',
                'departure_cities' => ['Delhi (DEL)', 'Mumbai (BOM)', 'Bangalore (BLR)'],
                'itinerary' => [
                    ['day_number' => 1, 'title' => 'Arrival in Hanoi · Old Quarter', 'route_summary' => 'Noi Bai Airport → Hotel · Hanoi Old Quarter', 'detail' => 'Arrive in Hanoi and check in near the Old Quarter. Spend the evening wandering its 36 historic streets, past Hoan Kiem Lake and the night market stalls.', 'bullet_points' => ['Airport pickup in an AC vehicle', 'Hotel check-in near the Old Quarter', 'Evening Old Quarter & Hoan Kiem Lake walk'], 'meal_tags' => ['dinner']],
                    ['day_number' => 2, 'title' => 'Ha Long Bay Cruise', 'route_summary' => 'Hanoi → Ha Long Bay · Overnight Cruise', 'detail' => 'Drive to Ha Long Bay and board an overnight cruise among thousands of limestone karsts rising from the emerald waters of this UNESCO World Heritage bay, with kayaking and a cave visit included.', 'bullet_points' => ['Drive to Ha Long Bay (~3.5 hrs)', 'Board cruise, cabin check-in', 'Kayaking & limestone cave visit', 'Overnight on board the cruise'], 'meal_tags' => ['breakfast', 'lunch', 'dinner']],
                    ['day_number' => 3, 'title' => 'Ha Long Bay to Da Nang', 'route_summary' => 'Ha Long Bay → Hanoi → Fly to Da Nang', 'detail' => 'Enjoy a final morning of cruise activities before disembarking and transferring to Hanoi for a flight to Da Nang, on Vietnam\'s central coast. Check in for a 3-night stay.', 'bullet_points' => ['Morning cruise activities & brunch', 'Disembark and transfer to Hanoi', 'Flight to Da Nang', 'Check-in at Da Nang beach resort'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 4, 'title' => 'Ba Na Hills & Golden Bridge', 'route_summary' => 'Da Nang → Ba Na Hills → Golden Bridge', 'detail' => 'A cable car ride up to Ba Na Hills to walk across the famous stone "hands" of the Golden Bridge, with the French Village, gardens and Fantasy Park nearby.', 'bullet_points' => ['Ba Na Hills cable car ride', 'Golden Bridge photo stop', 'French Village & flower gardens', 'Return to Da Nang for overnight stay'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 5, 'title' => 'Hoi An Ancient Town', 'route_summary' => 'Da Nang → Hoi An → Da Nang', 'detail' => 'A day trip to Hoi An\'s UNESCO-listed Ancient Town, with its lantern-lit streets, the Japanese Covered Bridge and famous tailor shops along the Thu Bon River.', 'bullet_points' => ['Hoi An Ancient Town walking tour', 'Japanese Covered Bridge', 'Thu Bon riverfront & tailor shops', 'Return to Da Nang for overnight stay'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 6, 'title' => 'Departure from Da Nang', 'route_summary' => 'Da Nang Airport departure', 'detail' => 'Check out after breakfast and transfer to Da Nang International Airport for your flight home.', 'bullet_points' => ['Breakfast and hotel checkout', 'Departure transfer to Da Nang Airport'], 'meal_tags' => ['breakfast']],
                ],
                'hotel_legs' => [
                    ['hotel' => 'Hanoi La Siesta Hotel & Spa', 'room_type' => 'Deluxe Room', 'day_number' => 1, 'nights' => 1],
                    ['hotel' => 'Paradise Elegance Cruise, Ha Long Bay', 'room_type' => 'Deluxe Ocean View Cabin', 'day_number' => 2, 'nights' => 1],
                    ['hotel' => 'InterContinental Danang Sun Peninsula Resort', 'room_type' => 'Ocean View Room', 'day_number' => 3, 'nights' => 3],
                ],
                'day_activities' => [
                    ['activity' => 'Ha Long Bay Cruise', 'day_number' => 2],
                    ['activity' => 'Ba Na Hills Golden Bridge Tour', 'day_number' => 4, 'is_optional' => true, 'price' => 2200],
                    ['activity' => 'Hoi An Ancient Town Walk', 'day_number' => 5],
                ],
                'general_activities' => [
                    ['activity' => 'Cu Chi Tunnels Tour', 'is_optional' => true, 'price' => 1800, 'note' => 'Located near Ho Chi Minh City — can be added as a pre/post extension to this itinerary'],
                ],
                'inclusions' => ['5 nights\' accommodation (hotels + 1 night cruise cabin) on double occupancy with daily breakfast', 'Ha Long Bay cruise with lunch, dinner and kayaking', 'Hanoi–Da Nang domestic flight', 'All transfers and sightseeing as per itinerary', 'Applicable hotel taxes'],
                'exclusions' => ['International airfare to/from Vietnam', 'Vietnam visa/e-visa fee', 'Ba Na Hills cable car and other entry tickets unless mentioned', 'Personal expenses, tips and gratuities', 'Travel insurance (recommended)'],
            ],

            'malaysia' => [
                'nights' => 4, 'hotel_category' => '4 & 5 Star Hotels', 'meals' => 'Daily Breakfast',
                'places_to_visit' => 'Kuala Lumpur, Genting Highlands, Langkawi',
                'departure_cities' => ['Delhi (DEL)', 'Mumbai (BOM)', 'Chennai (MAA)', 'Bangalore (BLR)'],
                'itinerary' => [
                    ['day_number' => 1, 'title' => 'Arrival in Kuala Lumpur · Petronas Towers', 'route_summary' => 'KLIA → Hotel · Petronas Towers', 'detail' => 'Arrive in Kuala Lumpur and check in. In the evening, visit the iconic 88-storey Petronas Twin Towers, crossing the Skybridge and riding to the observation deck for city views.', 'bullet_points' => ['Airport pickup in an AC vehicle', 'Hotel check-in', 'Petronas Towers Skybridge & Observation Deck'], 'meal_tags' => ['dinner']],
                    ['day_number' => 2, 'title' => 'Batu Caves & Genting Highlands', 'route_summary' => 'Batu Caves → Genting Highlands (SkyWay)', 'detail' => 'Climb the 272 rainbow steps to the limestone Batu Caves temple, then ride the Genting SkyWay, one of the fastest cable cars in the world, up to the hilltop resort of Genting Highlands.', 'bullet_points' => ['Batu Caves temple visit', 'Genting SkyWay cable car ride', 'Time at Genting\'s theme park/casino resort', 'Return to Kuala Lumpur for overnight stay'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 3, 'title' => 'Kuala Lumpur to Langkawi', 'route_summary' => 'Fly to Langkawi · Sky Bridge', 'detail' => 'Fly to the island of Langkawi and check in. In the afternoon, ride the cable car up Gunung Mat Cincang to the curved Langkawi Sky Bridge for views across the Andaman Sea.', 'bullet_points' => ['Flight to Langkawi', 'Check-in at beach resort', 'Langkawi Sky Bridge cable car ride'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 4, 'title' => 'Langkawi Island Leisure', 'route_summary' => 'Langkawi beaches & island hopping', 'detail' => 'A relaxed day on Langkawi\'s beaches, with an optional island-hopping boat tour to nearby Pulau Payar and its marine park.', 'bullet_points' => ['Leisure time at the resort beach', 'Optional island-hopping boat tour', 'Optional Underwater World aquarium visit'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 5, 'title' => 'Departure from Langkawi', 'route_summary' => 'Langkawi Airport departure', 'detail' => 'Check out after breakfast and transfer to Langkawi International Airport for your flight home (via Kuala Lumpur if required).', 'bullet_points' => ['Breakfast and hotel checkout', 'Departure transfer to Langkawi Airport'], 'meal_tags' => ['breakfast']],
                ],
                'hotel_legs' => [
                    ['hotel' => 'Sunway Resort Hotel, Kuala Lumpur', 'room_type' => 'Deluxe Room', 'day_number' => 1, 'nights' => 2],
                    ['hotel' => 'Berjaya Langkawi Resort', 'room_type' => 'Hillside Chalet', 'day_number' => 3, 'nights' => 2],
                ],
                'day_activities' => [
                    ['activity' => 'Petronas Towers Observation Deck', 'day_number' => 1, 'is_optional' => true, 'price' => 3200],
                    ['activity' => 'Batu Caves Tour', 'day_number' => 2],
                    ['activity' => 'Genting SkyWay Cable Car', 'day_number' => 2, 'is_optional' => true, 'price' => 1400],
                    ['activity' => 'Langkawi Sky Bridge', 'day_number' => 3, 'is_optional' => true, 'price' => 1800],
                ],
                'inclusions' => ['4 nights\' accommodation on double occupancy with daily breakfast', 'Kuala Lumpur–Langkawi domestic flight', 'All transfers and sightseeing as per itinerary', 'Applicable hotel taxes'],
                'exclusions' => ['International airfare to/from Malaysia', 'Malaysia entry visa/eNTRI fee (if applicable)', 'Cable car, observation deck and other entry tickets unless mentioned', 'Personal expenses, tips and gratuities', 'Travel insurance (recommended)'],
            ],

            'dubai' => [
                'nights' => 4, 'hotel_category' => '4 & 5 Star Hotels', 'meals' => 'Daily Breakfast',
                'places_to_visit' => 'Dubai, Abu Dhabi',
                'departure_cities' => ['Delhi (DEL)', 'Mumbai (BOM)', 'Bangalore (BLR)', 'Chennai (MAA)', 'Kochi (COK)'],
                'itinerary' => [
                    ['day_number' => 1, 'title' => 'Arrival in Dubai · Burj Khalifa', 'route_summary' => 'DXB Airport → Hotel · Burj Khalifa', 'detail' => 'Arrive in Dubai and check in. In the evening, ride to the observation deck of the Burj Khalifa, the world\'s tallest building, for 360-degree views over Downtown Dubai and the Gulf.', 'bullet_points' => ['Airport pickup in an AC vehicle', 'Hotel check-in', 'Burj Khalifa "At The Top" observation deck'], 'meal_tags' => ['dinner']],
                    ['day_number' => 2, 'title' => 'Desert Safari with BBQ Dinner', 'route_summary' => 'Dubai Desert · Dune Bashing · Camp', 'detail' => 'An afternoon desert safari — dune bashing in a 4x4, camel riding and sandboarding — followed by a BBQ dinner with live entertainment at a Bedouin-style desert camp under the stars.', 'bullet_points' => ['4x4 dune bashing', 'Camel riding & sandboarding', 'BBQ dinner with live entertainment', 'Return transfer to hotel'], 'meal_tags' => ['breakfast', 'dinner']],
                    ['day_number' => 3, 'title' => 'City Tour, Dubai Frame & Dhow Cruise', 'route_summary' => 'Dubai Frame · Miracle Garden · Dhow Cruise', 'detail' => 'A city tour covering the Dubai Frame and the flower-filled Dubai Miracle Garden, followed by an evening dhow cruise dinner along Dubai Marina with live entertainment on board.', 'bullet_points' => ['Dubai Frame sky deck', 'Dubai Miracle Garden', 'Evening Dhow Cruise Dinner on Dubai Marina'], 'meal_tags' => ['breakfast', 'dinner']],
                    ['day_number' => 4, 'title' => 'Abu Dhabi Day Trip', 'route_summary' => 'Dubai → Abu Dhabi → Dubai', 'detail' => 'A day trip to Abu Dhabi to visit the Sheikh Zayed Grand Mosque, one of the world\'s largest mosques, and drive past the Emirates Palace and Corniche waterfront.', 'bullet_points' => ['Sheikh Zayed Grand Mosque visit', 'Emirates Palace photo stop', 'Corniche waterfront drive', 'Return to Dubai for overnight stay'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 5, 'title' => 'Departure from Dubai', 'route_summary' => 'DXB Airport departure', 'detail' => 'Check out after breakfast and transfer to Dubai International Airport for your flight home.', 'bullet_points' => ['Breakfast and hotel checkout', 'Departure transfer to DXB Airport'], 'meal_tags' => ['breakfast']],
                ],
                'hotel_legs' => [
                    ['hotel' => 'Grand Millennium Dubai', 'room_type' => 'Deluxe Room', 'day_number' => 1, 'nights' => 4],
                    ['hotel' => 'Atlantis The Palm', 'room_type' => 'Ocean Deluxe Room', 'day_number' => 1, 'nights' => 4, 'is_optional' => true, 'note' => 'Premium optional upgrade stay on Palm Jumeirah with Aquaventure Waterpark access'],
                ],
                'day_activities' => [
                    ['activity' => 'Burj Khalifa At The Top', 'day_number' => 1, 'is_optional' => true, 'price' => 4500],
                    ['activity' => 'Desert Safari with BBQ Dinner', 'day_number' => 2],
                    ['activity' => 'Dubai Frame', 'day_number' => 3, 'is_optional' => true, 'price' => 2200],
                    ['activity' => 'Dubai Miracle Garden', 'day_number' => 3, 'is_optional' => true, 'price' => 1800],
                    ['activity' => 'Dhow Cruise Dinner', 'day_number' => 3],
                ],
                'inclusions' => ['4 nights\' accommodation on double occupancy with daily breakfast', 'Desert safari with BBQ dinner', 'Dhow cruise dinner', 'Abu Dhabi day trip with Grand Mosque visit', 'All transfers in a private AC vehicle'],
                'exclusions' => ['International airfare to/from Dubai', 'UAE visa fee', 'Burj Khalifa, Dubai Frame and Miracle Garden entry tickets unless mentioned', 'Personal expenses, tips and gratuities', 'Travel insurance (recommended)'],
            ],

            'singapore' => [
                'nights' => 4, 'hotel_category' => '3 to 5 Star Hotels', 'meals' => 'Daily Breakfast',
                'places_to_visit' => 'Singapore, Sentosa Island',
                'departure_cities' => ['Delhi (DEL)', 'Mumbai (BOM)', 'Bangalore (BLR)', 'Chennai (MAA)'],
                'itinerary' => [
                    ['day_number' => 1, 'title' => 'Arrival in Singapore · Gardens by the Bay', 'route_summary' => 'Changi Airport → Hotel · Gardens by the Bay', 'detail' => 'Arrive in Singapore and check in. In the evening, walk the OCBC Skyway between the futuristic Supertrees at Gardens by the Bay and catch the evening light show.', 'bullet_points' => ['Airport pickup in an AC vehicle', 'Hotel check-in', 'Gardens by the Bay & Supertree light show'], 'meal_tags' => ['dinner']],
                    ['day_number' => 2, 'title' => 'Universal Studios Singapore', 'route_summary' => 'Sentosa Island · Universal Studios', 'detail' => 'A full day at Universal Studios Singapore on Sentosa Island, Southeast Asia\'s only Universal theme park, with rides themed around Hollywood blockbusters.', 'bullet_points' => ['Full-day Universal Studios Singapore ticket', 'Themed zones: Hollywood, Sci-Fi City, Ancient Egypt & more', 'Return to hotel in the evening'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 3, 'title' => 'Sentosa Cable Car & Singapore Flyer', 'route_summary' => 'Mount Faber → Sentosa Cable Car → Singapore Flyer', 'detail' => 'A scenic cable car ride from Mount Faber over the harbour to Sentosa Island, followed in the evening by the Singapore Flyer, one of the world\'s largest observation wheels.', 'bullet_points' => ['Sentosa Cable Car ride', 'Time at Sentosa\'s beaches/attractions', 'Evening Singapore Flyer ride'], 'meal_tags' => ['breakfast']],
                    ['day_number' => 4, 'title' => 'City Tour & Night Safari', 'route_summary' => 'Merlion Park · Orchard Road · Night Safari', 'detail' => 'A city tour covering Merlion Park and Orchard Road, followed by an evening Night Safari — the world\'s first nocturnal wildlife park — on a tram or walking tour through habitats of over 100 species active after dark.', 'bullet_points' => ['Merlion Park photo stop', 'Orchard Road shopping district', 'Evening Night Safari at Singapore Zoo'], 'meal_tags' => ['breakfast', 'dinner']],
                    ['day_number' => 5, 'title' => 'Departure from Singapore', 'route_summary' => 'Changi Airport departure', 'detail' => 'Check out after breakfast and transfer to Changi Airport for your flight home.', 'bullet_points' => ['Breakfast and hotel checkout', 'Departure transfer to Changi Airport'], 'meal_tags' => ['breakfast']],
                ],
                'hotel_legs' => [
                    ['hotel' => 'Ibis Singapore on Bencoolen', 'room_type' => 'Standard Room', 'day_number' => 1, 'nights' => 4],
                    ['hotel' => 'Marina Bay Sands', 'room_type' => 'Deluxe Room', 'day_number' => 1, 'nights' => 4, 'is_optional' => true, 'note' => 'Premium optional upgrade stay with the iconic rooftop Infinity Pool'],
                ],
                'day_activities' => [
                    ['activity' => 'Gardens by the Bay', 'day_number' => 1, 'is_optional' => true, 'price' => 1800],
                    ['activity' => 'Universal Studios Singapore', 'day_number' => 2],
                    ['activity' => 'Sentosa Cable Car', 'day_number' => 3, 'is_optional' => true, 'price' => 2200],
                    ['activity' => 'Singapore Flyer', 'day_number' => 3, 'is_optional' => true, 'price' => 2500],
                    ['activity' => 'Night Safari', 'day_number' => 4, 'is_optional' => true, 'price' => 3200],
                ],
                'inclusions' => ['4 nights\' accommodation on double occupancy with daily breakfast', 'Universal Studios Singapore full-day ticket', 'Night Safari with dinner', 'All transfers and city tour as per itinerary'],
                'exclusions' => ['International airfare to/from Singapore', 'Singapore visa/arrival card fee (if applicable)', 'Cable car, Singapore Flyer and Gardens by the Bay conservatory tickets unless mentioned', 'Personal expenses, tips and gratuities', 'Travel insurance (recommended)'],
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // The 14 packages themselves: title/slug/pricing/SEO. References a
    // destinationCatalogue() key for the shared real-world day plan.
    // ─────────────────────────────────────────────────────────────────────────
    private function packageDefinitions(): array
    {
        return [
            // ── Kashmir ──────────────────────────────────────────────────────
            [
                'destination' => 'kashmir', 'title' => 'Kashmir Tour Package', 'slug' => 'kashmir-tour-package-from-delhi',
                'price' => 16999, 'discounted_price' => 15499, 'categories' => ['mountains', 'adventure', 'best-seller'], 'is_best_seller' => true,
                'focus_keyword' => 'Kashmir tour package from Delhi',
                'meta_title' => 'Kashmir Tour Package from Delhi | Best Deals 2026 | FareBuzzer Travel',
                'meta_description' => 'Book the best Kashmir tour package from Delhi with FareBuzzer Travel. Explore Srinagar, Gulmarg, Pahalgam & Sonmarg with flights, hotels, sightseeing and transfers.',
                'meta_keywords' => 'kashmir tour package from delhi, kashmir tour package, srinagar gulmarg pahalgam package, 5 nights 6 days kashmir tour',
                'overview' => 'Discover the paradise of Kashmir on this handpicked 6-day tour from Delhi, covering the houseboats of Dal Lake, the snow slopes of Gulmarg, the meadows of Pahalgam and the glaciers of Sonmarg — with hotels, transfers and sightseeing all arranged for you.',
                'seo_content' => $this->kashmirSeoContent(),
                'faqs' => [
                    ['question' => 'What is the best time to visit Kashmir?', 'answer' => 'April to October is ideal for pleasant weather and sightseeing, while December to February suits travellers chasing snow in Gulmarg and Pahalgam. Spring (March-April) brings tulip and blossom season in Srinagar.'],
                    ['question' => 'How long is the flight from Delhi to Srinagar?', 'answer' => 'Direct flights from Delhi to Srinagar take around 1 hour 40 minutes, making Kashmir one of the quickest Himalayan getaways from the capital.'],
                    ['question' => 'Are permits required for this Kashmir tour package?', 'answer' => 'No special permits are needed for Srinagar, Gulmarg, Pahalgam or Sonmarg for Indian nationals. Foreign nationals should carry a valid passport and visa.'],
                    ['question' => 'Is Kashmir safe for tourists right now?', 'answer' => 'Yes, the tourist circuit covering Srinagar, Gulmarg, Pahalgam and Sonmarg sees millions of domestic and international visitors every year and is well-patrolled. We recommend checking current travel advisories closer to your travel date.'],
                    ['question' => 'What should I pack for a Kashmir trip?', 'answer' => 'Carry layered woollens even in summer (evenings are cool), a windproof jacket for Gulmarg, comfortable walking shoes, sunglasses and sunscreen for the snow, and a valid photo ID.'],
                ],
            ],
            [
                'destination' => 'kashmir', 'title' => 'Kashmir Honeymoon Package', 'slug' => 'kashmir-honeymoon-package-from-delhi',
                'price' => 22999, 'discounted_price' => 21499, 'categories' => ['honeymoon', 'mountains'], 'honeymoon' => true,
                'focus_keyword' => 'Kashmir honeymoon package from Delhi',
                'meta_title' => 'Kashmir Honeymoon Package from Delhi | Best Deals 2026 | FareBuzzer Travel',
                'meta_description' => 'Book the best Kashmir honeymoon package from Delhi with FareBuzzer Travel. Enjoy Srinagar\'s houseboats, Gulmarg\'s snow and Pahalgam\'s valleys with romantic stays and transfers.',
                'meta_keywords' => 'kashmir honeymoon package from delhi, kashmir honeymoon package, srinagar honeymoon package, kashmir honeymoon tour',
                'overview' => 'Celebrate new beginnings in "Paradise on Earth" — a romantic 6-day Kashmir honeymoon from Delhi with a candlelit houseboat evening on Dal Lake, snow-time in Gulmarg and quiet valley walks in Pahalgam, with couple-friendly stays arranged throughout.',
                'seo_content' => $this->kashmirHoneymoonSeoContent(),
                'extra_inclusions' => ['Candlelight dinner on one evening', 'Room decoration on arrival day', 'Couple welcome drink on check-in'],
                'faqs' => [
                    ['question' => 'What is the best time for a Kashmir honeymoon?', 'answer' => 'April-May for tulip and blossom season, or December-February for a snow honeymoon in Gulmarg — both are equally romantic depending on whether you prefer flowers or snowfall.'],
                    ['question' => 'What honeymoon inclusions are part of this package?', 'answer' => 'This package includes a candlelight dinner, room decoration on arrival, and a couple welcome drink at check-in — let us know your anniversary or special occasion and we can arrange extra surprises.'],
                    ['question' => 'How long is the flight from Delhi to Srinagar?', 'answer' => 'Direct flights take around 1 hour 40 minutes, so more of your honeymoon is spent in the valley and less in transit.'],
                    ['question' => 'Is Kashmir safe for a honeymoon trip?', 'answer' => 'Yes, the Srinagar-Gulmarg-Pahalgam-Sonmarg circuit is a well-established, well-patrolled tourist route popular with honeymooners year-round.'],
                    ['question' => 'What should we pack for a Kashmir honeymoon?', 'answer' => 'Pack warm layered clothing even in summer, a good jacket for Gulmarg, comfortable footwear for valley walks, and something special for your candlelight dinner evening.'],
                ],
            ],

            // ── Himachal Pradesh ─────────────────────────────────────────────
            [
                'destination' => 'himachal-pradesh', 'title' => 'Himachal Tour Package', 'slug' => 'himachal-tour-package-from-delhi',
                'price' => 9999, 'discounted_price' => 8999, 'categories' => ['mountains', 'adventure', 'family'],
                'focus_keyword' => 'Himachal tour package from Delhi',
                'meta_title' => 'Himachal Tour Package from Delhi | Best Deals 2026 | FareBuzzer Travel',
                'meta_description' => 'Book the best Himachal tour package from Delhi with FareBuzzer Travel. Explore Shimla, Manali, Kufri & Solang Valley with hotels, sightseeing and transfers included.',
                'meta_keywords' => 'himachal tour package from delhi, shimla manali package, himachal tour package, 5 nights 6 days himachal tour',
                'overview' => 'A classic North Indian hill-station holiday from Delhi — 6 days across Shimla\'s colonial Mall Road and Manali\'s Solang Valley adventures, with the Atal Tunnel, Hadimba Temple and Kufri along the way, hotels and transfers all arranged.',
                'seo_content' => $this->himachalSeoContent(),
                'faqs' => [
                    ['question' => 'What is the best time to visit Himachal Pradesh?', 'answer' => 'March-June is ideal for pleasant weather and sightseeing, July-September for lush monsoon greenery (landslide risk on some routes), and December-February for snowfall in Manali and Kufri.'],
                    ['question' => 'How do I reach Shimla and Manali from Delhi?', 'answer' => 'You can fly into Shimla or Kullu-Manali airport, or take an overnight drive/train to Kalka followed by a scenic toy train or road journey — this package includes all inter-city transfers by private vehicle.'],
                    ['question' => 'Are permits required for this Himachal tour?', 'answer' => 'No special permits are needed for Shimla, Manali or Kufri. A permit is only required for the Rohtang Pass extension, which we can arrange in advance if included in your itinerary.'],
                    ['question' => 'Is Himachal Pradesh safe for family travel?', 'answer' => 'Yes, Shimla and Manali are among India\'s most visited and family-friendly hill stations, well-equipped for tourists of all ages.'],
                    ['question' => 'What should I pack for Himachal Pradesh?', 'answer' => 'Carry warm layered clothing year-round (evenings are cool even in summer), sturdy walking shoes, and heavier woollens plus gloves if travelling between December and February.'],
                ],
            ],
            [
                'destination' => 'himachal-pradesh', 'title' => 'Himachal Honeymoon Package', 'slug' => 'himachal-honeymoon-package-from-delhi',
                'price' => 15999, 'discounted_price' => 14499, 'categories' => ['honeymoon', 'mountains'], 'honeymoon' => true,
                'focus_keyword' => 'Himachal honeymoon package from Delhi',
                'meta_title' => 'Himachal Honeymoon Package from Delhi | Best Deals 2026 | FareBuzzer Travel',
                'meta_description' => 'Book the best Himachal honeymoon package from Delhi with FareBuzzer Travel. Enjoy Shimla and Manali with romantic stays, Solang Valley adventures and private transfers.',
                'meta_keywords' => 'himachal honeymoon package from delhi, shimla manali honeymoon package, himachal honeymoon tour',
                'overview' => 'A romantic 6-day Himachal honeymoon from Delhi, pairing Shimla\'s colonial charm with Manali\'s mountain adventures — couple-friendly stays, a candlelight dinner and the snow-dusted Solang Valley for the two of you.',
                'seo_content' => $this->himachalHoneymoonSeoContent(),
                'extra_inclusions' => ['Candlelight dinner on one evening', 'Room decoration on arrival day', 'Couple welcome drink on check-in'],
                'faqs' => [
                    ['question' => 'What is the best time for a Himachal honeymoon?', 'answer' => 'March-June for pleasant weather, or December-February if you want a snow honeymoon in Manali and Kufri — both are popular with couples.'],
                    ['question' => 'What honeymoon inclusions are part of this package?', 'answer' => 'This package includes a candlelight dinner, arrival-day room decoration and a couple welcome drink — tell us about any special occasion and we\'ll arrange extra touches.'],
                    ['question' => 'How do we reach Shimla and Manali from Delhi?', 'answer' => 'By flight into Shimla or Kullu-Manali airport, or an overnight journey to Kalka followed by road transfer — all inter-city transfers are included in this package.'],
                    ['question' => 'Is Himachal Pradesh good for a first honeymoon trip?', 'answer' => 'Yes, Shimla and Manali are among India\'s most popular honeymoon destinations, offering easy accessibility, romantic scenery and a wide range of couple-friendly resorts.'],
                    ['question' => 'What should we pack for a Himachal honeymoon?', 'answer' => 'Warm layered clothing, comfortable walking shoes, and heavier woollens if travelling in winter for snow activities at Solang Valley and Kufri.'],
                ],
            ],

            // ── Kerala ───────────────────────────────────────────────────────
            [
                'destination' => 'kerala', 'title' => 'Kerala Tour Package', 'slug' => 'kerala-tour-package-from-delhi',
                'price' => 11999, 'discounted_price' => 10999, 'categories' => ['family', 'wildlife', 'best-seller'], 'is_best_seller' => true,
                'focus_keyword' => 'Kerala tour package from Delhi',
                'meta_title' => 'Kerala Tour Package from Delhi | Best Deals 2026 | FareBuzzer Travel',
                'meta_description' => 'Book the best Kerala tour package from Delhi with FareBuzzer Travel. Explore Kochi, Munnar, Thekkady & Alleppey with a backwater houseboat stay and private transfers.',
                'meta_keywords' => 'kerala tour package from delhi, kerala tour package, munnar thekkady alleppey package, kerala houseboat package',
                'overview' => 'A complete 6-day Kerala tour from Delhi — Fort Kochi\'s colonial charm, Munnar\'s tea gardens, Thekkady\'s Periyar wildlife safari and an overnight houseboat cruise through the Alleppey backwaters, hotels and transfers included.',
                'seo_content' => $this->keralaSeoContent(),
                'faqs' => [
                    ['question' => 'What is the best time to visit Kerala?', 'answer' => 'September to March offers the most pleasant weather for sightseeing and houseboat cruises, while June to August (monsoon) brings lush greenery and is popular for Ayurvedic wellness stays.'],
                    ['question' => 'How long is the flight from Delhi to Kochi?', 'answer' => 'Direct flights from Delhi to Kochi take around 3 hours, making Kerala an easy long-weekend or week-long getaway.'],
                    ['question' => 'Do we need permits to visit Periyar or Munnar?', 'answer' => 'No special permits are required. A nominal entry fee applies at Periyar Tiger Reserve for the boat safari, which can be paid on the spot or pre-booked.'],
                    ['question' => 'Is Kerala safe and suitable for families?', 'answer' => 'Yes, Kerala is one of India\'s most visitor-friendly states, well set up for families, senior travellers and first-time visitors alike.'],
                    ['question' => 'What should I pack for Kerala?', 'answer' => 'Light cotton clothing, a rain jacket or umbrella (especially June-September), comfortable footwear for plantation walks, and mosquito repellent for the backwaters.'],
                ],
            ],
            [
                'destination' => 'kerala', 'title' => 'Kerala Honeymoon Package', 'slug' => 'kerala-honeymoon-package-from-delhi',
                'price' => 17999, 'discounted_price' => 16499, 'categories' => ['honeymoon', 'beaches'], 'honeymoon' => true,
                'focus_keyword' => 'Kerala honeymoon package from Delhi',
                'meta_title' => 'Kerala Honeymoon Package from Delhi | Best Deals 2026 | FareBuzzer Travel',
                'meta_description' => 'Book the best Kerala honeymoon package from Delhi with FareBuzzer Travel. Enjoy Munnar\'s tea gardens and a private Alleppey houseboat cruise with romantic stays included.',
                'meta_keywords' => 'kerala honeymoon package from delhi, kerala honeymoon tour, alleppey houseboat honeymoon, munnar honeymoon package',
                'overview' => 'God\'s Own Country, made for two — a 6-day Kerala honeymoon from Delhi through Munnar\'s misty tea plantations and an overnight private houseboat cruise through the Alleppey backwaters, with romantic touches arranged throughout.',
                'seo_content' => $this->keralaHoneymoonSeoContent(),
                'extra_inclusions' => ['Candlelight dinner on the houseboat', 'Room decoration on arrival day', 'Couple welcome drink on check-in'],
                'faqs' => [
                    ['question' => 'What is the best time for a Kerala honeymoon?', 'answer' => 'September to March for the most pleasant weather, or June-August if you\'d prefer a monsoon honeymoon with lush green backwaters and Ayurvedic spa sessions.'],
                    ['question' => 'What honeymoon inclusions are part of this package?', 'answer' => 'This package includes a candlelight dinner on your houseboat evening, arrival-day room decoration and a couple welcome drink at check-in.'],
                    ['question' => 'How long is the flight from Delhi to Kochi?', 'answer' => 'Direct flights take around 3 hours, so more of your honeymoon is spent in Kerala and less in transit.'],
                    ['question' => 'Is the Alleppey houseboat private for couples?', 'answer' => 'Yes, your houseboat cabin is private, with meals cooked fresh on board and a dedicated crew — ideal for a quiet, romantic evening on the backwaters.'],
                    ['question' => 'What should we pack for a Kerala honeymoon?', 'answer' => 'Light cotton clothing, a rain jacket or umbrella depending on season, comfortable footwear for plantation walks, and something special for your candlelight houseboat dinner.'],
                ],
            ],

            // ── Sikkim ───────────────────────────────────────────────────────
            [
                'destination' => 'sikkim', 'title' => 'Sikkim Tour Package', 'slug' => 'sikkim-tour-package-from-delhi',
                'price' => 17999, 'discounted_price' => 16499, 'categories' => ['mountains', 'adventure'],
                'focus_keyword' => 'Sikkim tour package from Delhi',
                'meta_title' => 'Sikkim Tour Package from Delhi | Best Deals 2026 | FareBuzzer Travel',
                'meta_description' => 'Book the best Sikkim tour package from Delhi with FareBuzzer Travel. Explore Gangtok, Pelling & Lachung with Tsomgo Lake, Nathula Pass and Rumtek Monastery included.',
                'meta_keywords' => 'sikkim tour package from delhi, gangtok pelling lachung package, sikkim tour package, tsomgo lake nathula pass tour',
                'overview' => 'An unspoilt Eastern Himalayan escape from Delhi — 6 days across Gangtok\'s monasteries, the turquoise Tsomgo Lake, the high-altitude Nathula Pass and the alpine valleys of Lachung and Pelling, with permits, hotels and transfers arranged.',
                'seo_content' => $this->sikkimSeoContent(),
                'faqs' => [
                    ['question' => 'What is the best time to visit Sikkim?', 'answer' => 'March-June and September-December offer the clearest mountain views and pleasant weather; the Yumthang Valley "Valley of Flowers" is at its best in April-May.'],
                    ['question' => 'What permits are required for this Sikkim tour?', 'answer' => 'Indian nationals need an inner-line permit for Tsomgo Lake, Nathula Pass and North Sikkim (Lachung), which we arrange for you — carry 2 passport photos and a valid photo ID.'],
                    ['question' => 'Is Nathula Pass always open?', 'answer' => 'Nathula Pass is subject to weather conditions and border authority approval, and may occasionally close at short notice — we\'ll always share the latest status before your trip.'],
                    ['question' => 'Is Sikkim safe for tourists?', 'answer' => 'Yes, Sikkim is one of India\'s safest and best-organised states for tourism, with well-marked routes and mandatory registered vehicles for the border-area excursions.'],
                    ['question' => 'What should I pack for Sikkim?', 'answer' => 'Heavy woollens even in summer (Tsomgo Lake and Lachung are well above 10,000 ft), waterproof shoes, sunglasses for the snow, and a valid photo ID with passport-sized photos for permits.'],
                ],
            ],
            [
                'destination' => 'sikkim', 'title' => 'Sikkim Honeymoon Package', 'slug' => 'sikkim-honeymoon-package-from-delhi',
                'price' => 23999, 'discounted_price' => 21999, 'categories' => ['honeymoon', 'mountains'], 'honeymoon' => true,
                'focus_keyword' => 'Sikkim honeymoon package from Delhi',
                'meta_title' => 'Sikkim Honeymoon Package from Delhi | Best Deals 2026 | FareBuzzer Travel',
                'meta_description' => 'Book the best Sikkim honeymoon package from Delhi with FareBuzzer Travel. Enjoy Gangtok, Lachung and Pelling with romantic mountain stays and permits included.',
                'meta_keywords' => 'sikkim honeymoon package from delhi, gangtok honeymoon package, sikkim honeymoon tour, lachung honeymoon',
                'overview' => 'A quiet Eastern Himalayan honeymoon from Delhi, away from the usual hill-station crowds — Gangtok\'s monasteries, the glacial Tsomgo Lake, and the alpine valleys of Lachung and Pelling, with romantic touches arranged for the two of you.',
                'seo_content' => $this->sikkimHoneymoonSeoContent(),
                'extra_inclusions' => ['Candlelight dinner on one evening', 'Room decoration on arrival day', 'Couple welcome drink on check-in'],
                'faqs' => [
                    ['question' => 'What is the best time for a Sikkim honeymoon?', 'answer' => 'April-May for the Yumthang Valley "Valley of Flowers" in full bloom, or March-June and September-December for clear mountain views.'],
                    ['question' => 'What honeymoon inclusions are part of this package?', 'answer' => 'This package includes a candlelight dinner, arrival-day room decoration and a couple welcome drink — let us know about any special occasion for extra touches.'],
                    ['question' => 'What permits do we need for this Sikkim honeymoon?', 'answer' => 'An inner-line permit is required for Tsomgo Lake, Nathula Pass and Lachung, which we arrange for you — just carry 2 passport photos and valid photo ID.'],
                    ['question' => 'Is Sikkim a good offbeat honeymoon choice?', 'answer' => 'Yes, Sikkim offers the scenery of a classic Himalayan honeymoon with far fewer crowds than Shimla or Manali, plus a genuinely unique Buddhist-monastery culture.'],
                    ['question' => 'What should we pack for a Sikkim honeymoon?', 'answer' => 'Heavy woollens even in summer, waterproof shoes, and valid photo ID with passport photos for the border-area permits.'],
                ],
            ],

            // ── Andaman ──────────────────────────────────────────────────────
            [
                'destination' => 'andaman', 'title' => 'Andaman Tour Package', 'slug' => 'andaman-tour-package-from-delhi',
                'price' => 14999, 'discounted_price' => 13499, 'categories' => ['beaches', 'adventure', 'family'],
                'focus_keyword' => 'Andaman tour package from Delhi',
                'meta_title' => 'Andaman Tour Package from Delhi | Best Deals 2026 | FareBuzzer Travel',
                'meta_description' => 'Book the best Andaman tour package from Delhi with FareBuzzer Travel. Explore Port Blair, Havelock & Neil Island with Radhanagar Beach, ferries and transfers included.',
                'meta_keywords' => 'andaman tour package from delhi, port blair havelock neil island package, andaman tour package, radhanagar beach tour',
                'overview' => 'India\'s best beach escape, in a 5-day package from Delhi — Port Blair\'s Cellular Jail, the powder-white Radhanagar Beach on Havelock, and the quiet shores of Neil Island, with inter-island ferries, hotels and transfers included.',
                'seo_content' => $this->andamanSeoContent(),
                'faqs' => [
                    ['question' => 'What is the best time to visit Andaman?', 'answer' => 'October to May offers calm seas, clear skies and the best conditions for beaches and scuba diving; June to September is monsoon season with rougher ferry crossings.'],
                    ['question' => 'How long is the flight from Delhi to Port Blair?', 'answer' => 'Direct flights from Delhi to Port Blair take around 5 hours; connecting flights via Chennai or Kolkata are also common.'],
                    ['question' => 'Are permits required for domestic tourists in Andaman?', 'answer' => 'Indian nationals don\'t need a special permit for Port Blair, Havelock or Neil Island — just a valid photo ID. Foreign nationals require a Restricted Area Permit, issued on arrival.'],
                    ['question' => 'Is scuba diving included in this Andaman package?', 'answer' => 'Scuba diving and snorkelling at Havelock are offered as optional paid add-ons so you can choose based on comfort and experience level — our team can help you pick the right session.'],
                    ['question' => 'What should I pack for the Andaman Islands?', 'answer' => 'Light breathable clothing, swimwear, reef-safe sunscreen, water shoes for rocky beach patches, and a waterproof bag for the inter-island ferries.'],
                ],
            ],
            [
                'destination' => 'andaman', 'title' => 'Andaman Honeymoon Package', 'slug' => 'andaman-honeymoon-package-from-delhi',
                'price' => 21999, 'discounted_price' => 19999, 'categories' => ['honeymoon', 'beaches'], 'honeymoon' => true,
                'focus_keyword' => 'Andaman honeymoon package from Delhi',
                'meta_title' => 'Andaman Honeymoon Package from Delhi | Best Deals 2026 | FareBuzzer Travel',
                'meta_description' => 'Book the best Andaman honeymoon package from Delhi with FareBuzzer Travel. Enjoy Havelock\'s Radhanagar Beach and Neil Island with romantic beachfront stays included.',
                'meta_keywords' => 'andaman honeymoon package from delhi, havelock honeymoon package, andaman honeymoon tour, radhanagar beach honeymoon',
                'overview' => 'A dreamy island honeymoon from Delhi — 5 days centred on Havelock\'s Radhanagar Beach, ranked among Asia\'s best, plus the quieter shores of Neil Island, with beachfront stays and romantic touches arranged for the two of you.',
                'seo_content' => $this->andamanHoneymoonSeoContent(),
                'extra_inclusions' => ['Candlelight beachside dinner on one evening', 'Room decoration on arrival day', 'Couple welcome drink on check-in'],
                'faqs' => [
                    ['question' => 'What is the best time for an Andaman honeymoon?', 'answer' => 'October to May for calm seas, clear skies and the best beach weather — the islands are busiest (and most festive) around the New Year period.'],
                    ['question' => 'What honeymoon inclusions are part of this package?', 'answer' => 'This package includes a candlelight beachside dinner, arrival-day room decoration and a couple welcome drink on check-in.'],
                    ['question' => 'How long is the flight from Delhi to Port Blair?', 'answer' => 'Direct flights take around 5 hours, with the Havelock ferry adding roughly 1.5-2 hours from Port Blair.'],
                    ['question' => 'Is Havelock a good honeymoon destination?', 'answer' => 'Yes, Havelock (Swaraj Dweep) is widely considered India\'s best honeymoon beach destination, with Radhanagar Beach repeatedly ranked among Asia\'s finest.'],
                    ['question' => 'What should we pack for an Andaman honeymoon?', 'answer' => 'Light resort wear, swimwear, reef-safe sunscreen, and something special for your candlelight beachside dinner evening.'],
                ],
            ],

            // ── International ────────────────────────────────────────────────
            [
                'destination' => 'vietnam', 'title' => 'Vietnam Tour Package', 'slug' => 'vietnam-tour-package-from-india',
                'price' => 14499, 'discounted_price' => 13499, 'categories' => ['beaches', 'adventure', 'best-seller'], 'is_best_seller' => true,
                'focus_keyword' => 'Vietnam tour package from India',
                'meta_title' => 'Vietnam Tour Package from India | Best Deals 2026 | FareBuzzer Travel',
                'meta_description' => 'Book the best Vietnam tour package from India with FareBuzzer Travel. Explore Hanoi, Ha Long Bay, Da Nang & Hoi An with cruise stays, flights and transfers included.',
                'meta_keywords' => 'vietnam tour package from india, ha long bay cruise package, vietnam package, hanoi da nang hoi an tour',
                'overview' => 'A complete 6-day Vietnam tour from India — Hanoi\'s Old Quarter, an overnight cruise through the limestone karsts of Ha Long Bay, and the beaches and heritage streets of Da Nang and Hoi An, flights, hotels and transfers included.',
                'seo_content' => $this->vietnamSeoContent(),
                'faqs' => [
                    ['question' => 'What is the best time to visit Vietnam?', 'answer' => 'October to April offers the most pleasant weather across Hanoi, Ha Long Bay and Da Nang, with dry, mild conditions ideal for sightseeing and the cruise.'],
                    ['question' => 'Do Indian travellers need a visa for Vietnam?', 'answer' => 'Yes, Indian nationals need a Vietnam e-visa, which is straightforward to apply for online — we can guide you through the process.'],
                    ['question' => 'How long is the flight from India to Vietnam?', 'answer' => 'Direct and one-stop flights from major Indian cities to Hanoi or Da Nang typically take 5-7 hours depending on your departure city and layover.'],
                    ['question' => 'Is Vietnam safe for Indian tourists?', 'answer' => 'Yes, Vietnam is a popular, well-developed tourist destination with a strong safety record, especially along the Hanoi-Ha Long-Da Nang-Hoi An circuit.'],
                    ['question' => 'What should I pack for Vietnam?', 'answer' => 'Light breathable clothing, a light jacket for Hanoi evenings, comfortable walking shoes for Hoi An\'s old streets, and swimwear for Da Nang\'s beaches.'],
                ],
            ],
            [
                'destination' => 'malaysia', 'title' => 'Malaysia Tour Package', 'slug' => 'malaysia-tour-package-from-india',
                'price' => 21999, 'discounted_price' => 19999, 'categories' => ['family', 'adventure', 'best-seller'], 'is_best_seller' => true,
                'focus_keyword' => 'Malaysia tour package from India',
                'meta_title' => 'Malaysia Tour Package from India | Best Deals 2026 | FareBuzzer Travel',
                'meta_description' => 'Book the best Malaysia tour package from India with FareBuzzer Travel. Explore Kuala Lumpur, Genting Highlands & Langkawi with flights, hotels and transfers included.',
                'meta_keywords' => 'malaysia tour package from india, kuala lumpur genting langkawi package, malaysia package, langkawi tour',
                'overview' => 'A compact 5-day Malaysia multi-city holiday from India — Kuala Lumpur\'s Petronas Towers and Batu Caves, the cool hilltop resort of Genting Highlands, and the beach island of Langkawi, flights, hotels and transfers included.',
                'seo_content' => $this->malaysiaSeoContent(),
                'faqs' => [
                    ['question' => 'What is the best time to visit Malaysia?', 'answer' => 'December to February and June to August tend to have the least rainfall in Kuala Lumpur and Langkawi, though Malaysia is a year-round tropical destination.'],
                    ['question' => 'Do Indian travellers need a visa for Malaysia?', 'answer' => 'Indian nationals can apply for a Malaysia eVISA or eNTRI online, both straightforward processes — we can guide you through the requirements.'],
                    ['question' => 'How long is the flight from India to Kuala Lumpur?', 'answer' => 'Direct flights from major Indian cities to Kuala Lumpur typically take 4.5-5.5 hours.'],
                    ['question' => 'Is this Malaysia package suitable for families?', 'answer' => 'Yes, Kuala Lumpur, Genting Highlands and Langkawi are all very family-friendly, with theme parks, cable cars and beaches suited to travellers of all ages.'],
                    ['question' => 'What should I pack for Malaysia?', 'answer' => 'Light, breathable tropical clothing, a light jacket for the cooler Genting Highlands, swimwear for Langkawi, and an umbrella for sudden tropical showers.'],
                ],
            ],
            [
                'destination' => 'dubai', 'title' => 'Dubai Tour Package', 'slug' => 'dubai-tour-package-from-india',
                'price' => 23000, 'discounted_price' => 20999, 'categories' => ['luxury', 'adventure', 'best-seller'], 'is_best_seller' => true,
                'focus_keyword' => 'Dubai tour package from India',
                'meta_title' => 'Dubai Tour Package from India | Best Deals 2026 | FareBuzzer Travel',
                'meta_description' => 'Book the best Dubai tour package from India with FareBuzzer Travel. Enjoy Burj Khalifa, desert safari, dhow cruise and an Abu Dhabi day trip with flights and hotels.',
                'meta_keywords' => 'dubai tour package from india, dubai package, burj khalifa desert safari package, dubai abu dhabi tour',
                'overview' => 'A fast, glamorous 5-day Dubai getaway from India — the Burj Khalifa\'s observation decks, a desert safari with BBQ dinner under the stars, a Dubai Marina dhow cruise, and a day trip to Abu Dhabi\'s Grand Mosque, flights, hotels and transfers included.',
                'seo_content' => $this->dubaiSeoContent(),
                'faqs' => [
                    ['question' => 'What is the best time to visit Dubai?', 'answer' => 'November to March offers the most comfortable weather for sightseeing and the desert safari, with temperatures dropping well below the scorching summer highs.'],
                    ['question' => 'Do Indian travellers need a visa for Dubai?', 'answer' => 'Yes, Indian nationals need a UAE visa, available as a tourist visa on arrival for eligible passport holders or applied for online in advance — we can guide you through the process.'],
                    ['question' => 'How long is the flight from India to Dubai?', 'answer' => 'Direct flights from most major Indian cities to Dubai take around 3-4 hours, making it one of the quickest international getaways.'],
                    ['question' => 'What is included in the desert safari?', 'answer' => 'The desert safari includes 4x4 dune bashing, camel riding, sandboarding and a BBQ dinner with live entertainment at a desert camp.'],
                    ['question' => 'What should I pack for Dubai?', 'answer' => 'Light, breathable clothing for the day, a light jacket for air-conditioned malls and the desert evening, modest wear for the Sheikh Zayed Grand Mosque, and comfortable walking shoes.'],
                ],
            ],
            [
                'destination' => 'singapore', 'title' => 'Singapore Tour Package', 'slug' => 'singapore-tour-package-from-india',
                'price' => 36999, 'discounted_price' => 33999, 'categories' => ['family', 'luxury', 'best-seller'], 'is_best_seller' => true,
                'focus_keyword' => 'Singapore tour package from India',
                'meta_title' => 'Singapore Tour Package from India | Best Deals 2026 | FareBuzzer Travel',
                'meta_description' => 'Book the best Singapore tour package from India with FareBuzzer Travel. Enjoy Universal Studios, Gardens by the Bay, Sentosa and Night Safari with flights and hotels.',
                'meta_keywords' => 'singapore tour package from india, universal studios singapore package, singapore family package, sentosa tour',
                'overview' => 'Asia\'s most polished city-break, in a 5-day package from India — Universal Studios and Sentosa Island, the futuristic Gardens by the Bay, panoramic views from the Singapore Flyer, and an evening Night Safari, flights, hotels and transfers included.',
                'seo_content' => $this->singaporeSeoContent(),
                'faqs' => [
                    ['question' => 'What is the best time to visit Singapore?', 'answer' => 'Singapore is a year-round destination close to the equator; February to April tends to have slightly less rainfall, but light showers are possible any month.'],
                    ['question' => 'Do Indian travellers need a visa for Singapore?', 'answer' => 'Yes, Indian nationals need a Singapore visa, applied for online in advance — a straightforward process we can guide you through.'],
                    ['question' => 'How long is the flight from India to Singapore?', 'answer' => 'Direct flights from major Indian cities to Singapore typically take 5-6 hours.'],
                    ['question' => 'Is this Singapore package good for families with kids?', 'answer' => 'Yes, Universal Studios, Gardens by the Bay and the Night Safari are all very family-friendly, and Singapore is consistently ranked one of the safest cities in the world for travellers.'],
                    ['question' => 'What should I pack for Singapore?', 'answer' => 'Light, breathable tropical clothing, a light jacket for air-conditioned malls and the Night Safari, comfortable walking shoes for the theme park, and an umbrella for sudden showers.'],
                ],
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Long-form SEO content blocks (~600-900 words each), one per package, all
    // hand-written and unique — no shared template text between destinations.
    // ─────────────────────────────────────────────────────────────────────────

    private function kashmirSeoContent(): string
    {
        return <<<'HTML'
<p>Kashmir, often called "Paradise on Earth", is one of India's most magical Himalayan destinations — and a <strong>Kashmir tour package from Delhi</strong> is one of the easiest ways to experience it. From the houseboats and shikaras of Dal Lake in Srinagar to the snow-draped slopes of Gulmarg, the meadows of Pahalgam and the glaciers of Sonmarg, this 5 nights 6 days itinerary covers the full Kashmir circuit without the stress of planning it yourself.</p>

<h2>Why Choose a Kashmir Tour Package from Delhi?</h2>
<p>Delhi is the most convenient gateway to the Kashmir valley. Direct flights to Srinagar take just around 1 hour 40 minutes, meaning you can be sipping Kahwa tea on a Dal Lake houseboat the same evening you leave home. A pre-planned tour package removes the guesswork of altitude-appropriate stays, permits and driver logistics, so you can focus purely on the scenery.</p>
<ul>
<li>Flight booking assistance from Delhi to Srinagar</li>
<li>Hotel accommodation across Srinagar and Pahalgam</li>
<li>Airport transfers in a private AC vehicle</li>
<li>Private sightseeing with an experienced local driver</li>
<li>Flexible itineraries that can be customised on request</li>
<li>Affordable, transparently priced packages</li>
<li>24/7 customer support throughout your trip</li>
</ul>

<h2>Popular Destinations Included</h2>

<h3>Srinagar</h3>
<p>Srinagar is the heart of any Kashmir trip, built around the shimmering Dal Lake and its floating gardens.</p>
<ul>
<li>Shikara ride on Dal Lake</li>
<li>Houseboat stay experience</li>
<li>Nishat Bagh Mughal garden</li>
<li>Shalimar Bagh Mughal garden</li>
<li>Pari Mahal hilltop viewpoint</li>
</ul>

<h3>Gulmarg</h3>
<p>Gulmarg, the "Meadow of Flowers", is Kashmir's premier hill resort and winter sports hub, reached on a scenic day trip from Srinagar.</p>
<ul>
<li>Gulmarg Gondola cable car ride</li>
<li>Skiing (winter season)</li>
<li>Snowboarding (winter season)</li>
<li>Snow scooter rides</li>
<li>ATV rides and horse riding</li>
</ul>

<h3>Pahalgam</h3>
<p>Pahalgam, the "Valley of Shepherds", is a peaceful base of pine forests and river valleys, and the traditional starting point of the Amarnath Yatra.</p>
<ul>
<li>Betaab Valley</li>
<li>Aru Valley</li>
<li>Chandanwari</li>
<li>Lidder River banks</li>
</ul>

<h3>Sonmarg</h3>
<p>Sonmarg, the "Meadow of Gold", sits at the edge of the Himalayan glaciers and makes for a memorable day trip from Srinagar.</p>
<ul>
<li>Thajiwas Glacier</li>
<li>Pony rides to the glacier base</li>
</ul>

<h2>Best Time to Visit Kashmir</h2>
<p><strong>Spring (March-May):</strong> Tulip gardens and orchards bloom across Srinagar — one of the most photogenic times to visit.</p>
<p><strong>Summer (June-August):</strong> Pleasant daytime temperatures (15-30°C) make this the most popular season for sightseeing and houseboat stays.</p>
<p><strong>Monsoon (July-September):</strong> Kashmir sees only light monsoon showers compared to the rest of India, and the valley stays lush and green.</p>
<p><strong>Winter (December-February):</strong> Snowfall transforms Gulmarg into a skiing destination and Srinagar's Dal Lake can partially freeze — ideal for travellers chasing snow.</p>
HTML;
    }

    private function kashmirHoneymoonSeoContent(): string
    {
        return <<<'HTML'
<p>Few places in India feel as made-for-two as Kashmir. A <strong>Kashmir honeymoon package from Delhi</strong> takes you straight from the everyday into a candlelit houseboat evening on Dal Lake, snowy walks in Gulmarg and quiet valley strolls in Pahalgam — a complete romantic escape without the hassle of planning it yourself.</p>

<h2>Why Choose a Kashmir Honeymoon Package from Delhi?</h2>
<p>With direct flights of just around 1 hour 40 minutes from Delhi to Srinagar, more of your honeymoon is spent together in the valley and less of it in transit. This package is built specifically around couples — romantic stays, a candlelight dinner and thoughtful little touches — so all you need to do is enjoy each other's company.</p>
<ul>
<li>Flight booking assistance from Delhi to Srinagar</li>
<li>Romantic hotel and houseboat accommodation</li>
<li>Private airport and sightseeing transfers</li>
<li>Candlelight dinner and arrival-day room decoration</li>
<li>Experienced, discreet local drivers</li>
<li>Flexible itineraries customisable for your special occasions</li>
<li>24/7 support throughout your honeymoon</li>
</ul>

<h2>Popular Destinations Included</h2>

<h3>Srinagar</h3>
<p>Begin your honeymoon on a private houseboat overlooking Dal Lake, with an evening shikara ride past floating gardens as the sun sets over the Zabarwan hills.</p>
<ul>
<li>Shikara ride on Dal Lake</li>
<li>Private houseboat stay experience</li>
<li>Nishat Bagh & Shalimar Bagh gardens</li>
<li>Pari Mahal sunset viewpoint</li>
</ul>

<h3>Gulmarg</h3>
<p>Gulmarg's snow-covered meadows make for a picture-perfect couple's day out, with the Gondola cable car ride offering sweeping views to share.</p>
<ul>
<li>Gulmarg Gondola cable car ride</li>
<li>Snow scooter rides for two</li>
<li>Skiing/snowboarding (winter season)</li>
<li>Horse riding through the meadow</li>
</ul>

<h3>Pahalgam</h3>
<p>Pahalgam's quiet river valleys are ideal for slow, unhurried walks — just the two of you and the sound of the Lidder River.</p>
<ul>
<li>Betaab Valley walk</li>
<li>Aru Valley</li>
<li>Chandanwari</li>
<li>Riverside picnic spots along the Lidder</li>
</ul>

<h3>Sonmarg</h3>
<p>A day trip to Sonmarg's glaciers adds a final dose of Himalayan drama to your honeymoon album.</p>
<ul>
<li>Thajiwas Glacier</li>
<li>Pony ride to the glacier base for two</li>
</ul>

<h2>Best Time for a Kashmir Honeymoon</h2>
<p><strong>Spring (March-May):</strong> Tulip and blossom season across Srinagar — arguably the most romantic time to visit.</p>
<p><strong>Summer (June-August):</strong> Comfortable weather (15-30°C) for houseboat evenings and valley walks.</p>
<p><strong>Monsoon (July-September):</strong> Light showers and a lush, green, quieter valley for couples who prefer fewer crowds.</p>
<p><strong>Winter (December-February):</strong> A snow honeymoon in Gulmarg, with the possibility of a partially frozen Dal Lake — pure Kashmir magic.</p>
HTML;
    }

    private function himachalSeoContent(): string
    {
        return <<<'HTML'
<p>Himachal Pradesh is the classic North Indian hill-station holiday, and a <strong>Himachal tour package from Delhi</strong> brings together its two most-loved towns in one easy trip — Shimla's colonial-era charm and Manali's adventure-filled mountains, connected by some of the most scenic drives in India.</p>

<h2>Why Choose a Himachal Tour Package from Delhi?</h2>
<p>Shimla and Manali are both within comfortable striking distance of Delhi by flight or overnight road/rail journey, making Himachal one of the most accessible Himalayan escapes. A ready-made package takes care of hotel bookings, the Shimla-Manali inter-city transfer and every sightseeing stop, so you can simply enjoy the mountains.</p>
<ul>
<li>Flight/road transfer assistance from Delhi</li>
<li>Hotel accommodation in both Shimla and Manali</li>
<li>Airport/station transfers in a private AC vehicle</li>
<li>Private sightseeing with an experienced local driver</li>
<li>Flexible itineraries that can be customised on request</li>
<li>Affordable, transparently priced packages</li>
<li>24/7 customer support throughout your trip</li>
</ul>

<h2>Popular Destinations Included</h2>

<h3>Shimla</h3>
<p>Shimla, Himachal Pradesh's colonial-era capital, is built around the pedestrian-only Mall Road and Ridge, with Raj-era architecture at every turn.</p>
<ul>
<li>Mall Road & The Ridge walk</li>
<li>Christ Church</li>
<li>Jakhoo Temple viewpoint</li>
</ul>

<h3>Manali</h3>
<p>Manali is Himachal's adventure capital, a base for exploring the Beas River valley and the high-altitude passes beyond.</p>
<ul>
<li>Hadimba Devi Temple</li>
<li>Old Manali cafes</li>
<li>Vashisht hot springs</li>
</ul>

<h3>Kufri</h3>
<p>A short drive from Shimla, Kufri is a popular stop for pony rides and, in winter, snow activities.</p>
<ul>
<li>Pony rides</li>
<li>Himalayan Nature Park</li>
<li>Seasonal snow activities</li>
</ul>

<h3>Solang Valley</h3>
<p>Solang Valley, near Manali, is Himachal's best-known adventure hub, framed by snow-capped peaks on every side.</p>
<ul>
<li>Paragliding</li>
<li>Zorbing</li>
<li>Snow scooter rides</li>
<li>Skiing (winter season)</li>
<li>Atal Tunnel drive-through</li>
</ul>

<h2>Best Time to Visit Himachal Pradesh</h2>
<p><strong>Spring (March-May):</strong> Orchards bloom across the Kullu Valley and temperatures are pleasant for sightseeing.</p>
<p><strong>Summer (June-August):</strong> Cool, comfortable weather makes this the most popular season, especially for travellers escaping the plains' heat.</p>
<p><strong>Monsoon (July-September):</strong> Lush green hillsides, though some mountain routes may see occasional landslides — check conditions before travel.</p>
<p><strong>Winter (December-February):</strong> Snowfall in Manali, Solang Valley and Kufri, ideal for skiing and snow activities.</p>
HTML;
    }

    private function himachalHoneymoonSeoContent(): string
    {
        return <<<'HTML'
<p>A <strong>Himachal honeymoon package from Delhi</strong> pairs two of North India's most romantic hill towns — Shimla's colonial charm and Manali's mountain adventures — into one easy, beautifully paced trip for newlyweds.</p>

<h2>Why Choose a Himachal Honeymoon Package from Delhi?</h2>
<p>Himachal Pradesh's easy accessibility from Delhi, combined with its wide choice of couple-friendly resorts, makes it one of India's most popular first-honeymoon destinations. This package is built around romantic touches — a candlelight dinner, room decoration and thoughtful little extras — layered on top of a well-planned Shimla-Manali circuit.</p>
<ul>
<li>Flight/road transfer assistance from Delhi</li>
<li>Romantic hotel accommodation in Shimla and Manali</li>
<li>Private airport/station and sightseeing transfers</li>
<li>Candlelight dinner and arrival-day room decoration</li>
<li>Experienced, discreet local drivers</li>
<li>Flexible itineraries customisable for your special occasions</li>
<li>24/7 support throughout your honeymoon</li>
</ul>

<h2>Popular Destinations Included</h2>

<h3>Shimla</h3>
<p>Start your honeymoon with an evening stroll along Shimla's Mall Road and Ridge, the colonial-era heart of the town.</p>
<ul>
<li>Mall Road & The Ridge evening walk</li>
<li>Christ Church</li>
<li>Jakhoo Temple viewpoint</li>
</ul>

<h3>Manali</h3>
<p>Manali's riverside setting and mountain backdrop make it a favourite for couples looking to slow down together.</p>
<ul>
<li>Hadimba Devi Temple</li>
<li>Old Manali cafes for two</li>
<li>Vashisht hot springs</li>
</ul>

<h3>Kufri</h3>
<p>A scenic detour from Shimla, perfect for a couple's pony ride with mountain views all around.</p>
<ul>
<li>Pony rides</li>
<li>Himalayan Nature Park</li>
</ul>

<h3>Solang Valley</h3>
<p>Add a little adrenaline to your honeymoon with Solang Valley's adventure activities, framed by snow peaks.</p>
<ul>
<li>Paragliding for two</li>
<li>Zorbing</li>
<li>Snow scooter rides</li>
<li>Atal Tunnel drive-through</li>
</ul>

<h2>Best Time for a Himachal Honeymoon</h2>
<p><strong>Spring (March-May):</strong> Blooming orchards across the Kullu Valley and pleasant weather for sightseeing.</p>
<p><strong>Summer (June-August):</strong> Cool, comfortable temperatures, ideal for escaping the summer heat together.</p>
<p><strong>Monsoon (July-September):</strong> A quieter, greener Himachal for couples who prefer fewer crowds — check road conditions before travel.</p>
<p><strong>Winter (December-February):</strong> A snow honeymoon in Manali and Kufri, with skiing and snow activities at Solang Valley.</p>
HTML;
    }

    private function keralaSeoContent(): string
    {
        return <<<'HTML'
<p>Kerala, God's Own Country, is one of India's most complete holiday destinations, and a <strong>Kerala tour package from Delhi</strong> brings together its best sides in one trip — Fort Kochi's colonial waterfront, Munnar's emerald tea gardens, Thekkady's wildlife-rich forests and an unforgettable overnight houseboat cruise through the Alleppey backwaters.</p>

<h2>Why Choose a Kerala Tour Package from Delhi?</h2>
<p>Direct flights from Delhi to Kochi take around 3 hours, making this lush southern state an easy week-long escape. A pre-planned package handles the hill-to-backwater logistics — hotel-to-houseboat transitions, plantation-tour bookings and inter-city drives — so you can focus on the scenery and the slow pace of backwater life.</p>
<ul>
<li>Flight booking assistance from Delhi to Kochi</li>
<li>Hotel accommodation plus an overnight houseboat stay</li>
<li>Airport transfers in a private AC vehicle</li>
<li>Private sightseeing with an experienced local driver</li>
<li>Flexible itineraries that can be customised on request</li>
<li>Affordable, transparently priced packages</li>
<li>24/7 customer support throughout your trip</li>
</ul>

<h2>Popular Destinations Included</h2>

<h3>Kochi</h3>
<p>Fort Kochi's colonial waterfront blends Portuguese, Dutch and British heritage in one walkable old town.</p>
<ul>
<li>Chinese fishing nets at sunset</li>
<li>Fort Kochi heritage walk</li>
<li>Live Kathakali dance performance</li>
</ul>

<h3>Munnar</h3>
<p>Munnar's rolling tea plantations, set against misty hills, are one of Kerala's most photographed landscapes.</p>
<ul>
<li>Tea plantation & tea museum tour</li>
<li>Eravikulam viewpoint (seasonal)</li>
<li>Mattupetty & Echo Point</li>
</ul>

<h3>Thekkady</h3>
<p>Thekkady is home to the Periyar Tiger Reserve, one of South India's most rewarding wildlife destinations.</p>
<ul>
<li>Periyar Lake boat safari</li>
<li>Spice plantation walks</li>
<li>Wildlife spotting (elephants, bison, birdlife)</li>
</ul>

<h3>Alleppey</h3>
<p>Alleppey's backwaters are Kerala's signature experience — a slow cruise through palm-lined canals aboard a traditional houseboat.</p>
<ul>
<li>Overnight kettuvallam houseboat cruise</li>
<li>Meals cooked fresh on board</li>
<li>Paddy field and village views</li>
</ul>

<h2>Best Time to Visit Kerala</h2>
<p><strong>Spring (March-May):</strong> Warm, humid weather — best suited to early-morning sightseeing and houseboat cruises.</p>
<p><strong>Summer/Pre-Monsoon (June):</strong> The build-up to the monsoon brings dramatic skies over the backwaters.</p>
<p><strong>Monsoon (June-September):</strong> Kerala's most atmospheric season — lush greenery everywhere and popular for Ayurvedic wellness stays.</p>
<p><strong>Winter (October-February):</strong> The most popular season, with cool, dry, pleasant weather ideal for hill and backwater sightseeing alike.</p>
HTML;
    }

    private function keralaHoneymoonSeoContent(): string
    {
        return <<<'HTML'
<p>A <strong>Kerala honeymoon package from Delhi</strong> pairs Munnar's misty tea gardens with a private overnight houseboat cruise through the Alleppey backwaters — one of India's most romantic honeymoon combinations, with the pace deliberately slow.</p>

<h2>Why Choose a Kerala Honeymoon Package from Delhi?</h2>
<p>Direct flights from Delhi to Kochi take around 3 hours, and from there Kerala unfolds gently — hill stations, spice gardens and backwaters, without the rush of a typical sightseeing trip. This package layers romantic touches, including a candlelight dinner on your private houseboat, on top of a well-paced honeymoon circuit.</p>
<ul>
<li>Flight booking assistance from Delhi to Kochi</li>
<li>Romantic hotel accommodation plus a private houseboat stay</li>
<li>Private airport and sightseeing transfers</li>
<li>Candlelight dinner on the houseboat and arrival-day room decoration</li>
<li>Experienced, discreet local drivers</li>
<li>Flexible itineraries customisable for your special occasions</li>
<li>24/7 support throughout your honeymoon</li>
</ul>

<h2>Popular Destinations Included</h2>

<h3>Kochi</h3>
<p>Begin with an evening walk along Fort Kochi's colonial waterfront, followed by a live Kathakali performance for two.</p>
<ul>
<li>Chinese fishing nets at sunset</li>
<li>Fort Kochi heritage walk</li>
<li>Kathakali dance performance</li>
</ul>

<h3>Munnar</h3>
<p>Wake up together to mist rolling over Munnar's tea gardens — one of Kerala's most romantic mornings.</p>
<ul>
<li>Tea plantation & tea museum tour</li>
<li>Scenic viewpoints for two</li>
</ul>

<h3>Thekkady</h3>
<p>A quiet boat safari on Periyar Lake offers a peaceful couple's morning surrounded by forest.</p>
<ul>
<li>Periyar Lake boat safari</li>
<li>Spice plantation walk</li>
</ul>

<h3>Alleppey</h3>
<p>Your private houseboat evening is the highlight — a candlelight dinner on deck as the backwaters turn gold at sunset.</p>
<ul>
<li>Private overnight houseboat cruise</li>
<li>Candlelight dinner on board</li>
<li>Sunset views over the backwaters</li>
</ul>

<h2>Best Time for a Kerala Honeymoon</h2>
<p><strong>Spring (March-May):</strong> Warm and humid — best for early starts and evening houseboat cruises.</p>
<p><strong>Monsoon (June-September):</strong> A lush, green, quieter Kerala, popular for couples' Ayurvedic spa sessions.</p>
<p><strong>Winter (October-February):</strong> The most popular honeymoon season, with cool, dry, comfortable weather throughout.</p>
<p><strong>Year-round:</strong> Kerala's backwaters are cruisable in every season, making this a flexible honeymoon choice.</p>
HTML;
    }

    private function sikkimSeoContent(): string
    {
        return <<<'HTML'
<p>Sikkim is one of India's most unspoilt Himalayan states, and a <strong>Sikkim tour package from Delhi</strong> takes you through its best sides — Gangtok's monasteries and markets, the glacial Tsomgo Lake, the high-altitude Nathula Pass, and the remote alpine valleys of Lachung and Pelling.</p>

<h2>Why Choose a Sikkim Tour Package from Delhi?</h2>
<p>Sikkim is reached via Bagdogra airport, well connected to Delhi by direct flights, followed by a scenic mountain drive to Gangtok. Because several of Sikkim's best sights sit inside protected border areas, a package with pre-arranged inner-line permits saves significant time and paperwork.</p>
<ul>
<li>Flight booking assistance from Delhi to Bagdogra</li>
<li>Hotel accommodation across Gangtok, Lachung and Pelling</li>
<li>Inner-line permits for Tsomgo Lake, Nathula Pass and North Sikkim</li>
<li>Private sightseeing with an experienced local driver</li>
<li>Flexible itineraries that can be customised on request</li>
<li>Affordable, transparently priced packages</li>
<li>24/7 customer support throughout your trip</li>
</ul>

<h2>Popular Destinations Included</h2>

<h3>Gangtok</h3>
<p>Gangtok, Sikkim's capital, blends Buddhist monasteries with a lively market street, all set on a steep Himalayan ridge.</p>
<ul>
<li>Rumtek Monastery</li>
<li>MG Marg market street</li>
</ul>

<h3>Tsomgo Lake & Nathula Pass</h3>
<p>A single permit-route day trip covers three of Sikkim's most iconic border-area sights.</p>
<ul>
<li>Tsomgo Lake</li>
<li>Baba Harbhajan Singh Memorial</li>
<li>Nathula Pass (Indo-China border)</li>
</ul>

<h3>Lachung</h3>
<p>Lachung is the gateway to the Yumthang Valley, one of the Himalayas' most beautiful high-altitude meadows.</p>
<ul>
<li>Yumthang Valley ("Valley of Flowers")</li>
<li>Zero Point (seasonal)</li>
<li>Hot springs</li>
</ul>

<h3>Pelling</h3>
<p>Pelling in West Sikkim offers some of the region's clearest, most unobstructed views of the Kanchenjunga range.</p>
<ul>
<li>Kanchenjunga viewpoints</li>
<li>Pemayangtse Monastery (optional)</li>
</ul>

<h2>Best Time to Visit Sikkim</h2>
<p><strong>Spring (March-May):</strong> Rhododendrons bloom across the hillsides and the Yumthang Valley is at its flower-filled best in April-May.</p>
<p><strong>Summer (June-August):</strong> Warmer, greener conditions, though monsoon rains can affect mountain roads.</p>
<p><strong>Monsoon (June-September):</strong> Heavier rainfall — some border-area excursions may be affected by road conditions.</p>
<p><strong>Autumn/Winter (October-December):</strong> The clearest mountain views of the year, with crisp air and excellent visibility for Kanchenjunga.</p>
HTML;
    }

    private function sikkimHoneymoonSeoContent(): string
    {
        return <<<'HTML'
<p>A <strong>Sikkim honeymoon package from Delhi</strong> is the choice for couples who want Himalayan scenery without the crowds of Shimla or Manali — Gangtok's monasteries, the glacial Tsomgo Lake and the remote valleys of Lachung and Pelling, all wrapped into one quiet, romantic trip.</p>

<h2>Why Choose a Sikkim Honeymoon Package from Delhi?</h2>
<p>Reached via Bagdogra airport with easy connections from Delhi, Sikkim offers a genuinely offbeat Himalayan honeymoon. This package pre-arranges the inner-line permits required for the border-area sights, and layers on romantic touches — a candlelight dinner, room decoration and more — so the paperwork stays out of your way.</p>
<ul>
<li>Flight booking assistance from Delhi to Bagdogra</li>
<li>Romantic hotel accommodation across Gangtok, Lachung and Pelling</li>
<li>Inner-line permits for Tsomgo Lake, Nathula Pass and North Sikkim</li>
<li>Candlelight dinner and arrival-day room decoration</li>
<li>Experienced, discreet local drivers</li>
<li>Flexible itineraries customisable for your special occasions</li>
<li>24/7 support throughout your honeymoon</li>
</ul>

<h2>Popular Destinations Included</h2>

<h3>Gangtok</h3>
<p>Start your honeymoon with a visit to Rumtek Monastery before an evening stroll along MG Marg together.</p>
<ul>
<li>Rumtek Monastery</li>
<li>MG Marg evening walk</li>
</ul>

<h3>Tsomgo Lake & Nathula Pass</h3>
<p>A shared day trip along Sikkim's dramatic high-altitude border route, with views few honeymooners get to see.</p>
<ul>
<li>Tsomgo Lake</li>
<li>Baba Harbhajan Singh Memorial</li>
<li>Nathula Pass</li>
</ul>

<h3>Lachung</h3>
<p>Lachung's remote alpine setting is ideal for couples seeking a quiet, offbeat honeymoon base.</p>
<ul>
<li>Yumthang Valley</li>
<li>Zero Point (seasonal)</li>
<li>Hot springs for two</li>
</ul>

<h3>Pelling</h3>
<p>End your honeymoon with unobstructed Kanchenjunga views from Pelling, one of Sikkim's most romantic outlooks.</p>
<ul>
<li>Kanchenjunga viewpoints</li>
<li>Pemayangtse Monastery (optional)</li>
</ul>

<h2>Best Time for a Sikkim Honeymoon</h2>
<p><strong>Spring (March-May):</strong> Rhododendron blooms and the Yumthang Valley "Valley of Flowers" at its peak in April-May.</p>
<p><strong>Summer (June-August):</strong> Warmer and greener, though some border roads may be affected by rain.</p>
<p><strong>Monsoon (June-September):</strong> A quieter, mist-covered Sikkim for couples who enjoy a more atmospheric mountain honeymoon.</p>
<p><strong>Autumn/Winter (October-December):</strong> The clearest skies of the year, perfect for Kanchenjunga views together.</p>
HTML;
    }

    private function andamanSeoContent(): string
    {
        return <<<'HTML'
<p>The Andaman Islands are India's best beach and diving destination, and an <strong>Andaman tour package from Delhi</strong> covers the full island circuit — Port Blair's colonial history, Havelock's world-class Radhanagar Beach, and the laid-back shores of Neil Island.</p>

<h2>Why Choose an Andaman Tour Package from Delhi?</h2>
<p>Direct flights connect Delhi to Port Blair in around 5 hours, with connecting options via Chennai or Kolkata. Because getting between islands requires ferry bookings timed around each hop, a pre-planned package removes the single biggest logistical headache of an Andaman trip.</p>
<ul>
<li>Flight booking assistance from Delhi to Port Blair</li>
<li>Hotel accommodation across Port Blair, Havelock and Neil Island</li>
<li>Inter-island ferry tickets included</li>
<li>Private island transfers and sightseeing</li>
<li>Flexible itineraries that can be customised on request</li>
<li>Affordable, transparently priced packages</li>
<li>24/7 customer support throughout your trip</li>
</ul>

<h2>Popular Destinations Included</h2>

<h3>Port Blair</h3>
<p>Port Blair's Cellular Jail is the historical heart of the islands, a former colonial prison now a moving memorial.</p>
<ul>
<li>Cellular Jail Light & Sound Show</li>
<li>Ross Island boat tour</li>
<li>North Bay Island coral viewing</li>
</ul>

<h3>Havelock (Swaraj Dweep)</h3>
<p>Havelock is home to Radhanagar Beach, repeatedly ranked among Asia's best, and some of the Andamans' richest coral reefs.</p>
<ul>
<li>Radhanagar Beach</li>
<li>Scuba diving & snorkelling</li>
<li>Elephant Beach (optional)</li>
</ul>

<h3>Neil Island (Shaheed Dweep)</h3>
<p>Neil Island offers a quieter, slower-paced beach experience away from Havelock's crowds.</p>
<ul>
<li>Bharatpur Beach</li>
<li>Laxmanpur Beach sunset point</li>
</ul>

<h2>Best Time to Visit Andaman</h2>
<p><strong>Winter (October-February):</strong> The most popular season — calm seas, clear skies and excellent visibility for diving.</p>
<p><strong>Spring/Summer (March-May):</strong> Warm, sunny days, still good for beaches and water sports before the monsoon.</p>
<p><strong>Monsoon (June-September):</strong> Rougher seas and occasional ferry disruptions — the quietest, most discounted season.</p>
<p><strong>Year-round:</strong> Underwater visibility is generally best from December to April for scuba diving enthusiasts.</p>
HTML;
    }

    private function andamanHoneymoonSeoContent(): string
    {
        return <<<'HTML'
<p>An <strong>Andaman honeymoon package from Delhi</strong> is India's answer to a tropical island honeymoon — Havelock's Radhanagar Beach, repeatedly ranked among Asia's finest, plus the quieter shores of Neil Island, all without needing a passport.</p>

<h2>Why Choose an Andaman Honeymoon Package from Delhi?</h2>
<p>With direct flights from Delhi to Port Blair taking around 5 hours, the Andaman Islands are a surprisingly accessible honeymoon destination for a genuinely tropical, beach-first experience. This package handles every ferry connection and beachfront stay, with romantic extras built in from day one.</p>
<ul>
<li>Flight booking assistance from Delhi to Port Blair</li>
<li>Romantic beachfront hotel accommodation</li>
<li>Inter-island ferry tickets included</li>
<li>Candlelight beachside dinner and arrival-day room decoration</li>
<li>Private island transfers and sightseeing</li>
<li>Flexible itineraries customisable for your special occasions</li>
<li>24/7 support throughout your honeymoon</li>
</ul>

<h2>Popular Destinations Included</h2>

<h3>Port Blair</h3>
<p>An evening at the Cellular Jail sets a reflective start to your honeymoon before the beach days begin.</p>
<ul>
<li>Cellular Jail Light & Sound Show</li>
<li>Ross & North Bay Island tour</li>
</ul>

<h3>Havelock (Swaraj Dweep)</h3>
<p>Havelock's Radhanagar Beach is the honeymoon centrepiece — powder-white sand and turquoise water made for long, unhurried walks together.</p>
<ul>
<li>Radhanagar Beach</li>
<li>Couple's scuba diving or snorkelling session (optional)</li>
</ul>

<h3>Neil Island (Shaheed Dweep)</h3>
<p>End your honeymoon on Neil Island's quieter beaches, ideal for a private sunset moment.</p>
<ul>
<li>Bharatpur Beach</li>
<li>Laxmanpur Beach sunset point</li>
</ul>

<h2>Best Time for an Andaman Honeymoon</h2>
<p><strong>Winter (October-February):</strong> The most popular honeymoon season — calm seas, clear skies and festive New Year energy on the islands.</p>
<p><strong>Spring/Summer (March-May):</strong> Warm, sunny beach days, slightly less crowded than peak winter.</p>
<p><strong>Monsoon (June-September):</strong> Quieter and more affordable, though ferry schedules can be affected by rough seas.</p>
<p><strong>Year-round:</strong> The islands' tropical climate means a beach honeymoon works in almost any season with the right planning.</p>
HTML;
    }

    private function vietnamSeoContent(): string
    {
        return <<<'HTML'
<p>Vietnam is one of Southeast Asia's most rewarding international trips, and a <strong>Vietnam tour package from India</strong> covers its highlight reel in one 6-day itinerary — Hanoi's Old Quarter, an overnight cruise through the limestone karsts of Ha Long Bay, and the beaches and heritage streets of Da Nang and Hoi An.</p>

<h2>Why Choose a Vietnam Tour Package from India?</h2>
<p>Direct and one-stop flights connect major Indian cities to Hanoi and Da Nang in around 5-7 hours, and Indian nationals can apply for a Vietnam e-visa online without much hassle. A pre-planned package bundles the Hanoi-Ha Long-Da Nang logistics, including a domestic flight, into one smooth trip.</p>
<ul>
<li>Flight booking assistance from India to Vietnam</li>
<li>Hotel accommodation plus an overnight Ha Long Bay cruise cabin</li>
<li>Hanoi-Da Nang domestic flight included</li>
<li>Private sightseeing with an experienced local guide</li>
<li>Flexible itineraries that can be customised on request</li>
<li>Affordable, transparently priced packages</li>
<li>24/7 customer support throughout your trip</li>
</ul>

<h2>Popular Destinations Included</h2>

<h3>Hanoi</h3>
<p>Vietnam's capital is built around its historic 36-street Old Quarter and the calm waters of Hoan Kiem Lake.</p>
<ul>
<li>Old Quarter walking tour</li>
<li>Hoan Kiem Lake</li>
<li>Night market</li>
</ul>

<h3>Ha Long Bay</h3>
<p>A UNESCO World Heritage bay dotted with thousands of limestone karsts, best experienced on an overnight cruise.</p>
<ul>
<li>Overnight cruise with cabin stay</li>
<li>Kayaking</li>
<li>Limestone cave visit</li>
</ul>

<h3>Da Nang</h3>
<p>Da Nang's beaches and the Ba Na Hills cable car make it central Vietnam's most popular resort base.</p>
<ul>
<li>Ba Na Hills Golden Bridge</li>
<li>French Village & gardens</li>
</ul>

<h3>Hoi An</h3>
<p>Hoi An's lantern-lit Ancient Town is one of Vietnam's most photogenic UNESCO heritage sites.</p>
<ul>
<li>Ancient Town walking tour</li>
<li>Japanese Covered Bridge</li>
<li>Thu Bon riverfront & tailor shops</li>
</ul>

<h2>Best Time to Visit Vietnam</h2>
<p><strong>Winter/Spring (October-April):</strong> The most pleasant season across Hanoi, Ha Long Bay and Da Nang — dry, mild and ideal for sightseeing and the cruise.</p>
<p><strong>Summer (May-August):</strong> Hot and humid in the north, though Da Nang's beaches remain popular.</p>
<p><strong>Monsoon (June-September):</strong> Central Vietnam can see heavier rain and occasional typhoons — worth checking forecasts before travel.</p>
<p><strong>Autumn (September-November):</strong> Comfortable temperatures and fewer crowds across the whole circuit.</p>
HTML;
    }

    private function malaysiaSeoContent(): string
    {
        return <<<'HTML'
<p>Malaysia offers one of Southeast Asia's most compact multi-city holidays, and a <strong>Malaysia tour package from India</strong> covers it all in 5 days — Kuala Lumpur's Petronas Towers skyline, the cool casino resorts of Genting Highlands, and the beach island of Langkawi.</p>

<h2>Why Choose a Malaysia Tour Package from India?</h2>
<p>Direct flights from major Indian cities to Kuala Lumpur take around 4.5-5.5 hours, and Indian nationals can apply for a Malaysia eVISA or eNTRI online with minimal paperwork. A ready-made package bundles the Kuala Lumpur-Langkawi domestic flight and every transfer into one seamless trip.</p>
<ul>
<li>Flight booking assistance from India to Malaysia</li>
<li>Hotel accommodation in Kuala Lumpur and Langkawi</li>
<li>Kuala Lumpur-Langkawi domestic flight included</li>
<li>Private sightseeing with an experienced local driver</li>
<li>Flexible itineraries that can be customised on request</li>
<li>Affordable, transparently priced packages</li>
<li>24/7 customer support throughout your trip</li>
</ul>

<h2>Popular Destinations Included</h2>

<h3>Kuala Lumpur</h3>
<p>Malaysia's capital is dominated by the iconic Petronas Twin Towers, one of the world's most recognisable skylines.</p>
<ul>
<li>Petronas Towers Skybridge & Observation Deck</li>
<li>Batu Caves</li>
</ul>

<h3>Genting Highlands</h3>
<p>A hilltop resort town reached by one of the world's fastest cable cars, offering cooler mountain weather year-round.</p>
<ul>
<li>Genting SkyWay cable car</li>
<li>Theme park & casino resort</li>
</ul>

<h3>Langkawi</h3>
<p>Langkawi's beaches and rainforest-clad hills make it Malaysia's most popular island getaway.</p>
<ul>
<li>Langkawi Sky Bridge</li>
<li>Cable car ride up Gunung Mat Cincang</li>
<li>Island-hopping boat tours (optional)</li>
</ul>

<h2>Best Time to Visit Malaysia</h2>
<p><strong>December-February:</strong> Generally drier across Kuala Lumpur and Langkawi, though brief showers are possible year-round in this tropical climate.</p>
<p><strong>March-May:</strong> Warm and increasingly humid ahead of the mid-year rains.</p>
<p><strong>June-August:</strong> A second drier stretch, popular for island time in Langkawi.</p>
<p><strong>September-November:</strong> The wettest months, with short, heavy tropical showers most afternoons.</p>
HTML;
    }

    private function dubaiSeoContent(): string
    {
        return <<<'HTML'
<p>Dubai is the UAE's glittering showcase city, and a <strong>Dubai tour package from India</strong> is one of the fastest, most convenient international holidays available — the Burj Khalifa, a desert safari under the stars, and a day trip to Abu Dhabi, all in 5 days.</p>

<h2>Why Choose a Dubai Tour Package from India?</h2>
<p>Direct flights from most major Indian cities reach Dubai in just 3-4 hours, and a UAE visa is straightforward to arrange for eligible Indian passport holders. A pre-planned package bundles the Burj Khalifa, desert safari and Abu Dhabi day trip into one smooth, no-guesswork itinerary.</p>
<ul>
<li>Flight booking assistance from India to Dubai</li>
<li>Hotel accommodation in Dubai</li>
<li>Airport transfers in a private AC vehicle</li>
<li>Private sightseeing with an experienced local driver</li>
<li>Flexible itineraries that can be customised on request</li>
<li>Affordable, transparently priced packages</li>
<li>24/7 customer support throughout your trip</li>
</ul>

<h2>Popular Destinations Included</h2>

<h3>Downtown Dubai</h3>
<p>Home to the Burj Khalifa, the world's tallest building, and the Dubai Fountain show at its base.</p>
<ul>
<li>Burj Khalifa "At The Top" observation deck</li>
<li>Dubai Fountain show</li>
</ul>

<h3>Dubai Desert</h3>
<p>A classic Dubai evening — dune bashing, camel rides and a BBQ dinner under a desert sky.</p>
<ul>
<li>4x4 dune bashing</li>
<li>Camel riding & sandboarding</li>
<li>BBQ dinner with live entertainment</li>
</ul>

<h3>Dubai City</h3>
<p>Beyond the skyline, Dubai's newer attractions make for a full day of sightseeing.</p>
<ul>
<li>Dubai Frame</li>
<li>Dubai Miracle Garden</li>
<li>Dhow Cruise Dinner on Dubai Marina</li>
</ul>

<h3>Abu Dhabi</h3>
<p>A day trip to the UAE capital centres on one of the world's most striking mosques.</p>
<ul>
<li>Sheikh Zayed Grand Mosque</li>
<li>Emirates Palace</li>
<li>Corniche waterfront</li>
</ul>

<h2>Best Time to Visit Dubai</h2>
<p><strong>Winter (November-February):</strong> The most comfortable season, with cool evenings ideal for the desert safari.</p>
<p><strong>Spring (March-April):</strong> Warm but still manageable daytime temperatures for sightseeing.</p>
<p><strong>Summer (May-September):</strong> Very hot (often 40°C+) — most activities shift indoors or to the evening.</p>
<p><strong>Autumn (October):</strong> Temperatures begin cooling down, marking the start of peak tourist season.</p>
HTML;
    }

    private function singaporeSeoContent(): string
    {
        return <<<'HTML'
<p>Singapore is Asia's most polished city-break destination, and a <strong>Singapore tour package from India</strong> covers everything that makes it special in 5 days — Universal Studios, the futuristic Gardens by the Bay, Sentosa Island, and an evening Night Safari.</p>

<h2>Why Choose a Singapore Tour Package from India?</h2>
<p>Direct flights from major Indian cities reach Singapore in around 5-6 hours, and it's consistently ranked one of the safest, most family-friendly cities in the world. A pre-planned package bundles theme park tickets, city sightseeing and the Night Safari into one seamless, stress-free trip.</p>
<ul>
<li>Flight booking assistance from India to Singapore</li>
<li>Hotel accommodation in Singapore</li>
<li>Airport transfers in a private AC vehicle</li>
<li>Universal Studios Singapore full-day ticket</li>
<li>Flexible itineraries that can be customised on request</li>
<li>Affordable, transparently priced packages</li>
<li>24/7 customer support throughout your trip</li>
</ul>

<h2>Popular Destinations Included</h2>

<h3>Marina Bay</h3>
<p>The futuristic heart of modern Singapore, anchored by the Supertrees of Gardens by the Bay.</p>
<ul>
<li>Gardens by the Bay & OCBC Skyway</li>
<li>Supertree light show</li>
<li>Singapore Flyer</li>
</ul>

<h3>Sentosa Island</h3>
<p>Singapore's resort island is home to its biggest family attraction, reached by a scenic cable car.</p>
<ul>
<li>Universal Studios Singapore</li>
<li>Sentosa Cable Car from Mount Faber</li>
<li>Sentosa beaches</li>
</ul>

<h3>Singapore City</h3>
<p>A classic city tour covering Singapore's most photographed landmark and its famous shopping street.</p>
<ul>
<li>Merlion Park</li>
<li>Orchard Road</li>
</ul>

<h3>Singapore Zoo</h3>
<p>The world's first nocturnal wildlife park makes for a uniquely memorable evening.</p>
<ul>
<li>Night Safari tram/walking tour</li>
<li>100+ species active after dark</li>
</ul>

<h2>Best Time to Visit Singapore</h2>
<p><strong>February-April:</strong> Slightly drier stretch of the year, though Singapore's equatorial climate means showers are possible any month.</p>
<p><strong>May-July:</strong> Warm and humid, with the Great Singapore Sale often running in this window.</p>
<p><strong>August-October:</strong> Occasional afternoon thunderstorms, but still a popular time to visit.</p>
<p><strong>November-January:</strong> The wettest months, with festive Christmas and New Year light displays along Orchard Road.</p>
HTML;
    }
}
