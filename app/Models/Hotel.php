<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\ForSiteTenant;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasFactory, BelongsToTenant, ForSiteTenant;

    protected $fillable = [
        'destination_id',
        'name',
        'slug',
        'star_rating',
        'property_type',
        'address',
        'latitude',
        'longitude',
        'check_in_time',
        'check_out_time',
        'property_rules',
        'description',
        'rating_score',
        'review_count',
        'cover_image',
        'gallery_images',
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
            'gallery_images' => 'array',
            'robots_index'   => 'boolean',
            'robots_follow'  => 'boolean',
        ];
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'hotel_amenity');
    }

    public function roomTypes(): HasMany
    {
        return $this->hasMany(HotelRoomType::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(HotelReview::class)->orderBy('sort_order');
    }

    public function holidayPackages(): BelongsToMany
    {
        return $this->belongsToMany(HolidayPackage::class, 'holiday_package_hotel')
            ->withPivot('id', 'room_type_id', 'price', 'is_optional', 'nights', 'note', 'sort_order');
    }

    protected function averageRating(): Attribute
    {
        return Attribute::get(fn () => round((float) $this->reviews()->avg('rating'), 1) ?: null);
    }

    // Category-wise rating bars shown next to the overall average, mirroring
    // HolidayPackage::categoryRatingBars().
    protected function categoryRatingBars(): Attribute
    {
        return Attribute::get(function () {
            $averages = $this->reviews()->reorder()->selectRaw(
                'avg(location_rating) as location, avg(cleanliness_rating) as cleanliness, avg(service_rating) as service, avg(value_rating) as value'
            )->first();

            return [
                'location'    => $averages?->location ? round((float) $averages->location, 1) : null,
                'cleanliness' => $averages?->cleanliness ? round((float) $averages->cleanliness, 1) : null,
                'service'     => $averages?->service ? round((float) $averages->service, 1) : null,
                'value'       => $averages?->value ? round((float) $averages->value, 1) : null,
            ];
        });
    }

    // Cheapest active room's per-night sell price — used by listing cards for
    // "starting from ₹X / night" without needing an eager-loaded relation.
    protected function fromPrice(): Attribute
    {
        return Attribute::get(function () {
            $room = $this->relationLoaded('roomTypes')
                ? $this->roomTypes->sortBy('sellPrice')->first()
                : $this->roomTypes()->orderByRaw('COALESCE(discounted_price, price) asc')->first();

            return $room?->sellPrice;
        });
    }
}
