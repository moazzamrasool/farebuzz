<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // User dashboard home — protected by 'auth' middleware (web guard)
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'bookings'         => $user->bookings()->count(),
            'pending_payments' => $user->bookings()->where('payment_status', 'pending')->count(),
            'enquiries'        => $user->packageEnquiries()->count(),
            'upcoming_trips'   => $user->bookings()->where('status', 'confirmed')->where('travel_date', '>=', now()->toDateString())->count(),
        ];

        $recentBookings = $user->bookings()->latest()->limit(5)->get();

        return view('user.dashboard', compact('user', 'stats', 'recentBookings'));
    }
}
