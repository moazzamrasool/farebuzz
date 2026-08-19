<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\ListingPageSeo;
use App\Support\SiteTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HotelController extends Controller
{
    // The homepage hero's "Hotels" search tab (resources/views/homepage/_hero.blade.php,
    // #panel-hotels) already posts straight to this route with these exact param names —
    // so both that form and this controller must keep speaking the same query-string
    // "dialect". A plain, filter-less visit renders the browse/landing experience;
    // any of these being present means the visitor is searching, so show results instead.
    public function index(Request $request)
    {
        $isSearch = $request->filled('destination') || $request->filled('destination_id')
            || $request->filled('checkin') || $request->filled('checkout')
            || $request->filled('star_rating') || $request->filled('amenities')
            || $request->filled('price_min') || $request->filled('price_max') || $request->filled('rating_min')
            || $request->filled('property_type');

        $pageSeo = ListingPageSeo::forSitePage('hotels');

        return $isSearch ? $this->results($request, $pageSeo) : $this->landing($request, $pageSeo);
    }

    private function landing(Request $request, ?ListingPageSeo $pageSeo = null)
    {
        $destinations = Destination::forSite()->where('status', 'active')
            ->whereHas('hotels', fn ($q) => $q->where('status', 'active'))
            ->withCount(['hotels as hotels_count' => fn ($q) => $q->where('status', 'active')])
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->get();

        $featuredDestination = $destinations->first();

        $stayHotels = Hotel::forSite()->where('status', 'active')
            ->when($featuredDestination, fn ($q) => $q->where('destination_id', $featuredDestination->id))
            ->with('roomTypes')
            ->orderByDesc('rating_score')
            ->limit(4)
            ->get();

        return view('hotels.landing', compact('destinations', 'featuredDestination', 'stayHotels', 'pageSeo'));
    }

    private function results(Request $request, ?ListingPageSeo $pageSeo = null)
    {
        $query = Hotel::forSite()->where('status', 'active')->with('amenities', 'roomTypes', 'destination');

        if ($request->filled('destination_id')) {
            $query->where('destination_id', $request->get('destination_id'));
        } elseif ($request->filled('destination')) {
            $term = $request->get('destination');
            $query->where(fn ($q) => $q->where('address', 'like', "%{$term}%")
                ->orWhere('name', 'like', "%{$term}%")
                ->orWhereHas('destination', fn ($d) => $d->where('name', 'like', "%{$term}%")->orWhere('city', 'like', "%{$term}%")));
        }

        if ($request->filled('star_rating')) {
            $query->whereIn('star_rating', (array) $request->get('star_rating'));
        }

        if ($request->filled('property_type')) {
            $query->where('property_type', $request->get('property_type'));
        }

        if ($request->filled('rating_min')) {
            $query->where('rating_score', '>=', (float) $request->get('rating_min'));
        }

        if ($request->filled('price_min')) {
            $min = (float) $request->get('price_min');
            $query->whereHas('roomTypes', fn ($q) => $q->whereRaw('COALESCE(discounted_price, price) >= ?', [$min]));
        }
        if ($request->filled('price_max')) {
            $max = (float) $request->get('price_max');
            $query->whereHas('roomTypes', fn ($q) => $q->whereRaw('COALESCE(discounted_price, price) <= ?', [$max]));
        }

        if ($request->filled('amenities')) {
            foreach ((array) $request->get('amenities') as $amenityId) {
                $query->whereHas('amenities', fn ($q) => $q->where('amenities.id', $amenityId));
            }
        }

        match ($request->get('sort')) {
            'price_low' => $query->orderByRaw('(select min(coalesce(discounted_price, price)) from hotel_room_types where hotel_room_types.hotel_id = hotels.id) asc'),
            'price_high' => $query->orderByRaw('(select min(coalesce(discounted_price, price)) from hotel_room_types where hotel_room_types.hotel_id = hotels.id) desc'),
            'popularity' => $query->orderByDesc('review_count'),
            default => $query->orderByDesc('rating_score')->orderBy('sort_order'),
        };

        $hotels = $query->paginate(9)->withQueryString();
        $amenities = Amenity::where('status', 'active')->orderBy('sort_order')->get();
        $destinations = Destination::forSite()->where('status', 'active')->orderBy('name')->get();

        [$checkIn, $checkOut, $nights] = $this->resolveStay($request);
        $adults = max(1, (int) $request->get('adults', $this->guestsFromRoomsParam($request)));
        $rooms = max(1, (int) $request->get('rooms_count', $this->roomsFromRoomsParam($request)));

        return view('hotels.index', compact('hotels', 'amenities', 'destinations', 'checkIn', 'checkOut', 'nights', 'adults', 'rooms', 'pageSeo'));
    }

    public function show(Request $request, Hotel $hotel)
    {
        abort_unless($hotel->status === 'active' && $hotel->unique_id === SiteTenant::id(), 404);

        $hotel->load([
            'amenities',
            'destination',
            'roomTypes',
            'reviews' => fn ($q) => $q->orderByDesc('review_date'),
            'holidayPackages' => fn ($q) => $q->where('status', 'active'),
            'holidayPackages.destination', 'holidayPackages.photos', 'holidayPackages.categories',
            'holidayPackages.hotels', 'holidayPackages.inclusionFeatures', 'holidayPackages.customInclusions', 'holidayPackages.reviews',
        ]);

        [$checkIn, $checkOut, $nights] = $this->resolveStay($request);
        $adults = max(1, (int) $request->get('adults', $this->guestsFromRoomsParam($request)));
        $children = max(0, (int) $request->get('children', 0));
        $rooms = max(1, (int) $request->get('rooms_count', $this->roomsFromRoomsParam($request)));

        $similarHotels = Hotel::forSite()->where('status', 'active')
            ->where('id', '!=', $hotel->id)
            ->when($hotel->destination_id, fn ($q) => $q->where('destination_id', $hotel->destination_id))
            ->with('roomTypes')
            ->limit(4)
            ->get();

        return view('hotels.show', compact('hotel', 'checkIn', 'checkOut', 'nights', 'adults', 'children', 'rooms', 'similarHotels'));
    }

    // Resolves check-in/out from the request (defaulting to today/tomorrow) and the
    // number of nights between them — the one thing every room price display and the
    // booking form itself all derive from, so it lives in one place. Never trusted for
    // the actual charge (see BookingController::calculateHotelBreakdown), only display.
    private function resolveStay(Request $request): array
    {
        $checkIn = $request->filled('checkin')
            ? Carbon::parse($request->get('checkin'))
            : Carbon::today();
        $checkOut = $request->filled('checkout')
            ? Carbon::parse($request->get('checkout'))
            : $checkIn->copy()->addDay();

        if ($checkOut->lessThanOrEqualTo($checkIn)) {
            $checkOut = $checkIn->copy()->addDay();
        }

        $nights = max(1, $checkIn->diffInDays($checkOut));

        return [$checkIn, $checkOut, $nights];
    }

    // The homepage hero's Hotels tab bundles guests+rooms into one <select> value like
    // "4-2" (4 guests, 2 rooms) rather than separate fields — parsed here so both that
    // shorthand and this page's own explicit adults/rooms_count fields resolve the same way.
    private function guestsFromRoomsParam(Request $request): int
    {
        return (int) (explode('-', (string) $request->get('rooms', '2-1'))[0] ?? 2);
    }

    private function roomsFromRoomsParam(Request $request): int
    {
        return (int) (explode('-', (string) $request->get('rooms', '2-1'))[1] ?? 1);
    }
}
