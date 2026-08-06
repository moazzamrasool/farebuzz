<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    // Confirmed bookings only — a pending/failed booking isn't a "trip" yet.
    public function index()
    {
        $confirmed = Auth::user()->bookings()->where('status', 'confirmed');

        $upcoming = (clone $confirmed)->where('travel_date', '>=', now()->toDateString())->orderBy('travel_date')->get();
        $past = (clone $confirmed)->where('travel_date', '<', now()->toDateString())->orderByDesc('travel_date')->get();

        return view('user.trips.index', compact('upcoming', 'past'));
    }
}
