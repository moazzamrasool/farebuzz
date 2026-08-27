<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityCategory;
use App\Models\Destination;
use App\Support\SiteTenant;
use Illuminate\Database\Seeder;

// Backfills meta_title/meta_description/meta_keywords/focus_keyword for the
// existing "Gulmarg Gondola" activity (Kashmir/Adventure) — those SEO fields
// were blank. Idempotent — safe to re-run; only these four columns are
// touched, canonical_url/OG/robots are left null so SeoResolver's fallbacks
// (current URL, activity image, etc.) keep applying, matching the convention
// used by SeoMetaContentSeeder. Resolves the Destination, ActivityCategory
// and Activity by name within the tenant and fails loudly rather than
// creating any new master record if one is missing.
class GulmargGondolaSeoSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = SiteTenant::id();

        if (!$tenantId) {
            $this->command?->error('COMPANY_UNIQUE_ID is not set — aborting, nothing seeded.');
            return;
        }

        $destination = Destination::where('unique_id', $tenantId)->where('name', 'Kashmir')->first();
        if (!$destination) {
            $this->command?->error('Destination "Kashmir" not found for this tenant — aborting rather than creating one.');
            return;
        }

        $category = ActivityCategory::where('unique_id', $tenantId)->where('name', 'Adventure')->first();
        if (!$category) {
            $this->command?->error('Activity Category "Adventure" not found for this tenant — aborting rather than creating one.');
            return;
        }

        $activity = Activity::where('unique_id', $tenantId)
            ->where('destination_id', $destination->id)
            ->where('activity_category_id', $category->id)
            ->where('name', 'Gulmarg Gondola')
            ->first();

        if (!$activity) {
            $this->command?->error('Activity "Gulmarg Gondola" not found for Kashmir/Adventure — aborting rather than creating a new one.');
            return;
        }

        $activity->update([
            'meta_title'       => 'Gulmarg Gondola Ride | Kongdoori & Apharwat Cable Car | FareBuzzer Travel',
            'meta_description' => "Book the Gulmarg Gondola Ride with FareBuzzer Travel. Ride Asia's highest cable car from Gulmarg to Kongdoori & Apharwat Peak for sweeping Pir Panjal views.",
            'meta_keywords'    => 'gulmarg gondola ride, gulmarg cable car, kongdoori gondola price, apharwat peak gondola, gulmarg gondola ticket',
            'focus_keyword'    => 'Gulmarg Gondola Ride',
        ]);

        $this->command?->info('Gulmarg Gondola (#'.$activity->id.') SEO fields updated.');
    }
}
