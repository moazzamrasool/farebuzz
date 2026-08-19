<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\ForSiteTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    use HasFactory, BelongsToTenant, ForSiteTenant;

    protected $fillable = [
        'destination_id',
        'travel_category_id',
        'name',
        'slug',
        'category',
        'description',
        'duration',
        'price',
        'image',
        'status',
        'sort_order',
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
            'robots_index'  => 'boolean',
            'robots_follow' => 'boolean',
        ];
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function travelCategory(): BelongsTo
    {
        return $this->belongsTo(TravelCategory::class);
    }

    public function holidayPackages(): BelongsToMany
    {
        return $this->belongsToMany(HolidayPackage::class, 'holiday_package_activity');
    }

    // Paid optional add-ons — separate from the price-less "highlight" chips above.
    public function optionalOnPackages(): BelongsToMany
    {
        return $this->belongsToMany(HolidayPackage::class, 'package_activity')
            ->withPivot('id', 'price', 'is_optional', 'sort_order', 'note');
    }
}
