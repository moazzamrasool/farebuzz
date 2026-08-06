<?php

namespace App\Mail;

use App\Models\PackageEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// New-enquiry notification sent to the admin/company inbox (config('mail.to.address')).
class PackageEnquiryAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PackageEnquiry $enquiry)
    {
    }

    public function build()
    {
        return $this->subject('New package enquiry — '.($this->enquiry->holidayPackage->title ?? 'FareBuzzer'))
            ->view('emails.package_enquiry_admin');
    }
}
