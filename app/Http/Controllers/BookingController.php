<?php

namespace App\Http\Controllers;

use App\Mail\BookingAdminMail;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\HolidayPackage;
use App\Models\Hotel;
use App\Models\HotelRoomType;
use App\Models\PackageRoomType;
use App\Services\CouponService;
use App\Services\PayUService;
use App\Support\SiteTenant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

// Public "Book Now" flow — form, price calculation, PayU handoff, and the two
// on-screen outcome pages. All PayU-specific logic lives in PayUService.
class BookingController extends Controller
{
    public function __construct(private PayUService $payU, private CouponService $coupons)
    {
    }

    public function form(HolidayPackage $package)
    {
        $this->assertBookable($package);
        $package->load(['departureCities', 'roomTypes', 'optionalActivities', 'hotels.roomTypes']);

        return view('bookings.form', compact('package'));
    }

    // "Apply Coupon" preview — re-derives the same subtotal store() would from the posted
    // selections (never trusts a client-supplied amount) and runs it through CouponService.
    // Nothing is persisted here; store() re-validates the code again before it's ever
    // allowed to affect a real booking's total_amount.
    public function applyCoupon(Request $request, HolidayPackage $package)
    {
        $this->assertBookable($package);

        $data = $request->validate([
            'code'           => 'required|string|max:50',
            'room_type_id'   => 'nullable|exists:package_room_types,id',
            'adults'         => 'required|integer|min:1|max:20',
            'children'       => 'nullable|integer|min:0|max:20',
            'activity_ids'   => 'nullable|array',
            'activity_ids.*' => 'integer',
            'hotel_ids'      => 'nullable|array',
            'hotel_ids.*'    => 'integer',
        ]);

        $roomType = !empty($data['room_type_id']) ? $package->roomTypes()->find($data['room_type_id']) : null;
        $selectedActivities = $package->optionalActivities()
            ->wherePivot('is_optional', true)
            ->whereIn('activities.id', $data['activity_ids'] ?? [])
            ->get();
        $selectedHotels = $package->hotels()
            ->wherePivot('is_optional', true)
            ->whereIn('hotels.id', $data['hotel_ids'] ?? [])
            ->with('roomTypes')
            ->get()
            ->unique(fn ($hotel) => $hotel->pivot->day_number ?? 'hotel-'.$hotel->id);

        $breakdown = $this->calculateBreakdown($package, $roomType, $data['adults'], $data['children'] ?? 0, $selectedActivities, $selectedHotels);
        $subtotal = $breakdown['base_fare'] - $breakdown['discount_amount'] + $breakdown['activities_total'] + $breakdown['hotels_total'];

        return $this->couponResponse($data['code'], 'package', $package->id, $subtotal);
    }

