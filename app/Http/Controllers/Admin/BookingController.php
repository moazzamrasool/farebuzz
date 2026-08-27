<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BookingAdminMail;
use App\Mail\BookingConfirmationMail;
use App\Mail\BookingItineraryMail;
use App\Models\Booking;
use App\Services\InvoiceService;
use App\Services\ItineraryService;
use App\Services\PayUService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

// Read-only booking listing — TenantScope on Booking (via BelongsToTenant) already
// filters to the logged-in admin's own company, same as every other Master Data module.
class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookingType = $request->get('type');

        $bookings = Booking::with('holidayPackage', 'hotel')
            ->when(in_array($bookingType, ['package', 'hotel'], true), fn ($q) => $q->where('booking_type', $bookingType))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings', 'bookingType'));
    }

    public function show(Booking $booking, ItineraryService $itineraries)
    {
        $booking->load('payments', 'activities', 'hotels', 'hotel', 'hotelRoomType', 'user');

        $hasItinerary = $itineraries->availableForBooking($booking);

        return view('admin.bookings.show', compact('booking', 'hasItinerary'));
    }

    public function invoice(Booking $booking, InvoiceService $invoices)
    {
        return $invoices->download($booking);
    }

    public function itinerary(Booking $booking, ItineraryService $itineraries)
    {
        return $itineraries->downloadForBooking($booking);
    }

    // Renders the exact same PDF the "Send Itinerary to Customer" button below
    // would email, inline in the browser, so staff can check it before sending.
    public function previewItinerary(Booking $booking, ItineraryService $itineraries)
    {
        return $itineraries->previewForBooking($booking);
    }

    public function sendItinerary(Booking $booking)
    {
        abort_unless($booking->user?->email, 404, 'This booking has no customer email on file.');

        Mail::to($booking->user->email)->send(new BookingItineraryMail($booking));

        return back()->with('success', 'Itinerary emailed to the customer.');
    }

    // Recovery path for "customer paid but our PayU callback never arrived" — asks PayU
    // directly for the latest payment attempt's real status and, if PayU confirms
    // success and the amount matches, confirms the booking exactly as a live callback
    // would (same idempotency guard, same amount check — see PayUService).
    public function reconcilePayment(Booking $booking, PayUService $payU)
    {
        if ($booking->payment_status === 'paid') {
            return back()->with('success', 'This booking is already paid.');
        }

        $payment = $booking->payments()->latest()->first();

        if (!$payment) {
            return back()->with('error', 'This booking has no payment attempts to verify.');
        }

        $result = $payU->reconcileWithGateway($payment);

        if (!$result['ok']) {
            return back()->with('error', 'Could not verify with PayU: '.$result['message']);
        }

        $outcome = $payU->confirmFromReconciliation($payment, $result['raw']);

        if ($outcome['status'] === 'confirmed') {
            try {
                Mail::to($booking->traveller_email)->queue(new BookingConfirmationMail($outcome['booking']));
                Mail::to(config('mail.admin_address'))->queue(new BookingAdminMail($outcome['booking']));
            } catch (\Throwable $e) {
                Log::error('Booking confirmation email failed to queue after reconcile: '.$e->getMessage(), ['booking_id' => $booking->id]);
            }

            return back()->with('success', 'PayU confirmed this payment — booking marked paid and confirmation emails queued.');
        }

        if ($outcome['status'] === 'already_confirmed') {
            return back()->with('success', 'This booking was already confirmed (possibly by a callback that arrived just now).');
        }

        if ($outcome['status'] === 'mismatch') {
            return back()->with('error', 'PayU reports this payment as successful, but the amount does not match this booking. Not confirmed — investigate before touching it manually.');
        }

        return back()->with('error', 'PayU reports this transaction as: '.($outcome['gateway_status'] ?? 'unknown').'. Not confirmed.');
    }
}
