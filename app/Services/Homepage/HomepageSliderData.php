<?php

namespace App\Services\Homepage;

use App\Models\Activity;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\HolidayPackage;
use Illuminate\Support\Collection;

// Live queries backing the 4 data-driven homepage sliders (domestic/international
// packages, top activities, handpicked hotels). Kept separate from the models
// themselves since these eager-loads/ordering are homepage-presentation-specific,
// not general-purpose query concerns shared by the public listing controllers.
class HomepageSliderData
{
    public static function domesticPackages(int $limit = 10): Collection
    {
        return static::packagesByLocal('domestic', $limit);
    }

    public static function internationalPackages(int $limit = 10): Collection
    {
        return static::packagesByLocal('international', $limit);
    }

    private static function packagesByLocal(string $local, int $limit): Collection
    {
        return HolidayPackage::forSite()
            ->where('status', 'active')
            ->whereHas('destination', fn ($q) => $q->where('local', $local))
            ->with(['destination', 'photos', 'categories', 'hotels', 'inclusionFeatures', 'customInclusions', 'reviews'])
            ->orderByDesc('featured')
            ->orderByDesc('is_best_seller')
            ->latest()
            ->take($limit)
            ->get();
    }

    public static function topActivities(int $limit = 10): Collection
    {
        return Activity::forSite()
            ->where('status', 'active')
            ->with(['destination', 'activityCategory'])
            ->orderBy('sort_order')
            ->latest()
            ->take($limit)
            ->get();
    }

    public static function handpickedHotels(int $limit = 10): Collection
    {
        return Hotel::forSite()
            ->where('status', 'active')
            ->with(['destination', 'amenities', 'roomTypes'])
            ->orderBy('sort_order')
            ->latest()
            ->take($limit)
            ->get();
    }

    // Mixes domestic and international destinations (roughly half each) so the
    // homepage "Trending Destinations" tile grid always shows both, rather than
    // one local dominating the take($limit) cut when ordered together.
    public static function trendingDestinations(int $limit = 10): Collection
    {
        $domestic = static::destinationsByLocal('domestic', (int) ceil($limit / 2));
        $international = static::destinationsByLocal('international', $limit - $domestic->count());

        return $domestic->concat($international)->take($limit)->values();
    }

    private static function destinationsByLocal(string $local, int $limit): Collection
    {
        if ($limit <= 0) {
            return collect();
        }

        return Destination::forSite()
            ->where('status', 'active')
            ->where('local', $local)
            ->withCount(['holidayPackages as packages_count' => fn ($q) => $q->where('status', 'active')])
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->take($limit)
            ->get();
    }
}