    public function store(Request $request, HolidayPackage $package)
    {
        $this->assertBookable($package);

        $data = $request->validate([
            'departure_city'    => 'nullable|string|max:255',
            'travel_date'       => 'required|date|after_or_equal:today',
            'room_type_id'      => 'nullable|exists:package_room_types,id',
            'adults'            => 'required|integer|min:1|max:20',
            'children'          => 'nullable|integer|min:0|max:20',
            'traveller_name'    => 'required|string|max:255',
            'traveller_email'   => 'required|email|max:255',
            'traveller_phone'   => 'required|string|max:20',
            'traveller_address' => 'required|string|max:1000',
            'special_requests'  => 'nullable|string|max:1000',
            'gst_number'        => 'nullable|string|max:20',
            'activity_ids'      => 'nullable|array',
            'activity_ids.*'    => 'integer',
            'hotel_ids'         => 'nullable|array',
            'hotel_ids.*'       => 'integer',
            'coupon_code'       => 'nullable|string|max:50',
        ]);

        $roomType = !empty($data['room_type_id'])
            ? $package->roomTypes()->find($data['room_type_id'])
            : null;

        // Ids are the only thing trusted from the client — prices always come from this
        // package's own pivot. wherePivot('is_optional', true) also silently drops any id
        // for an "included" (non-bookable) activity, so it can never be charged for twice.
        $selectedActivities = $package->optionalActivities()
            ->wherePivot('is_optional', true)
            ->whereIn('activities.id', $data['activity_ids'] ?? [])
            ->get();

        // Same "never trust the client" rule as activities above — hotel ids are only
        // used to look up this package's own priced pivot rows; is_optional=false
        // ("Included") hotels are never selectable, so a tampered id can't be charged for
        // either. A day can only have one hotel, so if two selected hotels share the same
        // day_number, keep the first and silently drop the rest (hotels with no
        // day_number are each independent and never deduped against one another).
        $selectedHotels = $package->hotels()
            ->wherePivot('is_optional', true)
            ->whereIn('hotels.id', $data['hotel_ids'] ?? [])
            ->with('roomTypes')
            ->get()
            ->unique(fn ($hotel) => $hotel->pivot->day_number ?? 'hotel-'.$hotel->id);

        $travellers = max(1, $data['adults'] + ($data['children'] ?? 0));
        $breakdown = $this->calculateBreakdown($package, $roomType, $data['adults'], $data['children'] ?? 0, $selectedActivities, $selectedHotels);
        $travelDate = Carbon::parse($data['travel_date']);

        // Re-validate the coupon here even though the "Apply" step already checked it —
        // time may have passed and limits may have changed since then, and this is the
        // only check that ever actually touches total_amount (the number PayU is handed).
        // An invalid/expired code at this point never blocks checkout — it's simply not
        // applied, same as if the customer had never entered one.
        $appliedCoupon = null;
        if (!empty($data['coupon_code'])) {
            $subtotal = $breakdown['base_fare'] - $breakdown['discount_amount'] + $breakdown['activities_total'] + $breakdown['hotels_total'];
            $couponResult = $this->coupons->evaluate($data['coupon_code'], 'package', $package->id, $subtotal, Auth::id(), Auth::user()->email);

            if ($couponResult['valid']) {
                $appliedCoupon = $couponResult['coupon'];
                $breakdown = $this->coupons->applyToBreakdown($breakdown, $couponResult['discount']);
            }
        }

        $booking = DB::transaction(function () use ($data, $package, $roomType, $breakdown, $selectedActivities, $selectedHotels, $travellers, $travelDate, $appliedCoupon) {
            $booking = Booking::create(array_merge($data, $breakdown, [
                'holiday_package_id'          => $package->id,
                'user_id'                     => Auth::id(),
                'booking_reference'           => Booking::generateReference(),
                'package_title'               => $package->title,
                'package_slug'                => $package->slug,
                'room_type_name'              => $roomType->name ?? null,
                'room_type_price'             => $roomType?->sell_price,
                'room_type_original_price'    => $roomType->price ?? null,
                'room_type_discount_percent'  => $roomType->savings_percent ?? null,
                'children'                    => $data['children'] ?? 0,
                'coupon_id'                   => $appliedCoupon?->id,
                'coupon_code'                 => $appliedCoupon?->code,
                'coupon_discount_amount'      => $breakdown['coupon_discount_amount'] ?? 0,
                'status'                      => 'pending',
                'payment_status'              => 'pending',
            ]));

            foreach ($selectedActivities as $activity) {
                $unitPrice = (float) ($activity->pivot->price ?? $activity->price ?? 0);
                $dayNumber = $activity->pivot->day_number;
                $booking->activities()->create([
                    'unique_id'      => $booking->unique_id,
                    'activity_id'    => $activity->id,
                    'name'           => $activity->name,
                    'unit_price'     => $unitPrice,
                    'qty'            => $travellers,
                    'line_total'     => round($unitPrice * $travellers, 2),
                    'day_number'     => $dayNumber,
                    'activity_date'  => $dayNumber ? $travelDate->copy()->addDays($dayNumber - 1) : null,
                ]);
            }

            foreach ($selectedHotels as $hotel) {
                $hotelRoomType = $hotel->pivot->room_type_id
                    ? $hotel->roomTypes->firstWhere('id', $hotel->pivot->room_type_id)
                    : null;
                $unitPrice = (float) ($hotel->pivot->price ?? $hotelRoomType?->sell_price ?? 0);
                $nights = (int) ($hotel->pivot->nights ?? 1);
                $name = $hotel->name.($hotelRoomType ? ' — '.$hotelRoomType->name : '');
                $dayNumber = $hotel->pivot->day_number;

                $booking->hotels()->create([
                    'unique_id'  => $booking->unique_id,
                    'hotel_id'   => $hotel->id,
                    'name'       => $name,
                    'unit_price' => $unitPrice,
                    'nights'     => $nights,
                    'line_total' => round($unitPrice * $nights, 2),
                    'day_number' => $dayNumber,
                    'stay_date'  => $dayNumber ? $travelDate->copy()->addDays($dayNumber - 1) : null,
                ]);
            }

            return $booking;
        });

        return $this->redirectToPayU($booking);
    }

