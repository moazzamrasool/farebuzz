<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\ForSiteTenant;
use Illuminate\Database\Eloquent\Model;

// One row per tenant — sitemap + robots.txt configuration for that company's public site.
// See App\Services\Seo\SitemapGenerator and App\Services\Seo\RobotsTxtBuilder.
class SeoSetting extends Model
{
    use BelongsToTenant, ForSiteTenant;

    // Per content-type sitemap toggles, used whenever sitemap_config is null on the row.
    public const DEFAULT_SITEMAP_CONFIG = [
        'holiday_packages' => ['included' => true, 'priority' => '0.7', 'changefreq' => 'weekly'],
        'hotels'           => ['included' => true, 'priority' => '0.7', 'changefreq' => 'weekly'],
        'activities'       => ['included' => true, 'priority' => '0.6', 'changefreq' => 'weekly'],
        'destinations'     => ['included' => true, 'priority' => '0.7', 'changefreq' => 'weekly'],
        'destination_packages' => ['included' => true, 'priority' => '0.6', 'changefreq' => 'weekly'],
        'cms_pages'        => ['included' => true, 'priority' => '0.5', 'changefreq' => 'monthly'],
        'blog'             => ['included' => true, 'priority' => '0.5', 'changefreq' => 'monthly'],
    ];

    protected $fillable = [
        'unique_id',
        'robots_txt',
        'sitemap_config',
        'sitemap_generated_at',
        'sitemap_url_count',
        'default_og_image',
        'organization_name',
        'organization_logo',
        'social_links',
    ];

    protected function casts(): array
    {
        return [
            'sitemap_config'        => 'array',
            'sitemap_generated_at'  => 'datetime',
            'sitemap_url_count'     => 'integer',
            'social_links'          => 'array',
        ];
    }

    // Mirrors WhatsAppSetting::forCurrentTenant() — always runs behind the admin guard's
    // tenant scope, so firstOrCreate naturally lands on that tenant's single row.
    public static function forCurrentTenant(): self
    {
        return static::query()->firstOrCreate([]);
    }

    // Saved per-type toggles merged over the built-in defaults, so a row that only
    // customizes one type doesn't lose sensible values for the rest.
    public function sitemapConfig(): array
    {
        $saved = $this->sitemap_config ?? [];

        return array_replace_recursive(self::DEFAULT_SITEMAP_CONFIG, $saved);
    }
}
