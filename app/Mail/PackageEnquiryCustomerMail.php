<?php

namespace App\Mail;

use App\Models\PackageEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// "Thank you for your enquiry" email sent to the customer who submitted it.
// Uses the Queueable trait so it can be dispatched with ->queue() instead of
// ->send() later without any other changes — sent synchronously for now to
// match the rest of the app's mail (see UserQueryMail).
class PackageEnquiryCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PackageEnquiry $enquiry)
    {
    }

    public function build()
    {
        return $this->subject('We received your enquiry — FareBuzzer')
            ->view('emails.package_enquiry_customer');
    }
}
