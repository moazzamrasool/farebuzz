<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\ForSiteTenant;
use Illuminate\Database\Eloquent\Model;

// SEO for general (non-per-record) public listing pages — india-packages,
// international-packages, hotels, activities — one row per tenant per page_key.
// Mirrors HomepageSeo's field set; see database/migrations/*_create_listing_page_seo_table.
class ListingPageSeo extends Model
{
    use BelongsToTenant, ForSiteTenant;

    protected $table = 'listing_page_seo';

    // page_key => admin-facing label, also used to build the admin index screen.
    public const PAGES = [
        'india-packages'         => 'India Packages',
        'international-packages' => 'International Packages',
        'hotels'                 => 'Hotels',
        'activities'              => 'Activities',
    ];

    protected $fillable = [
        'unique_id',
        'page_key',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'focus_keyword',
        'tags',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'robots_index',
        'robots_follow',
    ];

    protected function casts(): array
    {
        return [
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
        ];
    }

    // Admin screens: current tenant's row for this page, created on first edit.
    // Mirrors SeoSetting::forCurrentTenant() (relies on the admin-guard tenant scope).
    public static function forCurrentTenantPage(string $pageKey): self
    {
        return static::query()->firstOrCreate(['page_key' => $pageKey]);
    }

    // Public frontend: this tenant's row for this page, or null if never configured
    // (SeoResolver::resolve() already handles a null $seo with sensible fallbacks).
    public static function forSitePage(string $pageKey): ?self
    {
        return static::forSite()->where('page_key', $pageKey)->first();
    }
}
