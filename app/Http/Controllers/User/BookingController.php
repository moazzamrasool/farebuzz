<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\InvoiceService;
use App\Services\ItineraryService;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Auth::user()->bookings()->latest()->paginate(10);

        return view('user.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking, ItineraryService $itineraries)
    {
        $this->authorizeOwner($booking);
        $booking->load('payments', 'activities', 'hotels');

        $hasItinerary = $itineraries->availableForBooking($booking);

        return view('user.bookings.show', compact('booking', 'hasItinerary'));
    }

    public function invoice(Booking $booking, InvoiceService $invoices)
    {
        $this->authorizeOwner($booking);

        return $invoices->download($booking);
    }

    public function itinerary(Booking $booking, ItineraryService $itineraries)
    {
        $this->authorizeOwner($booking);

        return $itineraries->downloadForBooking($booking);
    }

    private function authorizeOwner(Booking $booking): void
    {
        abort_unless($booking->user_id === Auth::id(), 403);
    }
}
