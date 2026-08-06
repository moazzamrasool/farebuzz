<?php

/*
|--------------------------------------------------------------------------
| User Routes  —  prefix: /  (root)
|--------------------------------------------------------------------------
| Guard  : web  (App\Models\User, users table)
| Middleware: guest  →  redirect authenticated user to user.dashboard
|             auth   →  redirect unauthenticated to user.login (/login)
*/

use App\Http\Controllers\User\BookingController as UserBookingController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\EmailVerificationController;
use App\Http\Controllers\User\ForgotPasswordController;
use App\Http\Controllers\User\LoginController as UserLoginController;
use App\Http\Controllers\User\PackageEnquiryController as UserPackageEnquiryController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\User\SocialAuthController;
use App\Http\Controllers\User\TripController as UserTripController;
use Illuminate\Support\Facades\Route;

// ── Public (user not yet logged in) ─────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('login',    [UserLoginController::class, 'index'])->name('user.login');
    Route::post('login',   [UserLoginController::class, 'authenticate'])->name('user.authenticate');
    Route::get('register', [UserLoginController::class, 'register'])->name('user.register');
    Route::post('register',[UserLoginController::class, 'processRegister'])->name('user.processRegister');

    // Forgot / reset password
    Route::get('forgot-password',  [ForgotPasswordController::class, 'showLinkRequestForm'])->name('user.password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('user.password.email');
    Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('user.password.reset');
    Route::post('reset-password',  [ForgotPasswordController::class, 'reset'])->name('user.password.update');

    // Social OAuth — /auth/{provider} and /auth/{provider}/callback
    Route::get('auth/{provider}',          [SocialAuthController::class, 'redirect'])->name('social.redirect');
    Route::get('auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

    // Email verification
    Route::get('verify-email/{token}',   [EmailVerificationController::class, 'verify'])->name('user.verify-email');
    Route::get('resend-verification',    [EmailVerificationController::class, 'showResend'])->name('user.resend-verification.form');
    Route::post('resend-verification',   [EmailVerificationController::class, 'resend'])->name('user.resend-verification');
});

// ── Protected (user must be logged in) ──────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('logout',    [UserLoginController::class,     'logout'])->name('user.logout');

    // My Bookings
    Route::get('dashboard/bookings',                                    [UserBookingController::class, 'index'])->name('user.bookings.index');
    Route::get('dashboard/bookings/{booking:booking_reference}',         [UserBookingController::class, 'show'])->name('user.bookings.show');
    Route::get('dashboard/bookings/{booking:booking_reference}/invoice', [UserBookingController::class, 'invoice'])->name('user.bookings.invoice');
    Route::get('dashboard/bookings/{booking:booking_reference}/itinerary', [UserBookingController::class, 'itinerary'])->name('user.bookings.itinerary');

    // My Enquiries
    Route::get('dashboard/enquiries', [UserPackageEnquiryController::class, 'index'])->name('user.enquiries.index');

    // My Trips
    Route::get('dashboard/trips', [UserTripController::class, 'index'])->name('user.trips.index');

    // My Profile
    Route::get('dashboard/profile',           [UserProfileController::class, 'edit'])->name('user.profile.edit');
    Route::put('dashboard/profile',           [UserProfileController::class, 'update'])->name('user.profile.update');
    Route::put('dashboard/profile/password',  [UserProfileController::class, 'updatePassword'])->name('user.profile.password');
});
