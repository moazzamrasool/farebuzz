<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// New-confirmed-booking notification sent to the admin/company inbox.
class BookingAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function build()
    {
        return $this->subject('New Booking — '.$this->booking->booking_reference)
            ->view('emails.booking_admin_notification');
    }
}
