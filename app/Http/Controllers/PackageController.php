<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\HolidayPackage;
use App\Models\ListingPageSeo;
use App\Models\TravelCategory;
use App\Support\SiteTenant;
use Closure;
use Illuminate\Http\Request;

// Public holiday-package listings (India / International / MICE / all) and the
// shared detail page. All four listings reuse the same view + card design —
// only the underlying filter differs — per the "reuse one design" approach.
class PackageController extends Controller
{
    public function india(Request $request)
    {
        return $this->destinationGrid('domestic', 'India Packages', "Explore India's most sought-after travel destinations", ListingPageSeo::forSitePage('india-packages'));
    }

    public function international(Request $request)
    {
        return $this->destinationGrid('international', 'International Packages', "Explore the world's most sought-after travel destinations", ListingPageSeo::forSitePage('international-packages'));
    }

    public function byDestination(Request $request, Destination $destination)
    {
        abort_unless($destination->status === 'active', 404);

        return $this->listing(
            $request,
            $destination->name.' Packages',
            "Handpicked holiday packages in {$destination->name}",
            fn ($query) => $query->whereHas('destination', fn ($d) => $d->where('local', $destination->local)),
            $destination->local,
            $destination,
            $destination->packagesSeo()
        );
    }

    public function mice(Request $request)
    {
        return $this->listing($request, 'MICE', 'Meetings, Incentives, Conferences & Exhibitions — corporate travel solutions',
            fn ($query) => $query->whereHas('categories', fn ($c) => $c->where('slug', 'mice')));
    }

    public function all(Request $request)
    {
        return $this->listing($request, 'Holiday Packages', 'All our curated holiday packages in one place', fn ($query) => $query);
    }

    public function show(HolidayPackage $package)
    {
        abort_unless($package->status === 'active' && $package->unique_id === SiteTenant::id(), 404);

        $package->load([
            'destination', 'categories', 'activities', 'optionalActivities', 'inclusionFeatures', 'exclusionFeatures',
            'customInclusions', 'customExclusions', 'hotels.amenities', 'hotels.roomTypes', 'itineraries', 'photos', 'faqs', 'reviews',
        ]);

        $related = $package->displayRelatedPackages(4)
            ->load(['destination', 'photos', 'categories', 'hotels', 'inclusionFeatures', 'customInclusions', 'reviews']);

        return view('packages.show', compact('package', 'related'));
    }

    // Hero search's simplified "Budget per Person" brackets → a real price range,
    // only applied when the listing page's own price_min/price_max aren't set.
    private const BUDGET_BRACKETS = [
        'under_25k' => [null, 25000],
        '25k_50k' => [25000, 50000],
        '50k_1l' => [50000, 100000],
        '1l_2l' => [100000, 200000],
        '2l_plus' => [200000, null],
    ];

    private function listing(Request $request, string $heading, string $subheading, Closure $scope, ?string $local = null, ?Destination $scopedDestination = null, ?object $pageSeo = null)
    {
        $query = $scope(HolidayPackage::forSite()
            ->with(['destination', 'photos', 'categories', 'hotels', 'inclusionFeatures', 'customInclusions', 'reviews'])
            ->where('status', 'active'));

        if ($request->filled('category')) {
            $query->whereHas('categories', fn ($c) => $c->where('slug', $request->get('category')));
        }

        // Destination: prefer the autocomplete-selected id, fall back to a free-text
        // name/city match, then to the destination this page is scoped to (if any) —
        // so a per-destination listing page defaults to its own destination but the
        // sidebar dropdown can still switch away from it.
        if ($request->filled('destination_id')) {
            $query->where('destination_id', $request->get('destination_id'));
        } elseif ($request->filled('destination')) {
            $term = $request->get('destination');
            $query->whereHas('destination', fn ($d) => $d->where('name', 'like', "%{$term}%")->orWhere('city', 'like', "%{$term}%"));
        } elseif ($scopedDestination) {
            $query->where('destination_id', $scopedDestination->id);
        }

        if ($request->filled('duration')) {
            match ($request->get('duration')) {
                '1-3' => $query->whereBetween('nights', [1, 3]),
                '4-6' => $query->whereBetween('nights', [4, 6]),
                '7+' => $query->where('nights', '>=', 7),
                default => null,
            };
        }

        [$budgetMin, $budgetMax] = self::BUDGET_BRACKETS[$request->get('budget')] ?? [null, null];
        $priceMin = $request->get('price_min', $budgetMin);
        $priceMax = $request->get('price_max', $budgetMax);
        if ($priceMin) {
            $query->where('price', '>=', $priceMin);
        }
        if ($priceMax) {
            $query->where('price', '<=', $priceMax);
        }

        if ($request->filled('hotel_category')) {
            $query->where('hotel_category', $request->get('hotel_category'));
        }

        match ($request->get('sort')) {
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'duration' => $query->orderByDesc('nights'),
            default => $query->orderByDesc('is_best_seller')->orderBy('sort_order'),
        };

        $packages = $query->paginate(9)->withQueryString();
        $categories = TravelCategory::where('status', 'active')->orderBy('sort_order')->get();
        $destinations = Destination::forSite()->where('status', 'active')
            ->when($local, fn ($q) => $q->where('local', $local))
            ->orderBy('name')->get();
        $hotelCategories = HolidayPackage::forSite()->whereNotNull('hotel_category')->distinct()->orderBy('hotel_category')->pluck('hotel_category');

        return view('packages.index', compact(
            'packages', 'heading', 'subheading', 'categories', 'destinations', 'hotelCategories',
            'local', 'scopedDestination', 'pageSeo'
        ));
    }

    // Destination grid shown on the India/International landing pages — one
    // card per destination with an aggregated active-package count.
    private function destinationGrid(string $local, string $heading, string $subheading, ?ListingPageSeo $pageSeo = null)
    {
        $destinations = Destination::forSite()
            ->where('status', 'active')
            ->where('local', $local)
            ->whereHas('holidayPackages', fn ($q) => $q->where('status', 'active'))
            ->withCount(['holidayPackages as packages_count' => fn ($q) => $q->where('status', 'active')])
            ->withMin(['holidayPackages as cheapest_price' => fn ($q) => $q->where('status', 'active')], 'price')
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->get();

        return view('packages.destinations', compact('destinations', 'heading', 'subheading', 'pageSeo'));
    }
}
