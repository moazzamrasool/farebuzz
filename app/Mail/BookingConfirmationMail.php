<?php

namespace App\Mail;

use App\Models\Booking;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Sent to the traveller once payment is verified and the booking is confirmed,
// with the PDF invoice/voucher attached.
class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function build()
    {
        $invoices = app(InvoiceService::class);

        return $this->subject('Booking Confirmed — '.$this->booking->booking_reference.' — FareBuzzer')
            ->view('emails.booking_confirmation')
            ->attachData($invoices->output($this->booking), $invoices->filename($this->booking), [
                'mime' => 'application/pdf',
            ]);
    }
}
