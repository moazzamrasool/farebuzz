<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserQueryMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user_query;
    /**
     * Create a new message instance.
     */
    public function __construct($user_query)
    {
        $this->user_query = $user_query;
    }

    public function build()
    {
        return $this->subject('Get quote | Winify Logistics')
                    ->view('emails.user_query_mail');
    }
}
