<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// One-off content fix: seed data was written before the brand was finalized as
// "FareBuzzer", so several CMS/legal pages and homepage content still read "FareBuzz"
// (missing the "er") and reference the wrong email domain (farebuzz.com instead of
// farebuzzertravel.com). Rewrites in place; "FareBuzzer" occurrences are left untouched
// via a negative lookahead so this never turns "FareBuzzer" into "FareBuzzerer".
return new class extends Migration
{
    private const BRAND_PATTERN = '/FareBuzz(?!er)/';

    private const BRAND_REPLACEMENT = 'FareBuzzer';

    public function up(): void
    {
        $this->fixColumn('cms_pages', 'body');
        $this->fixColumn('cms_pages', 'meta_title');
        $this->fixColumn('package_features', 'title');
        $this->fixHomepageFooterExtra();
    }

    private function fixColumn(string $table, string $column): void
    {
        $rows = DB::table($table)->whereNotNull($column)->get(['id', $column]);

        foreach ($rows as $row) {
            $original = $row->{$column};
            $new = preg_replace(self::BRAND_PATTERN, self::BRAND_REPLACEMENT, $original);
            $new = str_replace('@farebuzz.com', '@farebuzzertravel.com', $new);

            if ($new === $original) {
                continue;
            }

            DB::table($table)->where('id', $row->id)->update([$column => $new]);

            Log::info("Standardized FareBuzzer brand text in {$table}.{$column}", [
                'id' => $row->id,
                'old' => $original,
                'new' => $new,
            ]);
        }
    }

    private function fixHomepageFooterExtra(): void
    {
        $rows = DB::table('homepage_sections')->where('key', 'footer')->whereNotNull('extra')->get(['id', 'extra']);

        foreach ($rows as $row) {
            $extra = json_decode($row->extra, true);

            if (!is_array($extra) || empty($extra['copyright_text'])) {
                continue;
            }

            $original = $extra['copyright_text'];
            $new = preg_replace(self::BRAND_PATTERN, self::BRAND_REPLACEMENT, $original);

            if ($new === $original) {
                continue;
            }

            $extra['copyright_text'] = $new;

            DB::table('homepage_sections')->where('id', $row->id)->update([
                'extra' => json_encode($extra),
            ]);

            Log::info('Standardized FareBuzzer brand text in homepage_sections.extra.copyright_text', [
                'id' => $row->id,
                'old' => $original,
                'new' => $new,
            ]);
        }
    }

    public function down(): void
    {
        // Not reversible — the original "FareBuzz" text was the bug being fixed.
    }
};
