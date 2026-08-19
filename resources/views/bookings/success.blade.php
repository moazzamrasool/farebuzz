@extends('layouts.app')

@section('title', 'Booking Confirmed – FareBuzzer')
@section('robots', 'noindex, follow')

@push('styles')
<style>
  :root { --blue:#005fcc; --orange:#f47b20; }
  .sc-wrap { max-width:680px; margin:40px auto 60px; padding:0 16px; }
  .sc-banner { background:#fff; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08); padding:32px; text-align:center; margin-bottom:20px; }
  .sc-icon { width:70px; height:70px; border-radius:50%; background:#dcfce7; color:#16a34a; display:flex; align-items:center; justify-content:center; font-size:32px; margin:0 auto 16px; }
  .sc-ref { display:inline-block; background:#f0f6ff; color:var(--blue); border-radius:8px; padding:6px 16px; font-weight:700; font-size:14px; margin-top:8px; }
  .sc-card { background:#fff; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08); padding:26px; margin-bottom:20px; }
  .sc-row { display:flex; justify-content:space-between; font-size:13px; padding:8px 0; border-bottom:1px solid #f2f2f2; }
  .sc-row:last-child { border-bottom:none; }
  .sc-row .label { color:#888; }
  .sc-total { font-size:18px; font-weight:800; color:var(--blue); }
  .btn-download { background:var(--blue); color:#fff; border:none; border-radius:10px; font-weight:700; padding:12px 24px; text-decoration:none; display:inline-block; }
</style>
@endpush

@section('content')
<div class="sc-wrap">
  <div class="sc-banner">
    <div class="sc-icon"><i class="bi bi-check-lg"></i></div>
    <h2 style="font-weight:800;">Booking Confirmed!</h2>
    <p class="text-muted" style="font-size:14px;">A confirmation email with your invoice has been sent to {{ $booking->traveller_email }}.</p>
    <div class="sc-ref">{{ $booking->booking_reference }}</div>
  </div>

  <div class="sc-card">
    @if($booking->booking_type === 'hotel')
      <h5 style="font-weight:800;font-size:15px;margin-bottom:14px;">Stay Summary</h5>
      <div class="sc-row"><span class="label">Hotel</span><span>{{ $booking->hotel->name ?? $booking->package_title }}</span></div>
      <div class="sc-row"><span class="label">Room Type</span><span>{{ $booking->room_type_name }}</span></div>
      <div class="sc-row"><span class="label">Check-in</span><span>{{ $booking->check_in_date?->format('d M Y') }}</span></div>
      <div class="sc-row"><span class="label">Check-out</span><span>{{ $booking->check_out_date?->format('d M Y') }}</span></div>
      <div class="sc-row"><span class="label">Nights / Rooms</span><span>{{ $booking->nights }} {{ Str::plural('night', $booking->nights) }} &middot; {{ $booking->rooms }} {{ Str::plural('room', $booking->rooms) }}</span></div>
      <div class="sc-row"><span class="label">Guests</span><span>{{ $booking->adults }} Adult(s){{ $booking->children ? ', '.$booking->children.' Child(ren)' : '' }}</span></div>
      <div class="sc-row"><span class="label">Traveller</span><span>{{ $booking->traveller_name }}</span></div>
    @else
      <h5 style="font-weight:800;font-size:15px;margin-bottom:14px;">Trip Summary</h5>
      <div class="sc-row"><span class="label">Package</span><span>{{ $booking->package_title }}</span></div>
      <div class="sc-row"><span class="label">Travel Date</span><span>{{ $booking->travel_date->format('d M Y') }}</span></div>
      @if($booking->departure_city)
        <div class="sc-row"><span class="label">Departure City</span><span>{{ $booking->departure_city }}</span></div>
      @endif
      @if($booking->room_type_name)
        <div class="sc-row"><span class="label">Room Type</span><span>{{ $booking->room_type_name }}</span></div>
      @endif
      <div class="sc-row"><span class="label">Travellers</span><span>{{ $booking->adults }} Adult(s){{ $booking->children ? ', '.$booking->children.' Child(ren)' : '' }}</span></div>
      <div class="sc-row"><span class="label">Traveller</span><span>{{ $booking->traveller_name }}</span></div>
    @endif
  </div>

  @if($booking->activities->isNotEmpty())
    <div class="sc-card">
      <h5 style="font-weight:800;font-size:15px;margin-bottom:14px;">Add-ons</h5>
      @foreach($booking->activities as $bookingActivity)
        <div class="sc-row"><span class="label">{{ $bookingActivity->name }} &times; {{ $bookingActivity->qty }}</span><span>₹{{ number_format($bookingActivity->line_total, 2) }}</span></div>
      @endforeach
    </div>
  @endif

  @if($booking->hotels->isNotEmpty())
    <div class="sc-card">
      <h5 style="font-weight:800;font-size:15px;margin-bottom:14px;">Hotel</h5>
      @foreach($booking->hotels as $bookingHotel)
        <div class="sc-row"><span class="label">{{ $bookingHotel->name }} &times; {{ $bookingHotel->nights }} night(s)</span><span>₹{{ number_format($bookingHotel->line_total, 2) }}</span></div>
      @endforeach
    </div>
  @endif

  <div class="sc-card">
    <h5 style="font-weight:800;font-size:15px;margin-bottom:14px;">Payment Summary</h5>
    <div class="sc-row"><span class="label">Base Fare</span><span>₹{{ number_format($booking->base_fare, 2) }}</span></div>
    @if($booking->discount_amount > 0)
      <div class="sc-row"><span class="label" style="color:#16a34a;">Discount</span><span style="color:#16a34a;">−₹{{ number_format($booking->discount_amount, 2) }}</span></div>
    @endif
    @if($booking->activities_total > 0)
      <div class="sc-row"><span class="label">Add-ons</span><span>₹{{ number_format($booking->activities_total, 2) }}</span></div>
    @endif
    @if($booking->hotels_total > 0)
      <div class="sc-row"><span class="label">Hotel</span><span>₹{{ number_format($booking->hotels_total, 2) }}</span></div>
    @endif
    <div class="sc-row"><span class="label">Taxes &amp; Fees</span><span>₹{{ number_format($booking->taxes_fee, 2) }}</span></div>
    <div class="sc-row"><span class="label sc-total">Total Paid</span><span class="sc-total">₹{{ number_format($booking->total_amount, 2) }}</span></div>
  </div>

  <div class="text-center">
    <a href="{{ route('user.bookings.invoice', $booking) }}" class="btn-download"><i class="bi bi-download me-1"></i> Download Invoice / Voucher (PDF)</a>
    <div class="mt-3"><a href="{{ route('user.dashboard') }}">Go to My Dashboard →</a></div>
  </div>
</div>
@endsection
