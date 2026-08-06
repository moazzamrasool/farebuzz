@extends('layouts.user_dashboard')

@section('title', 'My Dashboard – FareBuzzer')

@section('content')
<div class="dash-page-title">Welcome back, {{ $user->name }}!</div>

<div class="stat-grid">
  <div class="stat-card">
    <div class="icon" style="background:#dbeafe;color:#1d4ed8;"><i class="bi bi-briefcase"></i></div>
    <div class="value">{{ $stats['bookings'] }}</div>
    <div class="label">My Bookings</div>
  </div>
  <div class="stat-card">
    <div class="icon" style="background:#fef3c7;color:#b45309;"><i class="bi bi-hourglass-split"></i></div>
    <div class="value">{{ $stats['pending_payments'] }}</div>
    <div class="label">Pending Payments</div>
  </div>
  <div class="stat-card">
    <div class="icon" style="background:#fce7f3;color:#be185d;"><i class="bi bi-chat-dots"></i></div>
    <div class="value">{{ $stats['enquiries'] }}</div>
    <div class="label">My Enquiries</div>
  </div>
  <div class="stat-card">
    <div class="icon" style="background:#dcfce7;color:#16a34a;"><i class="bi bi-airplane"></i></div>
    <div class="value">{{ $stats['upcoming_trips'] }}</div>
    <div class="label">Upcoming Trips</div>
  </div>
</div>

<div class="dash-card">
  <h5 style="font-weight:800;font-size:15px;margin-bottom:16px;">Recent Bookings</h5>

  @if($recentBookings->isEmpty())
    <div class="empty-state">
      <i class="bi bi-briefcase" style="font-size:28px;display:block;margin-bottom:8px;"></i>
      You haven't made any bookings yet.
      <div class="mt-2"><a href="{{ route('packages.index') }}">Browse Holiday Packages →</a></div>
    </div>
  @else
    <div style="overflow-x:auto;">
      <table class="dash-table">
        <thead>
          <tr><th>Reference</th><th>Package</th><th>Travel Date</th><th>Total</th><th>Status</th><th>Payment</th><th></th></tr>
        </thead>
        <tbody>
          @foreach($recentBookings as $booking)
            <tr>
              <td><code>{{ $booking->booking_reference }}</code></td>
              <td>{{ $booking->package_title }}</td>
              <td>{{ $booking->travel_date->format('d M Y') }}</td>
              <td>₹{{ number_format($booking->total_amount, 2) }}</td>
              <td>@include('user.partials._booking_status_badge', ['status' => $booking->status])</td>
              <td>@include('user.partials._payment_status_badge', ['status' => $booking->payment_status])</td>
              <td><a href="{{ route('user.bookings.show', $booking->booking_reference) }}">View →</a></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-3"><a href="{{ route('user.bookings.index') }}">View All Bookings →</a></div>
  @endif
</div>
@endsection
