<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\UserEmailVerificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmailVerificationController extends Controller
{
    // Verification link target — /verify-email/{token}
    public function verify(string $token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('user.login')
                ->with('error', 'This verification link is invalid or has expired.');
        }

        $user->email_verified_at         = now();
        $user->status                    = 'active';
        $user->email_verification_token  = null;
        $user->save();

        return redirect()->route('user.login')
            ->with('success', 'Your email has been verified. Please login now.');
    }

    // Show the "resend verification email" form
    public function showResend(Request $request)
    {
        return view('user.resend-verification', [
            'email' => $request->query('email'),
        ]);
    }

    // Process a resend request
    public function resend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.resend-verification.form')
                ->withInput()
                ->withErrors($validator);
        }

        $user = User::where('email', $request->email)->first();

        if ($user && !$user->isVerified()) {
            $user->email_verification_token = Str::random(64);
            $user->save();

            Mail::to($user->email)->send(new UserEmailVerificationMail($user));
        }

        // Same response whether or not the email exists / is already verified —
        // matches ForgotPasswordController's existing disclosure behavior for this app.
        return redirect()->route('user.login')
            ->with('success', 'If an account with that email needs verification, we have sent a new link.');
    }
}
