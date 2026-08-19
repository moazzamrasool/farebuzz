<?php

namespace App\Services\Seo;

use App\Models\Activity;
use App\Models\Blog;
use App\Models\CmsPage;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\HolidayPackage;
use App\Models\SeoSetting;
use App\Support\SiteTenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

// Builds sitemap.xml for the single company the public frontend serves (SiteTenant::id()).
// The rendered XML is cached for CACHE_TTL_MINUTES so a page of static Blade+DB work
// doesn't re-run on every crawler hit; "Regenerate Now" in the admin screen forces a
// fresh build and refreshes the cache immediately instead of waiting for it to expire.
class SitemapGenerator
{
    private const CACHE_TTL_MINUTES = 60;

    public function render(): string
    {
        return Cache::remember($this->cacheKey(), now()->addMinutes(self::CACHE_TTL_MINUTES), fn () => $this->build()['xml']);
    }

    public function regenerate(): array
    {
        $result = $this->build();

        Cache::put($this->cacheKey(), $result['xml'], now()->addMinutes(self::CACHE_TTL_MINUTES));

        return $result;
    }

    private function cacheKey(): string
    {
        return 'sitemap:xml:'.(SiteTenant::id() ?? 'default');
    }

    private function build(): array
    {
        $setting = SeoSetting::forSite()->first();
        $config = $setting?->sitemapConfig() ?? SeoSetting::DEFAULT_SITEMAP_CONFIG;

        $urls = $this->staticUrls();

        if ($config['holiday_packages']['included']) {
            $urls = array_merge($urls, $this->recordUrls(
                HolidayPackage::forSite()->where('status', 'active')->get(['slug', 'updated_at']),
                fn ($p) => url("/packages/{$p->slug}"),
                $config['holiday_packages'],
            ));
        }

        if ($config['hotels']['included']) {
            $urls = array_merge($urls, $this->recordUrls(
                Hotel::forSite()->where('status', 'active')->get(['slug', 'updated_at']),
                fn ($h) => url("/hotels/{$h->slug}"),
                $config['hotels'],
            ));
        }

        if ($config['activities']['included']) {
            $urls = array_merge($urls, $this->recordUrls(
                Activity::forSite()->where('status', 'active')->get(['slug', 'updated_at']),
                fn ($a) => url("/activities/{$a->slug}"),
                $config['activities'],
            ));
        }

        if ($config['destinations']['included']) {
            $urls = array_merge($urls, $this->recordUrls(
                Destination::forSite()->where('status', 'active')->get(['slug', 'updated_at']),
                fn ($d) => url("/destinations/{$d->slug}"),
                $config['destinations'],
            ));
        }

        if ($config['destination_packages']['included']) {
            $urls = array_merge($urls, $this->recordUrls(
                Destination::forSite()->where('status', 'active')->get(['slug', 'updated_at']),
                fn ($d) => url("/destinations/{$d->slug}/packages"),
                $config['destination_packages'],
            ));
        }

        if ($config['cms_pages']['included']) {
            $urls = array_merge($urls, $this->recordUrls(
                CmsPage::forSite()->where('status', 'active')->get(['slug', 'updated_at']),
                fn ($p) => url("/{$p->slug}"),
                $config['cms_pages'],
            ));
        }

        if ($config['blog']['included']) {
            $urls = array_merge($urls, $this->recordUrls(
                Blog::forSite()->where('status', 'published')->get(['slug', 'updated_at']),
                fn ($b) => url("/blog/{$b->slug}"),
                $config['blog'],
            ));
        }

        $xml = $this->toXml($urls);
        $count = count($urls);

        // Only stamp status metadata onto an existing row — never auto-create one from
        // a public request (this runs for anonymous crawlers with no admin session).
        $setting?->update([
            'sitemap_generated_at' => now(),
            'sitemap_url_count' => $count,
        ]);

        return ['xml' => $xml, 'count' => $count];
    }

    // Top-level pages that always exist regardless of catalog content — no single
    // record's updated_at is meaningful for these, so lastmod is omitted.
    private function staticUrls(): array
    {
        return [
            ['loc' => url('/'), 'lastmod' => null, 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => url('/about-us'), 'lastmod' => null, 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => url('/contact-us'), 'lastmod' => null, 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => url('/india-packages'), 'lastmod' => null, 'changefreq' => 'daily', 'priority' => '0.8'],
            ['loc' => url('/international-packages'), 'lastmod' => null, 'changefreq' => 'daily', 'priority' => '0.8'],
            ['loc' => url('/holiday-packages'), 'lastmod' => null, 'changefreq' => 'daily', 'priority' => '0.8'],
            ['loc' => url('/mice'), 'lastmod' => null, 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => url('/hotels'), 'lastmod' => null, 'changefreq' => 'daily', 'priority' => '0.8'],
            ['loc' => url('/activities'), 'lastmod' => null, 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => url('/destinations'), 'lastmod' => null, 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => url('/blog'), 'lastmod' => null, 'changefreq' => 'daily', 'priority' => '0.6'],
        ];
    }

    private function recordUrls(iterable $records, \Closure $locBuilder, array $typeConfig): array
    {
        $urls = [];

        foreach ($records as $record) {
            $urls[] = [
                'loc' => $locBuilder($record),
                'lastmod' => $record->updated_at instanceof Carbon ? $record->updated_at : null,
                'changefreq' => $typeConfig['changefreq'],
                'priority' => $typeConfig['priority'],
            ];
        }

        return $urls;
    }

    private function toXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8')."</loc>\n";
            if ($url['lastmod']) {
                $xml .= '    <lastmod>'.$url['lastmod']->toAtomString()."</lastmod>\n";
            }
            $xml .= '    <changefreq>'.$url['changefreq']."</changefreq>\n";
            $xml .= '    <priority>'.$url['priority']."</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
