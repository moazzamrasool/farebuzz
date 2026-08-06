<?php

namespace App\Mail;

use App\Models\PackageEnquiry;
use App\Services\ItineraryService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Sent when an admin clicks "Send Itinerary" on a lead (see
// Admin\PackageEnquiryController@sendItinerary). The lead hasn't booked yet,
// so this carries the package's general itinerary rather than a booking-specific one.
class LeadItineraryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PackageEnquiry $enquiry)
    {
    }

    public function build()
    {
        $itineraries = app(ItineraryService::class);

        return $this->subject('Your Trip Itinerary — FareBuzzer')
            ->view('emails.lead_itinerary')
            ->attachData($itineraries->outputForLead($this->enquiry), $itineraries->filenameForLead($this->enquiry), [
                'mime' => 'application/pdf',
            ]);
    }
}
