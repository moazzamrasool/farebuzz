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
        'meta_keywords',
        'focus_keyword',
        'seo_content',
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
