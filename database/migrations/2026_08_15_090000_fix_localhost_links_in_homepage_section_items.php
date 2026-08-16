<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// One-off content fix: an admin pasted full http://localhost:... URLs (copied from a
// local dev environment) into homepage_section_items.link instead of a relative path.
// App\Support\MediaUrl::link() passes any http(s):// value through verbatim, so these
// leaked straight into production <a href> tags. Rewriting to a bare relative path makes
// them resolve correctly via APP_URL regardless of environment, same as every other
// (correctly-entered) row in this table.
return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('homepage_section_items')
            ->where(function ($query) {
                $query->where('link', 'like', 'http://localhost%')
                    ->orWhere('link', 'like', 'https://localhost%')
                    ->orWhere('link', 'like', 'http://127.0.0.1%')
                    ->orWhere('link', 'like', 'https://127.0.0.1%');
            })
            ->get(['id', 'link']);

        foreach ($rows as $row) {
            $path = parse_url($row->link, PHP_URL_PATH) ?? '';
            $query = parse_url($row->link, PHP_URL_QUERY);
            $new = ltrim($path, '/').($query ? '?'.$query : '');

            DB::table('homepage_section_items')->where('id', $row->id)->update(['link' => $new]);

            Log::info('Rewrote localhost link in homepage_section_items', [
                'id' => $row->id,
                'old' => $row->link,
                'new' => $new,
            ]);
        }
    }

    public function down(): void
    {
        // Not reversible — the original localhost values were a bug, not a
        // configuration worth restoring.
    }
};
