<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

// One-off cleanup: the "pages" table (backing the near-unused App\Models\Page, not the
// CmsPage model that actually powers the live /{slug} route) holds leftover content from
// an unrelated "Winify Logistics" courier-company template — not tenant-scoped, and not
// rendered by any live route (confirmed: DashboardController::pages() queries CmsPage).
// Clears the rows rather than dropping the table, since it's a content fix, not a schema
// change.
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pages')) {
            return;
        }

        $rows = DB::table('pages')->get(['id', 'page_title']);

        DB::table('pages')->delete();

        Log::info('Cleared orphaned legacy "pages" table content', [
            'deleted_count' => $rows->count(),
            'deleted_ids' => $rows->pluck('id'),
        ]);
    }

    public function down(): void
    {
        // Not reversible — this was unreachable leftover content from an unrelated
        // company template, not data worth restoring.
    }
};
