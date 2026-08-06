<?php

namespace App\Mail;

use App\Models\Booking;
use App\Services\ItineraryService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Sent when an admin clicks "Send Itinerary to Customer" on a booking (see
// Admin\BookingController@sendItinerary). Attaches the same PDF the customer
// can self-serve download from their dashboard (ItineraryService).
class BookingItineraryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function build()
    {
        $itineraries = app(ItineraryService::class);

        return $this->subject('Your Trip Itinerary — '.$this->booking->booking_reference.' — FareBuzzer')
            ->view('emails.booking_itinerary')
            ->attachData($itineraries->outputForBooking($this->booking), $itineraries->filenameForBooking($this->booking), [
                'mime' => 'application/pdf',
            ]);
    }
}
