<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    // Show the "forgot password" email request form
    public function showLinkRequestForm()
    {
        return view('user.forgot-password');
    }

    // Send the password reset link to the given email
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.password.request')
                ->withInput()
                ->withErrors($validator);
        }

        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()->route('user.password.request')
                ->with('success', 'We have emailed your password reset link.');
        }

        return redirect()->route('user.password.request')
            ->withInput()
            ->with('error', 'We could not find a user with that email address.');
    }

    // Show the reset password form
    public function showResetForm(Request $request, string $token)
    {
        return view('user.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    // Process the password reset
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:5|confirmed',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.password.reset', ['token' => $request->token, 'email' => $request->email])
                ->withInput()
                ->withErrors($validator);
        }

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('user.login')
                ->with('success', 'Your password has been reset successfully. Please login now');
        }

        return redirect()->route('user.password.reset', ['token' => $request->token, 'email' => $request->email])
            ->withInput()
            ->with('error', 'This password reset link is invalid or has expired.');
    }
}
