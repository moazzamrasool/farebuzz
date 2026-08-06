<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Support\SiteTenant;
use Database\Seeders\Concerns\GeneratesDemoImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DestinationSeeder extends Seeder
{
    use GeneratesDemoImages;

    public function run(): void
    {
        $destinations = [
            ['name' => 'Goa',        'local' => 'domestic',      'country' => 'India',      'city' => 'Goa',        'color' => '#0d6efd'],
            ['name' => 'Maldives',   'local' => 'international', 'country' => 'Maldives',   'city' => 'Male',       'color' => '#0ea5e9'],
            ['name' => 'Manali',     'local' => 'domestic',      'country' => 'India',      'city' => 'Manali',     'color' => '#6c757d'],
            ['name' => 'Switzerland','local' => 'international', 'country' => 'Switzerland','city' => 'Interlaken', 'color' => '#16a34a'],
            ['name' => 'Bali',       'local' => 'international', 'country' => 'Indonesia',  'city' => 'Bali',       'color' => '#f47b20'],
            // Indian metro hubs — not vacation spots, but needed so the hero
            // search's "From City" autocomplete (which reuses this same master
            // data, see Phase 2 plan) has real departure cities to suggest.
            ['name' => 'Delhi',      'local' => 'domestic',      'country' => 'India',      'city' => 'Delhi',      'color' => '#dc2626'],
            ['name' => 'Mumbai',     'local' => 'domestic',      'country' => 'India',      'city' => 'Mumbai',     'color' => '#2563eb'],
            ['name' => 'Bengaluru',  'local' => 'domestic',      'country' => 'India',      'city' => 'Bengaluru',  'color' => '#059669'],
            ['name' => 'Chennai',    'local' => 'domestic',      'country' => 'India',      'city' => 'Chennai',    'color' => '#7c3aed'],
            // Homepage content (Trending Destinations, Explore India, Trip Planner,
            // Weekend Deals, Top Unique Properties) links to these — see
            // HomepageDestinationsSeeder and HomepageContentSeeder.
            ['name' => 'Singapore',      'local' => 'international', 'country' => 'Singapore',   'city' => 'Singapore',      'color' => '#e11d48'],
            ['name' => 'London',        'local' => 'international', 'country' => 'United Kingdom', 'city' => 'London',     'color' => '#334155'],
            ['name' => 'Bangkok',       'local' => 'international', 'country' => 'Thailand',    'city' => 'Bangkok',        'color' => '#ea580c'],
            ['name' => 'Sri Lanka',     'local' => 'international', 'country' => 'Sri Lanka',   'city' => 'Sigiriya',       'color' => '#0d9488'],
            ['name' => 'Agra',          'local' => 'domestic',      'country' => 'India',       'city' => 'Agra',           'color' => '#b45309'],
            ['name' => 'Jaipur',        'local' => 'domestic',      'country' => 'India',       'city' => 'Jaipur',         'color' => '#be123c'],
            ['name' => 'Lucknow',       'local' => 'domestic',      'country' => 'India',       'city' => 'Lucknow',        'color' => '#4d7c0f'],
            ['name' => 'Bhopal',        'local' => 'domestic',      'country' => 'India',       'city' => 'Bhopal',         'color' => '#0369a1'],
            ['name' => 'Varanasi',      'local' => 'domestic',      'country' => 'India',       'city' => 'Varanasi',       'color' => '#a16207'],
            ['name' => 'Hyderabad',     'local' => 'domestic',      'country' => 'India',       'city' => 'Hyderabad',      'color' => '#7c2d12'],
            ['name' => 'Kolkata',       'local' => 'domestic',      'country' => 'India',       'city' => 'Kolkata',        'color' => '#1d4ed8'],
            ['name' => 'Ayodhya',       'local' => 'domestic',      'country' => 'India',       'city' => 'Ayodhya',        'color' => '#c2410c'],
            ['name' => 'Gurgaon',       'local' => 'domestic',      'country' => 'India',       'city' => 'Gurgaon',        'color' => '#0891b2'],
            ['name' => 'Madurai',       'local' => 'domestic',      'country' => 'India',       'city' => 'Madurai',        'color' => '#9333ea'],
            ['name' => 'Mahabalipuram', 'local' => 'domestic',      'country' => 'India',       'city' => 'Mahabalipuram',  'color' => '#0e7490'],
            ['name' => 'Ranthambore',   'local' => 'domestic',      'country' => 'India',       'city' => 'Sawai Madhopur', 'color' => '#65a30d'],
            ['name' => 'Kaziranga',     'local' => 'domestic',      'country' => 'India',       'city' => 'Kaziranga',      'color' => '#166534'],
        ];

        foreach ($destinations as $index => $dest) {
            $destination = Destination::firstOrCreate(
                ['slug' => Str::slug($dest['name'])],
                [
                    'unique_id'   => SiteTenant::id(),
                    'name'        => $dest['name'],
                    'local'       => $dest['local'],
                    'country'     => $dest['country'],
                    'city'        => $dest['city'],
                    'description' => "Discover the best of {$dest['name']}.",
                    'status'      => 'active',
                    'sort_order'  => $index,
                    'featured'    => $index < 2,
                ]
            );

            if (!$destination->cover_image) {
                $destination->update(['cover_image' => $this->destinationCoverImage($dest['name'], $dest['color'])]);
            }

            if (empty($destination->gallery_images)) {
                $destination->update(['gallery_images' => $this->destinationGalleryImages($dest['name'], $dest['color'])]);
            }
        }

        $this->realCatalogueDestinations();
    }

    // The 9 real destinations backing the 14-package SEO catalogue (HolidayPackageSeeder).
    // Unlike the demo rows above, these are updateOrCreate()'d — "Singapore" already exists
    // as a plain homepage-link row from the demo list, and needs its real SEO content and
    // images regardless — and images are always resynced to the real photo when a Commons
    // download succeeds, matching HotelSeeder's "always resync to the real photo" precedent.
    private function realCatalogueDestinations(): void
    {
        foreach ($this->realDestinationData() as $index => $dest) {
            $destination = Destination::updateOrCreate(
                ['slug' => $dest['slug']],
                [
                    'unique_id'         => SiteTenant::id(),
                    'name'              => $dest['name'],
                    'local'             => $dest['local'],
                    'country'           => $dest['country'],
                    'city'              => $dest['city'],
                    'description'       => $dest['description'],
                    'meta_title'        => $dest['meta_title'],
                    'meta_description'  => $dest['meta_description'],
                    'meta_keywords'     => $dest['meta_keywords'],
                    'focus_keyword'     => $dest['focus_keyword'],
                    'seo_content'       => $dest['seo_content'],
                    'status'            => 'active',
                    'sort_order'        => 100 + $index,
                    'featured'          => true,
                ]
            );

            $coverPath = $this->downloadFromCommons($dest['cover_commons'], 'destinations')
                ?? ($dest['cover_override'] ?? null)
                ?? $this->placeholderImage(1200, 600, $dest['name'], 'destinations', $dest['color']);
            $destination->update(['cover_image' => $coverPath]);

            $galleryPaths = [];
            foreach ($dest['gallery_commons'] as $i => $title) {
                $galleryPaths[] = $this->downloadFromCommons($title, 'destinations/gallery')
                    ?? $this->placeholderImage(800, 600, $dest['name'].' '.($i + 1), 'destinations/gallery', $dest['color']);
            }
            $destination->update(['gallery_images' => $galleryPaths]);
        }
    }

    private function realDestinationData(): array
    {
        return [
            [
                'name' => 'Kashmir', 'slug' => 'kashmir', 'local' => 'domestic', 'country' => 'India', 'city' => 'Srinagar', 'color' => '#0369a1',
                'description' => 'Known as "Paradise on Earth", Kashmir pairs the shimmering Dal Lake and its houseboats with the snow-clad slopes of Gulmarg, the meadows of Pahalgam and the glaciers of Sonmarg.',
                'meta_title' => 'Kashmir Tourism | Srinagar, Gulmarg, Pahalgam & Sonmarg | FareBuzzer Travel',
                'meta_description' => 'Plan your Kashmir trip with FareBuzzer Travel — Dal Lake shikara rides, Gulmarg\'s gondola, Pahalgam\'s valleys and Sonmarg\'s glaciers, with curated tour and honeymoon packages from Delhi.',
                'meta_keywords' => 'kashmir tourism, kashmir tour package, srinagar gulmarg pahalgam, kashmir honeymoon package',
                'focus_keyword' => 'Kashmir tour package from Delhi',
                'seo_content' => "<p>Kashmir is one of India's most sought-after Himalayan destinations, famous for the houseboats and shikaras of Dal Lake, the ski slopes of Gulmarg, the green meadows of Pahalgam and the glaciers of Sonmarg. Every season paints the valley differently — spring blossoms, summer meadows, autumn chinars and winter snow — which is why Kashmir stays on travellers' bucket lists all year round.</p><h2>Why Visit Kashmir</h2><p>Srinagar's Dal Lake, Gulmarg's Gondola cable car, Pahalgam's Betaab Valley and Sonmarg's Thajiwas Glacier together make Kashmir one of the most complete mountain holidays in India, easily reached on a short flight from Delhi.</p>",
                'cover_commons' => 'Dal Lake Srinagar.jpg',
                'gallery_commons' => ['Shalimar Bagh, Srinagar.jpg', 'Betaab Valley.jpg'],
            ],
            [
                'name' => 'Himachal Pradesh', 'slug' => 'himachal-pradesh', 'local' => 'domestic', 'country' => 'India', 'city' => 'Shimla', 'color' => '#6c757d',
                'description' => 'Himachal Pradesh brings together the colonial charm of Shimla\'s Mall Road, the snow adventures of Solang Valley near Manali, and the mountain views of Kufri — a classic North Indian hill-station getaway.',
                'meta_title' => 'Himachal Pradesh Tourism | Shimla & Manali Tour Packages | FareBuzzer Travel',
                'meta_description' => 'Explore Himachal Pradesh with FareBuzzer Travel — Shimla\'s Mall Road, Manali\'s Solang Valley, Atal Tunnel and Hadimba Temple, with tour and honeymoon packages from Delhi.',
                'meta_keywords' => 'himachal pradesh tourism, shimla manali tour package, himachal honeymoon package',
                'focus_keyword' => 'Himachal tour package from Delhi',
                'seo_content' => "<p>Himachal Pradesh is the classic North Indian hill-station holiday — Shimla's colonial-era Mall Road and Ridge, and Manali's adventure hub around Solang Valley and the Atal Tunnel, all set against snow-capped Himalayan peaks.</p><h2>Why Visit Himachal Pradesh</h2><p>A short overnight journey or a quick flight from Delhi to Shimla or Kullu-Manali airport puts you within reach of pine forests, apple orchards, and activities ranging from paragliding to snow scooters.</p>",
                'cover_commons' => 'Manali.jpg',
                'gallery_commons' => ['Shimla.jpg', 'Rohtang Pass.jpg'],
            ],
            [
                'name' => 'Andaman & Nicobar Islands', 'slug' => 'andaman', 'local' => 'domestic', 'country' => 'India', 'city' => 'Port Blair', 'color' => '#0e7490',
                'description' => 'The Andaman Islands offer India\'s finest beaches and coral reefs — Port Blair\'s colonial Cellular Jail, Havelock\'s Radhanagar Beach and Neil Island\'s laid-back shores, all wrapped in turquoise water.',
                'meta_title' => 'Andaman Tourism | Port Blair, Havelock & Neil Island | FareBuzzer Travel',
                'meta_description' => 'Discover the Andaman Islands with FareBuzzer Travel — Cellular Jail, Radhanagar Beach, Havelock scuba diving and Neil Island, with tour and honeymoon packages from Delhi.',
                'meta_keywords' => 'andaman tourism, andaman tour package, havelock neil island, andaman honeymoon package',
                'focus_keyword' => 'Andaman tour package from Delhi',
                'seo_content' => "<p>The Andaman Islands are India's best beach and diving destination, home to the historic Cellular Jail in Port Blair, the powder-white Radhanagar Beach on Havelock (Swaraj Dweep) and the quiet shores of Neil Island (Shaheed Dweep).</p><h2>Why Visit the Andaman Islands</h2><p>Connected by direct flights from Delhi via Chennai or Kolkata, the islands combine colonial history, coral-reef snorkelling and scuba diving, and some of the cleanest beaches in India.</p>",
                'cover_commons' => 'Radhanagar Beach.jpg',
                'gallery_commons' => ['Cellular Jail.jpg', 'Havelock Island.jpg'],
            ],
            [
                'name' => 'Kerala', 'slug' => 'kerala', 'local' => 'domestic', 'country' => 'India', 'city' => 'Kochi', 'color' => '#16a34a',
                'description' => 'God\'s Own Country blends Kochi\'s colonial waterfront, Munnar\'s rolling tea plantations, Thekkady\'s wildlife-rich forests and the palm-fringed backwaters of Alleppey.',
                'meta_title' => 'Kerala Tourism | Kochi, Munnar, Thekkady & Alleppey | FareBuzzer Travel',
                'meta_description' => 'Explore Kerala with FareBuzzer Travel — Fort Kochi, Munnar\'s tea gardens, Periyar wildlife safari and an Alleppey houseboat stay, with tour and honeymoon packages from Delhi.',
                'meta_keywords' => 'kerala tourism, kerala tour package, munnar thekkady alleppey, kerala honeymoon package',
                'focus_keyword' => 'Kerala tour package from Delhi',
                'seo_content' => "<p>Kerala, God's Own Country, pairs Fort Kochi's colonial charm with Munnar's emerald tea plantations, Thekkady's Periyar wildlife sanctuary and an unforgettable houseboat cruise through the Alleppey backwaters.</p><h2>Why Visit Kerala</h2><p>Direct flights from Delhi to Kochi make this lush, green corner of South India an easy escape, blending nature, wildlife and a slow-paced backwater holiday in one trip.</p>",
                'cover_commons' => 'Alleppey.jpg',
                'gallery_commons' => ['Fort Kochi.jpg', 'Munnar tea plantations.jpg'],
            ],
            [
                'name' => 'Sikkim', 'slug' => 'sikkim', 'local' => 'domestic', 'country' => 'India', 'city' => 'Gangtok', 'color' => '#166534',
                'description' => 'Tucked in the Eastern Himalayas, Sikkim mixes Gangtok\'s monasteries and markets with the turquoise Tsomgo Lake, the remote beauty of Pelling and the alpine valley of Lachung.',
                'meta_title' => 'Sikkim Tourism | Gangtok, Pelling & Lachung | FareBuzzer Travel',
                'meta_description' => 'Discover Sikkim with FareBuzzer Travel — Tsomgo Lake, Baba Mandir, Nathula Pass and Rumtek Monastery, with tour and honeymoon packages from Delhi.',
                'meta_keywords' => 'sikkim tourism, sikkim tour package, gangtok pelling lachung, sikkim honeymoon package',
                'focus_keyword' => 'Sikkim tour package from Delhi',
                'seo_content' => "<p>Sikkim is an unspoilt Eastern Himalayan state where Gangtok's monasteries and markets open onto the turquoise Tsomgo Lake, the sacred Baba Mandir, the high-altitude Nathula Pass, and the pine-forested valleys around Pelling and Lachung.</p><h2>Why Visit Sikkim</h2><p>Reached via Bagdogra airport with easy connections from Delhi, Sikkim offers pristine mountain scenery, Buddhist culture and far fewer crowds than better-known Himalayan hill stations.</p>",
                'cover_commons' => 'Gangtok.jpg',
                'gallery_commons' => ['Tsomgo Lake.jpg', 'Rumtek Monastery.jpg'],
            ],
            [
                'name' => 'Vietnam', 'slug' => 'vietnam', 'local' => 'international', 'country' => 'Vietnam', 'city' => 'Hanoi', 'color' => '#b91c1c',
                'description' => 'Vietnam pairs Hanoi\'s Old Quarter with the limestone karsts of Ha Long Bay and the lantern-lit heritage town of Hoi An near Da Nang.',
                'meta_title' => 'Vietnam Tourism | Hanoi, Ha Long Bay & Hoi An | FareBuzzer Travel',
                'meta_description' => 'Book your Vietnam trip with FareBuzzer Travel — a Ha Long Bay cruise, Hanoi\'s Old Quarter, Ba Na Hills Golden Bridge and Hoi An, with tour packages from India.',
                'meta_keywords' => 'vietnam tourism, vietnam tour package, ha long bay hoi an, vietnam package from india',
                'focus_keyword' => 'Vietnam tour package from India',
                'seo_content' => "<p>Vietnam is one of Southeast Asia's most rewarding trips, combining Hanoi's Old Quarter, an overnight cruise through the limestone karsts of Ha Long Bay, and the lantern-lit ancient town of Hoi An near Da Nang.</p><h2>Why Visit Vietnam</h2><p>Direct and one-stop flights from major Indian cities make Vietnam an easy international getaway, blending UNESCO heritage sites, dramatic seascapes and some of Asia's best street food.</p>",
                'cover_commons' => 'Ha Long Bay.jpg',
                'gallery_commons' => ['Hanoi.jpg', 'Hoi An.jpg'],
            ],
            [
                'name' => 'Malaysia', 'slug' => 'malaysia', 'local' => 'international', 'country' => 'Malaysia', 'city' => 'Kuala Lumpur', 'color' => '#7c3aed',
                'description' => 'Malaysia mixes Kuala Lumpur\'s Petronas Towers skyline with the cool hilltop resorts of Genting Highlands and the island beaches of Langkawi.',
                'meta_title' => 'Malaysia Tourism | Kuala Lumpur, Genting & Langkawi | FareBuzzer Travel',
                'meta_description' => 'Plan your Malaysia holiday with FareBuzzer Travel — Petronas Towers, Batu Caves, Genting Highlands and Langkawi Sky Bridge, with tour packages from India.',
                'meta_keywords' => 'malaysia tourism, malaysia tour package, kuala lumpur genting langkawi, malaysia package from india',
                'focus_keyword' => 'Malaysia tour package from India',
                'seo_content' => "<p>Malaysia offers a compact multi-city holiday — Kuala Lumpur's iconic Petronas Towers and the nearby Hindu shrine of Batu Caves, the cool casino resorts of Genting Highlands, and the beach island of Langkawi with its cable car and Sky Bridge.</p><h2>Why Visit Malaysia</h2><p>With frequent flights from India and visa-friendly entry, Malaysia is one of the easiest Southeast Asian family holidays, mixing city skylines, theme parks and island beaches in one trip.</p>",
                'cover_commons' => 'Petronas Towers.jpg',
                'gallery_commons' => ['Batu Caves.jpg', 'Langkawi Sky Bridge.jpg'],
            ],
            [
                'name' => 'Dubai', 'slug' => 'dubai', 'local' => 'international', 'country' => 'United Arab Emirates', 'city' => 'Dubai', 'color' => '#b45309',
                'description' => 'Dubai delivers desert safaris and the world\'s tallest building, the Burj Khalifa, alongside Abu Dhabi\'s grand mosque and Dubai\'s dhow cruises and theme gardens.',
                'meta_title' => 'Dubai Tourism | Burj Khalifa, Desert Safari & Abu Dhabi | FareBuzzer Travel',
                'meta_description' => 'Book your Dubai holiday with FareBuzzer Travel — Burj Khalifa, desert safari with BBQ dinner, dhow cruise and Dubai Frame, with tour packages from India.',
                'meta_keywords' => 'dubai tourism, dubai tour package, burj khalifa desert safari, dubai package from india',
                'focus_keyword' => 'Dubai tour package from India',
                'seo_content' => "<p>Dubai is the UAE's glittering showcase city — the Burj Khalifa's observation decks, a desert safari with dune bashing and a BBQ dinner under the stars, a traditional dhow cruise dinner, and family attractions like the Dubai Frame and Miracle Garden.</p><h2>Why Visit Dubai</h2><p>Just a few hours' flight from most Indian cities, Dubai is one of the most convenient international holidays — ultramodern architecture, desert adventure and easy day trips to Abu Dhabi, all in one short trip.</p>",
                'cover_commons' => 'Burj Khalifa.jpg',
                // Commons redirect for "Burj Khalifa.jpg" doesn't reliably resolve at seed
                // time (falls through to a text placeholder) — known-good direct URL as backup.
                'cover_override' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/46/Dubai_Skyline_mit_Burj_Khalifa_%28cropped%29.jpg/1280px-Dubai_Skyline_mit_Burj_Khalifa_%28cropped%29.jpg',
                'gallery_commons' => ['Dubai Frame.jpg', 'Dubai Marina.jpg'],
            ],
            [
                'name' => 'Singapore', 'slug' => 'singapore', 'local' => 'international', 'country' => 'Singapore', 'city' => 'Singapore', 'color' => '#e11d48',
                'description' => 'Singapore packs Universal Studios, Gardens by the Bay and Sentosa Island\'s beaches and cable car into one compact, family-friendly city-state.',
                'meta_title' => 'Singapore Tourism | Sentosa, Gardens by the Bay & Universal Studios | FareBuzzer Travel',
                'meta_description' => 'Plan your Singapore trip with FareBuzzer Travel — Universal Studios, Gardens by the Bay, Sentosa cable car and Singapore Flyer, with tour packages from India.',
                'meta_keywords' => 'singapore tourism, singapore tour package, sentosa universal studios, singapore package from india',
                'focus_keyword' => 'Singapore tour package from India',
                'seo_content' => "<p>Singapore is Asia's most polished city-break destination — Universal Studios and Sentosa Island's beaches and cable car, the futuristic Gardens by the Bay, panoramic views from the Singapore Flyer, and an evening Night Safari at the zoo.</p><h2>Why Visit Singapore</h2><p>Well-connected by direct flights from India and famously safe and clean, Singapore is ideal for families, honeymooners and first-time international travellers alike.</p>",
                'cover_commons' => 'Marina Bay Sands.jpg',
                'gallery_commons' => ['Gardens by the Bay.jpg', 'Singapore Flyer.jpg'],
            ],
        ];
    }

    // Wikimedia Commons file titles for the demo (non-catalogue) destinations'
    // cover photo, keyed by destination name. Verified to resolve via
    // Special:FilePath at the time these were added.
    private function realCoverCommons(): array
    {
        return [
            'Delhi' => 'Red Fort Delhi.jpg',
            'Jaipur' => 'Hawa Mahal.jpg',
            'Gurgaon' => 'Gurgaon skyline.jpg',
            'Madurai' => 'Meenakshi Amman Temple.jpg',
            'Mahabalipuram' => 'Shore Temple.jpg',
            'Lucknow' => 'Bara Imambara.jpg',
            'Varanasi' => 'Dashashwamedh Ghat.jpg',
            'Hyderabad' => 'Charminar.jpg',
            'Kolkata' => 'Victoria Memorial Kolkata.jpg',
            'Ranthambore' => 'Ranthambhore Fort.jpg',
            'Kaziranga' => 'Kaziranga National Park.jpg',
        ];
    }

    // Direct known-good URLs for destinations whose Commons title doesn't
    // reliably resolve via Special:FilePath at seed time (same issue as Dubai).
    private function realCoverOverrides(): array
    {
        return [
            'Bhopal' => 'https://upload.wikimedia.org/wikipedia/commons/a/a7/Taj-ul-Masajid_Bhopal.JPG',
            'Ayodhya' => 'https://upload.wikimedia.org/wikipedia/commons/d/d1/Ram_Janmbhoomi_Mandir%2C_Ayodhya_Dham.jpg',
        ];
    }

    private function realGalleryCommons(): array
    {
        return [
            'Delhi' => ['Qutub Minar.jpg', "Humayun's Tomb.jpg"],
            'Jaipur' => ['Amber Fort.jpg', 'City Palace, Jaipur.jpg'],
            'Gurgaon' => ['Kingdom of Dreams, Gurgaon.jpg', 'Ambience Mall, Gurgaon.jpg'],
            'Madurai' => ['Thirumalai Nayakkar Mahal.jpg', 'Vaigai River.jpg'],
            'Mahabalipuram' => ['Pancha Rathas.jpg', "Arjuna's Penance.jpg"],
            'Lucknow' => ['Rumi Darwaza.jpg', 'Chota Imambara.jpg'],
            'Bhopal' => ['Upper Lake, Bhopal.jpg', 'Van Vihar National Park.jpg'],
            'Varanasi' => ['Kashi Vishwanath Temple.jpg', 'Ganga Aarti Varanasi.jpg'],
            'Hyderabad' => ['Golconda Fort.jpg', 'Hussain Sagar.jpg'],
            'Kolkata' => ['Howrah Bridge.jpg', 'Dakshineswar Kali Temple.jpg'],
            'Ayodhya' => ['Ayodhya.jpg', 'Guptar Ghat.jpg'],
            'Ranthambore' => ['Ranthambore National park.jpg', 'Tiger in Ranthambore National Park.jpg'],
            'Kaziranga' => ['Rhino at Kaziranga National Park.jpg', 'Deer in Kaziranga National Park.jpg'],
        ];
    }

    private function destinationCoverImage(string $name, string $color): string
    {
        if ($name === 'Goa') {
            // The previous Unsplash id here (photo-1587474260584-136574528ed5) is actually
            // India Gate, Delhi — not Goa. Palolem Beach (Wikimedia Commons) instead.
            return $this->downloadFromCommons('Palolem Beach, south Goa.jpg', 'destinations')
                ?? $this->placeholderImage(1200, 600, $name, 'destinations', $color);
        }

        $override = $this->realCoverOverrides()[$name] ?? null;
        $commonsTitle = $this->realCoverCommons()[$name] ?? null;

        if ($commonsTitle) {
            return $this->downloadFromCommons($commonsTitle, 'destinations')
                ?? $override
                ?? $this->placeholderImage(1200, 600, $name, 'destinations', $color);
        }

        return $override ?? $this->placeholderImage(1200, 600, $name, 'destinations', $color);
    }

    private function destinationGalleryImages(string $name, string $color): array
    {
        if ($name === 'Goa') {
            $urls = [
                'https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?w=800&q=70',
                'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?w=800&q=70',
            ];
            $paths = array_values(array_filter(array_map(
                fn ($url) => $this->downloadImage($url, 'destinations/gallery'),
                $urls
            )));
            if (count($paths) === count($urls)) {
                return $paths;
            }
        }

        $titles = $this->realGalleryCommons()[$name] ?? null;
        if ($titles) {
            $paths = array_values(array_filter(array_map(
                fn ($title) => $this->downloadFromCommons($title, 'destinations/gallery'),
                $titles
            )));
            if (count($paths) === count($titles)) {
                return $paths;
            }
        }

        return [
            $this->placeholderImage(800, 600, $name.' 1', 'destinations/gallery', $color),
            $this->placeholderImage(800, 600, $name.' 2', 'destinations/gallery', $color),
        ];
    }
}
