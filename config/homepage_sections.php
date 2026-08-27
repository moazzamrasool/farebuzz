<?php

// Declarative shape of each homepage section so ONE controller + ONE pair of
// admin views (resources/views/admin/homepage/index.blade.php + edit.blade.php)
// can render/validate/save all 14 sections without per-section duplicate code.
//
// 'fields'      => which of the generic HomepageSectionItem columns this section's
//                  cards use (image, title, subtitle, description, label, link,
//                  button_label, price, old_price, rating, review_count).
// 'meta_fields' => extra named keys stored inside the item's `meta` json column
//                  (e.g. ['nights'], ['tile_size']) — rendered as plain inputs.
// 'group'       => null, or how group_key is chosen for each item:
//                  ['type' => 'tabs']            user manages the tab list themselves (stored in section.extra.tabs)
//                  ['type' => 'fixed', 'options' => [...]]  a fixed dropdown of allowed group_key values
// 'extra_fields'=> section-level (not per-item) fields stored in homepage_sections.extra json.

return [

    'hero' => [
        'name' => 'Hero / Banner',
        'fields' => ['image'],
        'meta_fields' => [],
        'group' => ['type' => 'fixed', 'options' => ['banner' => 'Banner']],
        'extra_fields' => [],
    ],

    'offers' => [
        'name' => 'Offers',
        'fields' => ['image', 'title', 'subtitle', 'description', 'label', 'link', 'button_label'],
        'meta_fields' => [],
        'group' => ['type' => 'fixed', 'options' => [
            'all' => 'All Offers', 'flights' => 'Flights', 'hotels' => 'Hotels',
            'holidays' => 'Holidays', 'trains' => 'Trains', 'cabs' => 'Cabs',
        ]],
        'extra_fields' => [],
    ],

    'travel_pros' => [
        'name' => 'For Travel Pros',
        'fields' => ['image', 'title', 'subtitle', 'link'],
        'meta_fields' => [],
        'group' => null,
        'extra_fields' => [],
    ],

    'domestic_packages' => [
        'name' => 'Domestic Holiday Packages',
        'data_source' => 'domestic_packages',
        'fields' => [],
        'meta_fields' => [],
        'group' => null,
        'extra_fields' => [],
    ],

    'international_packages' => [
        'name' => 'International Holiday Packages',
        'data_source' => 'international_packages',
        'fields' => [],
        'meta_fields' => [],
        'group' => null,
        'extra_fields' => [],
    ],

    'top_activities' => [
        'name' => 'Top Activities',
        'data_source' => 'top_activities',
        'fields' => [],
        'meta_fields' => [],
        'group' => null,
        'extra_fields' => [],
    ],

    'handpicked_hotels' => [
        'name' => 'Handpicked Stays',
        'data_source' => 'handpicked_hotels',
        'fields' => [],
        'meta_fields' => [],
        'group' => null,
        'extra_fields' => [],
    ],

    'trending_destinations' => [
        'name' => 'Trending Destinations',
        'data_source' => 'trending_destinations',
        'fields' => [],
        'meta_fields' => [],
        'group' => null,
        'extra_fields' => [],
    ],

    'trip_planner' => [
        'name' => 'Quick and Easy Trip Planner',
        'fields' => ['image', 'title', 'link'],
        'meta_fields' => ['distance_km'],
        'group' => ['type' => 'tabs'],
        'extra_fields' => [],
    ],

    'explore_india' => [
        'name' => 'Explore India',
        'fields' => ['image', 'title', 'link'],
        'meta_fields' => ['property_count'],
        'group' => null,
        'extra_fields' => [],
    ],

    'property_types' => [
        'name' => 'Browse by Property Type',
        'fields' => ['image', 'title', 'link'],
        'meta_fields' => [],
        'group' => null,
        'extra_fields' => [],
    ],

    'country_spotlight' => [
        'name' => 'Featured Country Spotlight',
        'fields' => ['image', 'title', 'description', 'link'],
        'meta_fields' => [],
        'group' => null,
        'extra_fields' => [],
    ],

    'footer' => [
        'name' => 'Footer',
        'fields' => ['title', 'link'],
        'meta_fields' => ['icon'],
        'group' => ['type' => 'fixed', 'options' => [
            'company' => 'Company', 'products' => 'Products',
            'support' => 'Support', 'download_app' => 'Download App',
            'legal' => 'Legal (bottom bar)',
        ]],
        'extra_fields' => ['blurb', 'social_facebook', 'social_twitter', 'social_instagram', 'social_youtube', 'copyright_text', 'google_review_url', 'google_review_enabled'],
    ],

];