    // Booking a specific room in a specific hotel — independent of any holiday package.
    // Dates/guests/rooms arrive as query params (carried over from the hotel detail
    // page's "Change Dates & Guests" bar), same display-only role as everywhere else;
    // store() below re-derives everything server-side regardless of what's posted.
    public function hotelForm(Request $request, Hotel $hotel, HotelRoomType $roomType)
    {
        $this->assertHotelBookable($hotel, $roomType);

        [$checkIn, $checkOut, $nights] = $this->resolveHotelStay($request);
        $adults = max(1, (int) $request->get('adults', 2));
        $children = max(0, (int) $request->get('children', 0));
        $rooms = max(1, (int) $request->get('rooms_count', 1));

        return view('bookings.hotel_form', compact('hotel', 'roomType', 'checkIn', 'checkOut', 'nights', 'adults', 'children', 'rooms'));
    }

    // Same "recompute from posted ids, never trust a client amount" preview as
    // applyCoupon() above, keyed off a hotel + room type stay instead of a package.
    public function applyHotelCoupon(Request $request, Hotel $hotel, HotelRoomType $roomType)
    {
        $this->assertHotelBookable($hotel, $roomType);

        $data = $request->validate([
            'code'            => 'required|string|max:50',
            'check_in_date'   => 'required|date',
            'check_out_date'  => 'required|date|after:check_in_date',
            'rooms_count'     => 'required|integer|min:1|max:10',
        ]);

        $checkIn = Carbon::parse($data['check_in_date']);
        $checkOut = Carbon::parse($data['check_out_date']);
        $nights = max(1, $checkIn->diffInDays($checkOut));
        $rooms = (int) $data['rooms_count'];

        $breakdown = $this->calculateHotelBreakdown($roomType, $nights, $rooms);
        $subtotal = $breakdown['base_fare'] - $breakdown['discount_amount'] + $breakdown['activities_total'] + $breakdown['hotels_total'];

        return $this->couponResponse($data['code'], 'hotel', $hotel->id, $subtotal);
    }

