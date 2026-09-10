<?php

namespace App\Mail;

use App\Models\PackageEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Admin notification for a standalone landing-page lead (no holiday_package_id to
// describe it, unlike PackageEnquiryAdminMail) — carries the landing page's own
// name plus a direct link back to the lead in the CRM.
class LandingEnquiryAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PackageEnquiry $enquiry, public string $landingPageName)
    {
    }

    public function build()
    {
        return $this->subject("New Enquiry - {$this->landingPageName}")
            ->view('emails.landing_enquiry_admin');
    }
}
