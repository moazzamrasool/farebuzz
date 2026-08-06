<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;

// Generates the booking invoice/voucher PDF — used by the on-screen download button,
// the user dashboard, the admin panel, and as an email attachment (see Part D).
class InvoiceService
{
    public function download(Booking $booking)
    {
        return $this->pdf($booking)->download($this->filename($booking));
    }

    // Raw PDF bytes, for Mailable::attachData().
    public function output(Booking $booking): string
    {
        return $this->pdf($booking)->output();
    }

    public function filename(Booking $booking): string
    {
        return "invoice-{$booking->booking_reference}.pdf";
    }

    private function pdf(Booking $booking)
    {
        $payment = $booking->payments()->where('status', 'success')->latest()->first()
            ?? $booking->payments()->latest()->first();

        return Pdf::loadView('pdf.booking_invoice', compact('booking', 'payment'))->setPaper('a4');
    }
}
