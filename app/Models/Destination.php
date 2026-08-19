<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\ForSiteTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destination extends Model
{
    use HasFactory, BelongsToTenant, ForSiteTenant;

    protected $fillable = [
        'name',
        'slug',
        'local',
        'country',
        'city',
        'description',
        'meta',
        'meta_title',
        'meta_description',
        'packages_meta_title',
        'packages_meta_description',
        'meta_keywords',
        'focus_keyword',
        'seo_content',
        'tags',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'robots_index',
        'robots_follow',
        'cover_image',
        'gallery_images',
        'status',
        'sort_order',
        'featured',
    ];

    protected function casts(): array
    {
        return [
            'gallery_images' => 'array',
            'featured'       => 'boolean',
            'robots_index'   => 'boolean',
            'robots_follow'  => 'boolean',
        ];
    }

    // meta_title/meta_description above back /destinations/{slug}; this backs the
    // distinct /destinations/{slug}/packages listing (PackageController::byDestination).
    // A plain object (not this model) so SeoResolver::resolve() reads packages_meta_*
    // through the standard meta_title/meta_description property names without the two
    // pages' SEO ever colliding.
    public function packagesSeo(): object
    {
        return (object) [
            'meta_title' => $this->packages_meta_title,
            'meta_description' => $this->packages_meta_description,
            'meta_keywords' => null,
            'canonical_url' => null,
            'og_title' => null,
            'og_description' => null,
            'og_image' => null,
            'robots_index' => null,
            'robots_follow' => null,
        ];
    }

    public function holidayPackages(): HasMany
    {
        return $this->hasMany(HolidayPackage::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }
}
