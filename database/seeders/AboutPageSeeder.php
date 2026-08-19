<?php

namespace Database\Seeders;

use App\Models\AboutPage;
use App\Models\AboutPageItem;
use App\Support\SiteTenant;
use Illuminate\Database\Seeder;

class AboutPageSeeder extends Seeder
{
    public function run(): void
    {
        $about = AboutPage::updateOrCreate(
            ['unique_id' => SiteTenant::id()],
            [
                'hero_heading' => 'Personalised, Once In A Lifetime Trips',
                'hero_tagline' => 'Founded to make travel planning effortless, personal, and reliable — from the first spark of an idea to the moment you land back home with stories to tell.',

                'story_heading' => 'Our Story',
                'story_body' => '<p>FareBuzzer is your trusted travel companion for flights, hotels, holidays, trains and more. We partner with the best hotels, airlines and local experts to bring you curated travel experiences at the best prices.</p>',

                'mission_heading' => 'Our Mission',
                'mission_body' => '<p>To make travel planning effortless — from the first spark of an idea to the moment you land back home with stories to tell. We believe great trips shouldn\'t require expert-level research, so we do the hard part for you.</p>',

                'team_heading' => 'The Talent Behind Every Journey',
                'team_subheading' => 'A small, obsessive team of travel planners, engineers and support experts.',

                'feature1_icon' => 'bi-shield-check', 'feature1_title' => 'Trusted Partners', 'feature1_description' => 'We work only with verified hotels, airlines and local experts.',
                'feature2_icon' => 'bi-tag', 'feature2_title' => 'Best Prices', 'feature2_description' => 'Handpicked packages at the most competitive prices, guaranteed.',
                'feature3_icon' => 'bi-headset', 'feature3_title' => '24/7 Support', 'feature3_description' => 'Our travel experts are always here to help, before and during your trip.',

                'growth_heading' => 'We Are Growing!',
                'growth_subheading' => 'Year-on-year booking growth across all destinations.',

                'timeline_heading' => 'Journey That Made FareBuzzer',
                'timeline_subheading' => 'From a small idea to a full-fledged travel company.',

                'life_heading' => 'Life At FareBuzzer',
                'life_subheading' => 'Celebrating autonomy, adventure and small wins.',
                'life_body' => 'We travel as much as we help others travel. Flexible leave, team offsites and a genuine love for exploring new places keep the FareBuzzer team as adventurous as our customers.',

                'gallery_heading' => 'FareBuzzer Picture Gallery',

                'testimonials_heading' => 'Voices of FareBuzzer',

                'press_heading' => 'In The Spotlight',

                'impact_heading' => 'Stories of Impact',
                'impact_body' => 'Every itinerary we plan is someone\'s once-in-a-lifetime trip. Hearing back from travellers about the memories they made is why we do this.',

                'awards_heading' => 'Awards & Recognition',

                'meta_title' => 'About Us – FareBuzzer',
            ]
        );

        $sections = [
            'mission_card' => [
                ['title' => 'Customer First', 'subtitle' => 'bi-heart', 'description' => 'Every decision starts with what\'s best for the traveller.'],
                ['title' => 'Honest Pricing', 'subtitle' => 'bi-tag', 'description' => 'No hidden fees, no surprise mark-ups — ever.'],
                ['title' => 'Local Expertise', 'subtitle' => 'bi-geo-alt', 'description' => 'On-ground partners who know each destination inside out.'],
                ['title' => 'Constant Improvement', 'subtitle' => 'bi-graph-up-arrow', 'description' => 'We ship better itineraries and better support every quarter.'],
            ],
            'team_member' => [
                ['title' => 'Aisha Khan', 'subtitle' => 'Co-Founder & CEO', 'image_url' => 'frontend/img/about/team-1.svg'],
                ['title' => 'Rohan Mehta', 'subtitle' => 'Head of Product', 'image_url' => 'frontend/img/about/team-2.svg'],
                ['title' => 'Priya Sharma', 'subtitle' => 'Head of Operations', 'image_url' => 'frontend/img/about/team-3.svg'],
                ['title' => 'Farhan Ali', 'subtitle' => 'Head of Partnerships', 'image_url' => 'frontend/img/about/team-4.svg'],
                ['title' => 'Neha Verma', 'subtitle' => 'Customer Experience Lead', 'image_url' => 'frontend/img/about/team-5.svg'],
                ['title' => 'Sameer Iqbal', 'subtitle' => 'Engineering Lead', 'image_url' => 'frontend/img/about/team-6.svg'],
            ],
            'growth_stat' => [
                ['title' => '20%', 'subtitle' => '2021'],
                ['title' => '35%', 'subtitle' => '2022'],
                ['title' => '67%', 'subtitle' => '2023'],
                ['title' => '82%', 'subtitle' => '2024'],
                ['title' => '48%', 'subtitle' => '2025'],
            ],
            'timeline' => [
                ['title' => 'FareBuzzer Founded', 'subtitle' => '2015', 'description' => 'Started with a single mission: make travel planning effortless.'],
                ['title' => 'First 1,000 Bookings', 'subtitle' => '2017', 'description' => 'Crossed our first big booking milestone, all through word of mouth.'],
                ['title' => 'Expanded Internationally', 'subtitle' => '2019', 'description' => 'Launched international packages across South East Asia and Europe.'],
                ['title' => 'Hit 10,000 Happy Travellers', 'subtitle' => '2021', 'description' => 'A decade of trust, one curated trip at a time.'],
                ['title' => 'Launched MICE & Group Travel', 'subtitle' => '2023', 'description' => 'Started serving corporate and large group travel needs.'],
                ['title' => '50,000+ Travellers Strong', 'subtitle' => '2025', 'description' => 'Today, FareBuzzer serves travellers across India and abroad.'],
            ],
            'gallery' => [
                ['title' => 'Team offsite', 'image_url' => 'https://images.unsplash.com/photo-1543269865-cbf427effbad?w=500&q=80'],
                ['title' => 'Destination scouting', 'image_url' => 'https://images.unsplash.com/photo-1530521954074-e64f6810b32d?w=500&q=80'],
                ['title' => 'Customer meetup', 'image_url' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=500&q=80'],
                ['title' => 'Office life', 'image_url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=500&q=80'],
            ],
            'testimonial' => [
                ['title' => 'Ananya R.', 'subtitle' => 'Customer Experience', 'description' => 'What I love about working here is that we actually get to travel to the places we sell — it shows in how we plan trips.'],
                ['title' => 'Vikram S.', 'subtitle' => 'Operations', 'description' => 'FareBuzzer trusts its team with real ownership. My ideas for improving the booking flow shipped within weeks.'],
                ['title' => 'Meera J.', 'subtitle' => 'Product', 'description' => 'A genuinely supportive team — we celebrate wins together and learn from misses just as openly.'],
            ],
            // Press/award/trust-badge images are served locally (frontend/img/about/*.svg) rather
            // than hotlinked from placehold.co — that free service rate-limits/blocks hotlinking
            // in production, which was rendering these as blank boxes on the live site.
            'press' => [
                ['title' => 'Travel Weekly', 'image_url' => 'frontend/img/about/press-1.svg'],
                ['title' => 'Economic Times', 'image_url' => 'frontend/img/about/press-2.svg'],
                ['title' => 'YourStory', 'image_url' => 'frontend/img/about/press-3.svg'],
            ],
            'award' => [
                ['title' => 'Best Travel Startup 2023', 'image_url' => 'frontend/img/about/award-1.svg'],
                ['title' => 'Top Rated on Trustpilot', 'image_url' => 'frontend/img/about/award-2.svg'],
            ],
        ];

        foreach ($sections as $sectionKey => $rows) {
            foreach ($rows as $i => $row) {
                AboutPageItem::updateOrCreate(
                    [
                        'unique_id' => SiteTenant::id(),
                        'section_key' => $sectionKey,
                        'title' => $row['title'],
                    ],
                    [
                        'subtitle' => $row['subtitle'] ?? null,
                        'description' => $row['description'] ?? null,
                        'link' => $row['link'] ?? null,
                        'image' => $row['image_url'] ?? null,
                        'sort_order' => $i,
                        'status' => 'active',
                    ]
                );
            }
        }
    }
}
