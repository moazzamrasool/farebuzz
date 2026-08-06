<?php

namespace App\Mail;

use App\Models\LeadFollowUp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FollowUpReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public LeadFollowUp $followUp)
    {
    }

    public function build()
    {
        return $this->subject('Follow-up due: '.$this->followUp->leadable->name)
            ->view('emails.follow_up_reminder');
    }
}