    public function hotelStore(Request $request, Hotel $hotel, HotelRoomType $roomType)
    {
        $this->assertHotelBookable($hotel, $roomType);

        $data = $request->validate([
            'check_in_date'      => 'required|date|after_or_equal:today',
            'check_out_date'     => 'required|date|after:check_in_date',
            'rooms_count'        => 'required|integer|min:1|max:10',
            'adults'             => 'required|integer|min:1|max:20',
            'children'           => 'nullable|integer|min:0|max:20',
            'traveller_name'     => 'required|string|max:255',
            'traveller_email'    => 'required|email|max:255',
            'traveller_phone'    => 'required|string|max:20',
            'traveller_address'  => 'required|string|max:1000',
            'special_requests'   => 'nullable|string|max:1000',
            'gst_number'         => 'nullable|string|max:20',
            'coupon_code'        => 'nullable|string|max:50',
        ]);

        $checkIn = Carbon::parse($data['check_in_date']);
        $checkOut = Carbon::parse($data['check_out_date']);
        $nights = max(1, $checkIn->diffInDays($checkOut));
        $rooms = (int) $data['rooms_count'];

        $breakdown = $this->calculateHotelBreakdown($roomType, $nights, $rooms);

        $appliedCoupon = null;
        if (!empty($data['coupon_code'])) {
            $subtotal = $breakdown['base_fare'] - $breakdown['discount_amount'] + $breakdown['activities_total'] + $breakdown['hotels_total'];
            $couponResult = $this->coupons->evaluate($data['coupon_code'], 'hotel', $hotel->id, $subtotal, Auth::id(), Auth::user()->email);

            if ($couponResult['valid']) {
                $appliedCoupon = $couponResult['coupon'];
                $breakdown = $this->coupons->applyToBreakdown($breakdown, $couponResult['discount']);
            }
        }

        $booking = Booking::create(array_merge($data, $breakdown, [
            'holiday_package_id'         => null,
            'booking_type'               => 'hotel',
            'user_id'                    => Auth::id(),
            'booking_reference'          => Booking::generateReference(),
            'package_title'              => $hotel->name.' — '.$roomType->name,
            'package_slug'               => $hotel->slug,
            'travel_date'                => $checkIn,
            'coupon_id'                  => $appliedCoupon?->id,
            'coupon_code'                => $appliedCoupon?->code,
            'coupon_discount_amount'     => $breakdown['coupon_discount_amount'] ?? 0,
            'hotel_id'                   => $hotel->id,
            'hotel_room_type_id'         => $roomType->id,
            'check_in_date'              => $checkIn,
            'check_out_date'             => $checkOut,
            'nights'                     => $nights,
            'rooms'                      => $rooms,
            'room_type_name'             => $roomType->name,
            'room_type_price'            => $roomType->sellPrice,
            'room_type_original_price'   => $roomType->price,
            'room_type_discount_percent' => $roomType->price > 0
                ? (int) round((($roomType->price - $roomType->sellPrice) / $roomType->price) * 100)
                : null,
            'children'                   => $data['children'] ?? 0,
            'status'                     => 'pending',
            'payment_status'             => 'pending',
        ]));

        return $this->redirectToPayU($booking);
    }

    // Re-initiates payment for a booking whose earlier attempt failed or was abandoned.
    public function retry(Booking $booking)
    {
        $this->authorizeOwner($booking);
        abort_if($booking->payment_status === 'paid', 403);

        $booking->update(['status' => 'pending', 'payment_status' => 'pending']);

        return $this->redirectToPayU($booking);
    }

    // PayU posts back to these two from the customer's own browser, so no CSRF
    // token of ours is present (see bootstrap/app.php validateCsrfTokens except).
    public function paymentSuccess(Request $request)
    {
        return $this->handleCallback($request, expectingSuccess: true);
    }

    public function paymentFailure(Request $request)
    {
        return $this->handleCallback($request, expectingSuccess: false);
    }

    public function success(Request $request, Booking $booking)
    {
        $this->authorizeBookingAccess($request, $booking);
        abort_unless($booking->status === 'confirmed', 404);

        return view('bookings.success', compact('booking'));
    }

    public function failed(Request $request, Booking $booking)
    {
        $this->authorizeBookingAccess($request, $booking);

        return view('bookings.failed', compact('booking'));
    }

    // All trust decisions (hash verification, amount integrity, idempotency, never
    // downgrading a paid booking) live in PayUService::processCallback() — this method
    // only reacts to its verdict, so the success and failure routes can never disagree
    // about what's safe to do with unverified data.
    private function handleCallback(Request $request, bool $expectingSuccess)
    {
        $result = $this->payU->processCallback($request->all(), $expectingSuccess);

        abort_unless($result['booking'], 404);

        if ($result['status'] === 'confirmed') {
            $this->sendConfirmationEmails($result['booking']);
        }

        $routeName = in_array($result['status'], ['confirmed', 'already_confirmed'], true)
            ? 'bookings.success'
            : 'bookings.failed';

        return redirect()->to($this->signedBookingUrl($routeName, $result['booking']));
    }

