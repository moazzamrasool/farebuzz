<?php

namespace App\Mail;

use App\Models\Quotation;
use App\Services\QuotationService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Sent when an admin submits the "Create Quotation" page for a lead (see
// Admin\QuotationController@store) — a single action both saves and emails the quote.
class QuotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Quotation $quotation)
    {
    }

    public function build()
    {
        $quotations = app(QuotationService::class);

        return $this->subject('Your Quotation — '.$this->quotation->quotation_number.' — FareBuzzer')
            ->view('emails.quotation')
            ->attachData($quotations->output($this->quotation), $quotations->filename($this->quotation), [
                'mime' => 'application/pdf',
            ]);
    }
}
