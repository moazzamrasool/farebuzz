<?php

namespace App\Services\Seo;

class RobotsTxtBuilder
{
    public static function default(): string
    {
        return implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /crm',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /dashboard',
            'Disallow: /api/',
        ]);
    }

    // Renders the final robots.txt served to crawlers: the saved (or default) body with
    // any existing "Sitemap:" line stripped, then the correct one re-appended — so the
    // sitemap URL is always derived from APP_URL and can never go stale or point
    // somewhere wrong, even if the saved text was written before a domain change.
    public static function render(?string $stored): string
    {
        $body = ($stored !== null && trim($stored) !== '') ? $stored : self::default();

        $lines = preg_split('/\R/', $body);
        $lines = array_filter($lines, fn ($line) => !preg_match('/^\s*sitemap\s*:/i', $line));

        $body = trim(implode("\n", $lines));

        return $body."\n\nSitemap: ".url('/sitemap.xml');
    }

    // Detects a "Disallow: /" line inside the default (*) user-agent block — saving this
    // as-is would deindex the entire site, so the admin UI requires explicit confirmation
    // before it's allowed to be saved.
    public static function blocksEverything(string $content): bool
    {
        $lines = preg_split('/\R/', $content);
        $currentAgentIsWildcard = false;

        foreach ($lines as $line) {
            $line = trim($line);

            if (preg_match('/^user-agent\s*:\s*(.+)$/i', $line, $m)) {
                $currentAgentIsWildcard = trim($m[1]) === '*';
                continue;
            }

            if ($currentAgentIsWildcard && preg_match('#^disallow\s*:\s*/\s*$#i', $line)) {
                return true;
            }
        }

        return false;
    }
}
