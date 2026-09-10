<?php

namespace App\Mail;

use App\Models\PackageEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Sent only when the visitor gave an email on a standalone landing page — the
// controller skips this entirely when the form's email field was left blank.
class LandingEnquiryThankYouMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PackageEnquiry $enquiry, public string $landingPageName)
    {
    }

    public function build()
    {
        return $this->subject('Thank you for your enquiry - Fare Buzzer Travel')
            ->view('emails.landing_enquiry_thankyou');
    }
}
