<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Verification link sent on registration and on resend requests (see
// App\Http\Controllers\User\EmailVerificationController).
class UserEmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function build()
    {
        return $this->subject('Verify your email — FareBuzzer')
            ->view('emails.user_email_verification');
    }
}
