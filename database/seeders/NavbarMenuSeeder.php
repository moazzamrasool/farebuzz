<?php

namespace Database\Seeders;

use App\Models\NavbarMenuItem;
use App\Support\SiteTenant;
use Illuminate\Database\Seeder;

class NavbarMenuSeeder extends Seeder
{
    // Seeds the navbar's current 6 items as plain internal-route links, so the site
    // looks identical the moment the navbar starts reading from this table.
    public function run(): void
    {
        foreach ([
            ['label' => 'Home',                   'link_value' => 'home'],
            ['label' => 'India Packages',          'link_value' => 'packages.india'],
            ['label' => 'International Packages',  'link_value' => 'packages.international'],
            ['label' => 'Hotels',                  'link_value' => 'hotels.index'],
            ['label' => 'Activities',              'link_value' => 'activities.index'],
            ['label' => 'Mice',                    'link_value' => 'packages.mice'],
            ['label' => 'Blog',                    'link_value' => 'blog.index'],
            ['label' => 'About Us',                'link_value' => 'about-us'],
            ['label' => 'Contact Us',               'link_value' => 'contact_us'],
        ] as $index => $item) {
            NavbarMenuItem::updateOrCreate(
                ['link_type' => 'route', 'link_value' => $item['link_value']],
                [
                    'unique_id'       => SiteTenant::id(),
                    'label'           => $item['label'],
                    'parent_id'       => null,
                    'open_in_new_tab' => false,
                    'sort_order'      => $index,
                    'status'          => 'active',
                ]
            );
        }
    }
}
