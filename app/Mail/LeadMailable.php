<?php

namespace App\Mail;

use App\Models\PackageEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Generic templated email sent to a lead's customer from the CRM's email composer.
class LeadMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PackageEnquiry $lead,
        public string $subjectLine,
        public string $bodyHtml,
        public ?string $attachmentPath = null,
        public ?string $attachmentName = null,
    ) {
    }

    public function build()
    {
        $mail = $this->subject($this->subjectLine)
            ->view('emails.lead_message', ['lead' => $this->lead, 'body' => $this->bodyHtml]);

        if ($this->attachmentPath) {
            $mail->attach($this->attachmentPath, ['as' => $this->attachmentName]);
        }

        return $mail;
    }
}