    // PayU's redirect back to us is a cross-site POST, so SameSite=Lax drops the session
    // cookie on the way — a plain route() URL would then fail the auth check even though the
    // customer never actually logged out. A short-lived signature lets the outcome page prove
    // itself without depending on that cookie surviving the trip.
    private function signedBookingUrl(string $routeName, Booking $booking): string
    {
        return URL::temporarySignedRoute($routeName, now()->addMinutes(30), ['booking' => $booking->booking_reference]);
    }

    private function redirectToPayU(Booking $booking)
    {
        $result = $this->payU->initiate($booking);

        return view('bookings.payu_redirect', $result);
    }

    // Shared JSON shape for both applyCoupon() and applyHotelCoupon() — a 422 with a
    // message on failure, or the discount amount to show in the live summary on success.
    private function couponResponse(string $code, string $bookingType, int $itemId, float $subtotal)
    {
        $result = $this->coupons->evaluate($code, $bookingType, $itemId, $subtotal, Auth::id(), Auth::user()->email);

        if (!$result['valid']) {
            return response()->json(['valid' => false, 'message' => $result['message']], 422);
        }

        return response()->json([
            'valid' => true,
            'code' => $result['coupon']->code,
            'discount' => $result['discount'],
        ]);
    }

    private function sendConfirmationEmails(Booking $booking): void
    {
        // Queued, not sent inline — PDF generation + SMTP must never add latency (or a
        // hard failure) to the PayU callback request itself. A queue-worker outage still
        // fails safe: the booking is already confirmed in the DB, the jobs just wait.
        try {
            Mail::to($booking->traveller_email)->queue(new BookingConfirmationMail($booking));
            Mail::to(config('mail.admin_address'))->queue(new BookingAdminMail($booking));
        } catch (\Throwable $e) {
            Log::error('Booking confirmation email failed to queue: '.$e->getMessage(), ['booking_id' => $booking->id]);
        }
    }

    private function calculateBreakdown(HolidayPackage $package, ?PackageRoomType $roomType, int $adults, int $children, ?Collection $selectedActivities = null, ?Collection $selectedHotels = null): array
    {
        $travellers = max(1, $adults + $children);

        // A selected room type is its own source of truth for both the reference (original)
        // price and the actual sell price — it no longer borrows the package's MRP. Only
        // packages with no room type selected fall back to package-level pricing.
        $mrpPerPerson = (float) ($roomType?->price ?? $package->price);
        $sellPerPerson = (float) ($roomType?->sell_price ?? $package->discounted_price ?? $package->price);

        // "Base Fare" is a reference/strike-through price, not what's charged — so it must
        // never be allowed to sit BELOW the actual per-person sell price. A room type (e.g.
        // a Suite) can be priced above the package's own MRP, in which case there's no
        // discount at all and the reference price is simply the room's own price; using the
        // package MRP unconditionally here previously undercharged for premium room types.
        $referencePerPerson = max($mrpPerPerson, $sellPerPerson);

        $baseFare = round($referencePerPerson * $travellers, 2);
        $discount = round(max(0, $referencePerPerson - $sellPerPerson) * $travellers, 2);

        // Same per-person × travellers basis as the room type, priced off each activity's
        // pivot override (falling back to its own master price) — never the client.
        $activitiesTotal = 0.0;
        foreach ($selectedActivities ?? [] as $activity) {
            $activitiesTotal += (float) ($activity->pivot->price ?? $activity->price ?? 0) * $travellers;
        }
        $activitiesTotal = round($activitiesTotal, 2);

        // Flat stay cost (price × nights) per selected hotel — unlike activities, never
        // multiplied by travellers, since a hotel room rate isn't charged per person.
        // Summed across every day's selected hotel (deduping same-day picks already
        // happened in store() before this is called).
        $hotelsTotal = 0.0;
        foreach ($selectedHotels ?? [] as $hotel) {
            $hotelRoomType = $hotel->pivot->room_type_id
                ? $hotel->roomTypes->firstWhere('id', $hotel->pivot->room_type_id)
                : null;
            $hotelUnitPrice = (float) ($hotel->pivot->price ?? $hotelRoomType?->sell_price ?? 0);
            $hotelNights = (int) ($hotel->pivot->nights ?? 1);
            $hotelsTotal += $hotelUnitPrice * $hotelNights;
        }
        $hotelsTotal = round($hotelsTotal, 2);

        $subtotal = $baseFare - $discount + $activitiesTotal + $hotelsTotal;
        $taxesFee = round($subtotal * 0.05, 2); // GST-style 5% service tax on the discounted subtotal + add-ons
        $total = round($subtotal + $taxesFee, 2);

        return [
            'base_fare'        => $baseFare,
            'discount_amount'  => $discount,
            'activities_total' => $activitiesTotal,
            'hotels_total'     => $hotelsTotal,
            'taxes_fee'        => $taxesFee,
            'total_amount'     => $total,
        ];
    }

