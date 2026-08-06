<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\UserEmailVerificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    // Show user login page
    public function index()
    {
        return view('user.login');
    }

    // Process user login — uses default 'web' guard
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:5',
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.login')
                ->withInput()
                ->withErrors($validator);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            if ($user->status === 'suspended') {
                Auth::logout();

                return redirect()->route('user.login')
                    ->with('error', 'Your account has been suspended. Please contact support.');
            }

            if (!$user->isVerified() || !$user->isActive()) {
                Auth::logout();

                return redirect()->route('user.login')
                    ->with('error', 'Please verify your email before logging in.')
                    ->with('unverified_email', $user->email);
            }

            $request->session()->regenerate();
            // Sends the user back to whatever protected page they were trying to reach
            // (e.g. the booking form after clicking "Book Now" while logged out).
            return redirect()->intended(route('user.dashboard'));
        }

        return redirect()->route('user.login')
            ->with('error', 'Email or password is wrong. Please try again');
    }

    // Show user registration page
    public function register()
    {
        return view('user.register');
    }

    // Process user registration
    public function processRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'              => 'required|min:3',
            'email'                 => 'required|email|unique:users',
            'password'              => 'required|min:5|confirmed',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.register')
                ->withInput()
                ->withErrors($validator);
        }

        $user                           = new User();
        $user->name                     = $request->username;
        $user->email                    = $request->email;
        $user->password                 = Hash::make($request->password);
        $user->role                     = 'customer';
        $user->status                   = 'pending_verification';
        $user->email_verification_token = Str::random(64);
        $user->save();

        Mail::to($user->email)->send(new UserEmailVerificationMail($user));

        return redirect()->route('user.login')
            ->with('success', 'Registration successful! Please check your email to verify your account before logging in.');
    }

    // Logout user
    public function logout()
    {
        Auth::logout();
        return redirect()->route('user.login');
    }
}
