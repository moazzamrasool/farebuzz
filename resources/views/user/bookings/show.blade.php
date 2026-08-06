@extends('layouts.user_dashboard')

@section('title', 'Booking '.$booking->booking_reference.' – FareBuzzer')

@section('content')
<div class="dash-page-title">Booking {{ $booking->booking_reference }}</div>

<div class="dash-card">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      @include('user.partials._booking_status_badge', ['status' => $booking->status])
      @include('user.partials._payment_status_badge', ['status' => $booking->payment_status])
    </div>
    <div>
      @if($booking->status === 'confirmed')
        <a href="{{ route('user.bookings.invoice', $booking) }}" class="btn btn-sm" style="background:#005fcc;color:#fff;"><i class="bi bi-download"></i> Download Invoice</a>
        @if($hasItinerary)
          <a href="{{ route('user.bookings.itinerary', $booking) }}" class="btn btn-sm" style="background:#f47b20;color:#fff;"><i class="bi bi-map"></i> Download Itinerary</a>
        @endif
      @elseif($booking->payment_status === 'pending')
        <form action="{{ route('bookings.retry', $booking->booking_reference) }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-sm" style="background:#005fcc;color:#fff;">Retry Payment</button>
        </form>
      @endif
    </div>
  </div>

  <div class="row g-4">
    <div class="col-md-6">
      @if($booking->booking_type === 'hotel')
        <h6 style="font-weight:800;font-size:13px;color:#888;text-transform:uppercase;">Stay Details</h6>
        <p class="mb-1"><strong>Hotel:</strong> {{ $booking->hotel->name ?? $booking->package_title }}</p>
        <p class="mb-1"><strong>Room Type:</strong> {{ $booking->room_type_name }}</p>
        <p class="mb-1"><strong>Check-in:</strong> {{ $booking->check_in_date?->format('d M Y') }}</p>
        <p class="mb-1"><strong>Check-out:</strong> {{ $booking->check_out_date?->format('d M Y') }}</p>
        <p class="mb-1"><strong>Nights / Rooms:</strong> {{ $booking->nights }} &middot; {{ $booking->rooms }}</p>
        <p class="mb-1"><strong>Guests:</strong> {{ $booking->adults }} Adult(s){{ $booking->children ? ', '.$booking->children.' Child(ren)' : '' }}</p>
      @else
        <h6 style="font-weight:800;font-size:13px;color:#888;text-transform:uppercase;">Trip Details</h6>
        <p class="mb-1"><strong>Package:</strong> {{ $booking->package_title }}</p>
        <p class="mb-1"><strong>Travel Date:</strong> {{ $booking->travel_date->format('d M Y') }}</p>
        @if($booking->departure_city)<p class="mb-1"><strong>Departure City:</strong> {{ $booking->departure_city }}</p>@endif
        @if($booking->room_type_name)<p class="mb-1"><strong>Room Type:</strong> {{ $booking->room_type_name }}</p>@endif
        <p class="mb-1"><strong>Travellers:</strong> {{ $booking->adults }} Adult(s){{ $booking->children ? ', '.$booking->children.' Child(ren)' : '' }}</p>
      @endif
    </div>
    <div class="col-md-6">
      <h6 style="font-weight:800;font-size:13px;color:#888;text-transform:uppercase;">Traveller</h6>
      <p class="mb-1"><strong>Name:</strong> {{ $booking->traveller_name }}</p>
      <p class="mb-1"><strong>Email:</strong> {{ $booking->traveller_email }}</p>
      <p class="mb-1"><strong>Phone:</strong> {{ $booking->traveller_phone }}</p>
      <p class="mb-1"><strong>Address:</strong> {{ $booking->traveller_address }}</p>
    </div>
  </div>
</div>

@include('bookings.partials._day_itinerary_cards', ['booking' => $booking, 'variant' => 'user'])

@if($booking->activities->isNotEmpty())
  <div class="dash-card">
    <h6 style="font-weight:800;font-size:13px;color:#888;text-transform:uppercase;margin-bottom:12px;">Add-ons</h6>
    <table class="dash-table" style="max-width:420px;">
      @foreach($booking->activities as $bookingActivity)
        <tr>
          <td>{{ $bookingActivity->name }} <span class="text-muted">&times; {{ $bookingActivity->qty }}</span></td>
          <td style="text-align:right;">₹{{ number_format($bookingActivity->line_total, 2) }}</td>
        </tr>
      @endforeach
    </table>
  </div>
@endif

@if($booking->hotels->isNotEmpty())
  <div class="dash-card">
    <h6 style="font-weight:800;font-size:13px;color:#888;text-transform:uppercase;margin-bottom:12px;">Hotel</h6>
    <table class="dash-table" style="max-width:420px;">
      @foreach($booking->hotels as $bookingHotel)
        <tr>
          <td>{{ $bookingHotel->name }} <span class="text-muted">&times; {{ $bookingHotel->nights }} night(s)</span></td>
          <td style="text-align:right;">₹{{ number_format($bookingHotel->line_total, 2) }}</td>
        </tr>
      @endforeach
    </table>
  </div>
@endif

<div class="dash-card">
  <h6 style="font-weight:800;font-size:13px;color:#888;text-transform:uppercase;margin-bottom:12px;">Price Breakdown</h6>
  <table class="dash-table" style="max-width:420px;">
    <tr><td>Base Fare</td><td style="text-align:right;">₹{{ number_format($booking->base_fare, 2) }}</td></tr>
    @if($booking->discount_amount > 0)
      <tr><td style="color:#16a34a;">Discount</td><td style="text-align:right;color:#16a34a;">−₹{{ number_format($booking->discount_amount, 2) }}</td></tr>
    @endif
    @if($booking->activities_total > 0)
      <tr><td>Add-ons</td><td style="text-align:right;">₹{{ number_format($booking->activities_total, 2) }}</td></tr>
    @endif
    @if($booking->hotels_total > 0)
      <tr><td>Hotel</td><td style="text-align:right;">₹{{ number_format($booking->hotels_total, 2) }}</td></tr>
    @endif
    @if($booking->coupon_code)
      <tr><td style="color:#16a34a;">Coupon Discount ({{ $booking->coupon_code }})</td><td style="text-align:right;color:#16a34a;">−₹{{ number_format($booking->coupon_discount_amount, 2) }}</td></tr>
    @endif
    <tr><td>Taxes &amp; Fees</td><td style="text-align:right;">₹{{ number_format($booking->taxes_fee, 2) }}</td></tr>
    <tr><td style="font-weight:800;">Total</td><td style="text-align:right;font-weight:800;color:#005fcc;">₹{{ number_format($booking->total_amount, 2) }}</td></tr>
  </table>
</div>
@endsection
