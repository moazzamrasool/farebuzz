<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\ForSiteTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TravelCategory extends Model
{
    use HasFactory, BelongsToTenant, ForSiteTenant;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'badge_color',
        'status',
        'sort_order',
    ];

    // Destinations no longer link to a single TravelCategory — they use their own
    // 'local' (domestic/international) field instead.
    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    // Holiday Packages use categories as multi-select badges (Honeymoon, Best Seller, ...),
    // unlike Packages which keep a single-select travel_category_id FK.
    public function holidayPackages(): BelongsToMany
    {
        return $this->belongsToMany(HolidayPackage::class, 'holiday_package_travel_category');
    }
}
