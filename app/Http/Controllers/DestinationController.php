<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Support\SiteTenant;
use Illuminate\Http\Request;

// Dedicated destination landing page — hero + a taste of that destination's
// packages/hotels/activities, each section linking out to the existing full
// listing (packages.byDestination / hotels.index / activities.index) rather
// than duplicating those grids here.
class DestinationController extends Controller
{
    // Full destinations directory (India + International) linked from the
    // homepage "Trending Destinations" section's "View all" link.
    public function index(Request $request)
    {
        $local = in_array($request->get('local'), ['domestic', 'international']) ? $request->get('local') : null;

        $destinations = Destination::forSite()
            ->where('status', 'active')
            ->when($local, fn ($q) => $q->where('local', $local))
            ->withCount(['holidayPackages as packages_count' => fn ($q) => $q->where('status', 'active')])
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('destinations.index', compact('destinations', 'local'));
    }

    public function show(Destination $destination)
    {
        abort_unless($destination->status === 'active' && $destination->unique_id === SiteTenant::id(), 404);

        $packages = $destination->holidayPackages()
            ->where('status', 'active')
            ->with(['destination', 'photos', 'categories', 'hotels', 'inclusionFeatures', 'customInclusions', 'reviews'])
            ->orderByDesc('is_best_seller')
            ->limit(6)
            ->get();

        $hotels = $destination->hotels()
            ->where('status', 'active')
            ->with(['destination', 'amenities', 'roomTypes'])
            ->orderByDesc('rating_score')
            ->limit(6)
            ->get();

        $activities = $destination->activities()
            ->where('status', 'active')
            ->with('activityCategory')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        return view('destinations.show', compact('destination', 'packages', 'hotels', 'activities'));
    }
}