    private function assertBookable(HolidayPackage $package): void
    {
        abort_unless(
            $package->status === 'active'
                && $package->unique_id === SiteTenant::id()
                && $package->booking_type === 'book_enquiry',
            404
        );
    }

    // Room price × nights × rooms is the entire hotel pricing model — no per-person
    // multiplier (unlike packages), since a room rate isn't charged per guest. The
    // room type is always re-fetched from the DB by the caller, never trusted from input.
    private function calculateHotelBreakdown(HotelRoomType $roomType, int $nights, int $rooms): array
    {
        $referencePrice = max((float) $roomType->price, (float) $roomType->sellPrice);

        $baseFare = round($referencePrice * $nights * $rooms, 2);
        $sellFare = round($roomType->sellPrice * $nights * $rooms, 2);
        $discount = round(max(0, $baseFare - $sellFare), 2);
        $taxesFee = round($sellFare * 0.05, 2);
        $total = round($sellFare + $taxesFee, 2);

        return [
            'base_fare'        => $baseFare,
            'discount_amount'  => $discount,
            'activities_total' => 0,
            'hotels_total'     => 0,
            'taxes_fee'        => $taxesFee,
            'total_amount'     => $total,
        ];
    }

    private function resolveHotelStay(Request $request): array
    {
        $checkIn = $request->filled('checkin') ? Carbon::parse($request->get('checkin')) : Carbon::today();
        $checkOut = $request->filled('checkout') ? Carbon::parse($request->get('checkout')) : $checkIn->copy()->addDay();

        if ($checkOut->lessThanOrEqualTo($checkIn)) {
            $checkOut = $checkIn->copy()->addDay();
        }

        return [$checkIn, $checkOut, max(1, $checkIn->diffInDays($checkOut))];
    }

    private function assertHotelBookable(Hotel $hotel, HotelRoomType $roomType): void
    {
        abort_unless(
            $hotel->status === 'active'
                && $hotel->unique_id === SiteTenant::id()
                && $roomType->hotel_id === $hotel->id,
            404
        );
    }

    private function authorizeOwner(Booking $booking): void
    {
        abort_unless($booking->user_id === Auth::id(), 403);
    }

    // Logged-in visits (e.g. from the user dashboard) still go through normal ownership
    // checks. Visits with no session — the PayU-redirect case — are allowed only with a
    // valid signature, which only signedBookingUrl() above can produce.
    private function authorizeBookingAccess(Request $request, Booking $booking): void
    {
        if (Auth::check()) {
            $this->authorizeOwner($booking);

            return;
        }

        abort_unless($request->hasValidSignature(), 403);
    }
}
