<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// One-off content fix: two footer links were left as "#" placeholders even though the
// pages they should point to already exist and are live (routes/user.php "dashboard/trips",
// routes/web.php "hotels.index"). Matched by section/group/title text rather than row id,
// so this is safe to run against any environment's data, not just this dev copy.
return new class extends Migration
{
    private const FIXES = [
        ['group_key' => 'support', 'title' => 'My Trips', 'link' => 'dashboard/trips'],
        ['group_key' => 'products', 'title' => 'Hotels', 'link' => 'hotels'],
    ];

    public function up(): void
    {
        $footerSectionIds = DB::table('homepage_sections')->where('key', 'footer')->pluck('id');

        foreach (self::FIXES as $fix) {
            $rows = DB::table('homepage_section_items')
                ->whereIn('homepage_section_id', $footerSectionIds)
                ->where('group_key', $fix['group_key'])
                ->where('title', $fix['title'])
                ->where('link', '#')
                ->get(['id']);

            foreach ($rows as $row) {
                DB::table('homepage_section_items')->where('id', $row->id)->update(['link' => $fix['link']]);

                Log::info('Fixed dead footer link', [
                    'id' => $row->id,
                    'group_key' => $fix['group_key'],
                    'title' => $fix['title'],
                    'new_link' => $fix['link'],
                ]);
            }
        }
    }

    public function down(): void
    {
        // Not reversible — "#" was the bug being fixed, not a value worth restoring.
    }
};
