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
use Illuminate\Support\Collection;

class HolidayPackage extends Model
{
    use HasFactory, BelongsToTenant, ForSiteTenant;

    protected $fillable = [
        'destination_id',
        'title',
        'slug',
        'nights',
        'days',
        'hotel_category',
        'hotel_category_overridden',
        'meals',
        'meals_overridden',
        'language',
        'places_to_visit',
        'overview',
        'seo_content',
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
        'price',
        'discounted_price',
        'booking_type',
        'status',
        'featured',
        'is_best_seller',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'featured'                   => 'boolean',
            'is_best_seller'             => 'boolean',
            'robots_index'               => 'boolean',
            'robots_follow'              => 'boolean',
            'hotel_category_overridden'  => 'boolean',
            'meals_overridden'           => 'boolean',
        ];
    }

    private const MEAL_KEYS = ['breakfast', 'lunch', 'dinner'];

    // Recomputes nights/days/hotel_category/meals from the itinerary and attached
    // hotels, then saves. This is the single place that logic lives — call it after
    // itinerary/hotel rows are synced (admin form save, bulk import) rather than from
    // a model event, since those child rows are written in later steps of the same
    // request/import and wouldn't exist yet if this ran on `saving`.
    public function applyDerivedFields(): void
    {
        $this->load(['itineraries', 'hotels']);

        $dayCount = $this->itineraries->count();
        $this->days = $dayCount;
        $this->nights = $dayCount > 0 ? $dayCount - 1 : 0;

        if (!$this->hotel_category_overridden) {
            $this->hotel_category = $this->deriveHotelCategory();
        }

        if (!$this->meals_overridden) {
            $this->meals = $this->deriveMeals();
        }

        $this->save();
    }

    // Star ratings of every attached hotel, formatted as a range. Property-type
    // annotations an admin might want (e.g. "+ Houseboat") aren't derivable from
    // star rating alone — that's what the override exists for.
    public function deriveHotelCategory(): ?string
    {
        $stars = $this->hotels->pluck('star_rating')
            ->filter(fn ($rating) => $rating > 0)
            ->unique()
            ->sort()
            ->values();

        if ($stars->isEmpty()) {
            return null;
        }
        if ($stars->count() === 1) {
            return $stars->first().' Star Hotels';
        }
        if ($stars->count() === 2) {
            return $stars->first().' & '.$stars->last().' Star Hotels';
        }

        return $stars->first().' to '.$stars->last().' Star Hotels';
    }

    // Day-by-day meal_tags on the itinerary, collapsed to a short phrase. Reads coarse
    // ("Daily Breakfast + 1 Dinner") when breakfast covers every day, since that's how
    // almost every package's meals actually look; falls back to explicit per-meal
    // counts only when breakfast isn't on every day.
    public function deriveMeals(): ?string
    {
        $dayCount = $this->itineraries->count();
        $counts = array_fill_keys(self::MEAL_KEYS, 0);

        foreach ($this->itineraries as $day) {
            foreach ((array) ($day->meal_tags ?? []) as $tag) {
                if (isset($counts[$tag])) {
                    $counts[$tag]++;
                }
            }
        }

        if ($dayCount === 0 || array_sum($counts) === 0) {
            return null;
        }

        $extras = [];
        foreach (['lunch', 'dinner'] as $meal) {
            if ($counts[$meal] > 0) {
                $extras[] = $counts[$meal].' '.ucfirst($meal).($counts[$meal] > 1 ? 's' : '');
            }
        }

        // A day-1 arrival with no breakfast tag (guests land and go straight to dinner)
        // doesn't break "daily" breakfast — every other day still needs it counted to say so.
        $firstDayHasBreakfast = in_array('breakfast', (array) ($this->itineraries->first()->meal_tags ?? []), true);
        $expectedBreakfastDays = $firstDayHasBreakfast ? $dayCount : $dayCount - 1;

        if ($expectedBreakfastDays > 0 && $counts['breakfast'] === $expectedBreakfastDays) {
            if (empty($extras)) {
                return 'Daily Breakfast';
            }
            if (count($extras) === 1) {
                return 'Daily Breakfast + '.$extras[0];
            }
            $last = array_pop($extras);

            return 'Daily Breakfast, '.implode(', ', $extras).' & '.$last;
        }

        $parts = [];
        if ($counts['breakfast'] > 0) {
            $parts[] = $counts['breakfast'].' Breakfast'.($counts['breakfast'] > 1 ? 's' : '');
        }
        $parts = array_merge($parts, $extras);

        return $parts ? implode(', ', $parts) : null;
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    // Multi-select badges (Honeymoon, Beach, Best Seller, ...) — replaces the old single travel_category_id FK.
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(TravelCategory::class, 'holiday_package_travel_category');
    }

    // Overview highlight chips (Sunrise Beach Walk, Water Sports, ...) — price-less, purely descriptive.
    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'holiday_package_activity');
    }

    // Paid optional add-ons offered at booking time. Separate from activities() above —
    // a highlight chip and a bookable add-on are different concepts even when they point
    // at the same Activity master row.
    public function optionalActivities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'package_activity')
            ->withPivot('id', 'price', 'is_optional', 'sort_order', 'note', 'day_number')
            ->orderBy('package_activity.sort_order');
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(PackageFeature::class, 'holiday_package_feature');
    }

    public function inclusionFeatures(): BelongsToMany
    {
        return $this->features()->where('type', 'inclusion');
    }

    public function exclusionFeatures(): BelongsToMany
    {
        return $this->features()->where('type', 'exclusion');
    }

    public function customFeatures(): HasMany
    {
        return $this->hasMany(HolidayPackageCustomFeature::class)->orderBy('sort_order');
    }

    public function customInclusions(): HasMany
    {
        return $this->customFeatures()->where('type', 'inclusion');
    }

    public function customExclusions(): HasMany
    {
        return $this->customFeatures()->where('type', 'exclusion');
    }

    // Every attached hotel — regardless of its optional/priced state. Some rows may
    // simply be "included, no charge" (is_optional false, price null); pricing/booking
    // logic filters on the pivot's is_optional flag rather than a separate relation,
    // since (per product decision) this single pivot carries both the display and the
    // bookable-add-on concept for hotels — unlike Activities, which splits the two.
    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'holiday_package_hotel')
            ->withPivot('id', 'room_type_id', 'price', 'is_optional', 'nights', 'note', 'sort_order', 'day_number')
            ->orderBy('holiday_package_hotel.sort_order');
    }

    public function itineraries(): HasMany
    {
        return $this->hasMany(PackageItinerary::class)->orderBy('day_number');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PackagePhoto::class)->orderBy('sort_order');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(PackageFaq::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(PackageReview::class)->orderBy('sort_order');
    }

    public function departureCities(): HasMany
    {
        return $this->hasMany(PackageDepartureCity::class)->orderBy('sort_order');
    }

    public function roomTypes(): HasMany
    {
        return $this->hasMany(PackageRoomType::class)->orderBy('sort_order');
    }

    // Admin-curated "You Might Also Like" picks.
    public function relatedPackages(): BelongsToMany
    {
        return $this->belongsToMany(
            HolidayPackage::class,
            'holiday_package_related',
            'holiday_package_id',
            'related_holiday_package_id'
        )->orderBy('holiday_package_related.sort_order');
    }

    // Falls back to auto-suggesting other active packages sharing a category when
    // no explicit picks exist, so "You Might Also Like" is never empty.
    public function displayRelatedPackages(int $limit = 4): Collection
    {
        $explicit = $this->relatedPackages()->where('status', 'active')->get();
        if ($explicit->isNotEmpty()) {
            return $explicit;
        }

        $categoryIds = $this->categories()->pluck('travel_categories.id');
        if ($categoryIds->isEmpty()) {
            return collect();
        }

        return static::where('id', '!=', $this->id)
            ->where('status', 'active')
            ->whereHas('categories', fn ($query) => $query->whereIn('travel_categories.id', $categoryIds))
            ->limit($limit)
            ->get();
    }

    protected function savingsPercent(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->discounted_price || !$this->price || $this->price <= 0) {
                return null;
            }

            return (int) round((($this->price - $this->discounted_price) / $this->price) * 100);
        });
    }

    protected function averageRating(): Attribute
    {
        return Attribute::get(fn () => round((float) $this->reviews()->avg('rating'), 1) ?: null);
    }

    // Category-wise rating bars shown next to the overall average (Hotels/Sightseeing/Food/Value).
    protected function categoryRatingBars(): Attribute
    {
        return Attribute::get(function () {
            $averages = $this->reviews()->reorder()->selectRaw(
                'avg(hotels_rating) as hotels, avg(sightseeing_rating) as sightseeing, avg(food_rating) as food, avg(value_rating) as value'
            )->first();

            return [
                'hotels'      => $averages?->hotels ? round((float) $averages->hotels, 1) : null,
                'sightseeing' => $averages?->sightseeing ? round((float) $averages->sightseeing, 1) : null,
                'food'        => $averages?->food ? round((float) $averages->food, 1) : null,
                'value'       => $averages?->value ? round((float) $averages->value, 1) : null,
            ];
        });
    }
}
