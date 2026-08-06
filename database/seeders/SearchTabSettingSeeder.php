<?php

namespace Database\Seeders;

use App\Models\SearchTabSetting;
use App\Support\SiteTenant;
use Illuminate\Database\Seeder;

class SearchTabSettingSeeder extends Seeder
{
    // Holiday Packages, Hotels and Activities start enabled (Phase 2); the rest
    // are "Coming soon" until turned on from the CRM. See PART A.
    public function run(): void
    {
        $tabs = [
            ['tab_key' => 'holidays',  'label' => 'Holiday Packages', 'enabled' => true,  'sort_order' => 0],
            ['tab_key' => 'hotels',    'label' => 'Hotels',           'enabled' => true,  'sort_order' => 1],
            ['tab_key' => 'activities','label' => 'Activities',       'enabled' => true,  'sort_order' => 2],
            ['tab_key' => 'flights',   'label' => 'Flights',          'enabled' => false, 'sort_order' => 3],
            ['tab_key' => 'homestays', 'label' => 'Homestays',        'enabled' => false, 'sort_order' => 4],
            ['tab_key' => 'trains',    'label' => 'Trains',           'enabled' => false, 'sort_order' => 5],
            ['tab_key' => 'buses',     'label' => 'Buses',            'enabled' => false, 'sort_order' => 6],
            ['tab_key' => 'cabs',      'label' => 'Cabs',             'enabled' => false, 'sort_order' => 7],
        ];

        foreach ($tabs as $tab) {
            SearchTabSetting::firstOrCreate(
                ['tab_key' => $tab['tab_key']],
                [
                    'unique_id'  => SiteTenant::id(),
                    'label'      => $tab['label'],
                    'enabled'    => $tab['enabled'],
                    'badge_text' => $tab['enabled'] ? null : 'Coming soon',
                    'sort_order' => $tab['sort_order'],
                ]
            );
        }
    }
}
