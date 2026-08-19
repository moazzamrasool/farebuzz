@extends('layouts.app')

@section('title', 'Payment Failed – FareBuzzer')
@section('robots', 'noindex, follow')

@push('styles')
<style>
  :root { --blue:#005fcc; }
  .fl-wrap { max-width:560px; margin:60px auto; padding:0 16px; text-align:center; }
  .fl-icon { width:80px; height:80px; border-radius:50%; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; font-size:36px; margin:0 auto 20px; }
  .fl-title { font-size:24px; font-weight:800; margin-bottom:10px; }
  .fl-sub { font-size:14px; color:#666; line-height:1.7; margin-bottom:12px; }
  .fl-ref { display:inline-block; background:#f5f5f5; color:#555; border-radius:8px; padding:5px 14px; font-size:13px; margin-bottom:24px; }
  .btn-retry { background:var(--blue); color:#fff; border:none; border-radius:10px; font-weight:700; padding:12px 26px; }
</style>
@endpush

@section('content')
<div class="fl-wrap">
  <div class="fl-icon"><i class="bi bi-x-lg"></i></div>
  <div class="fl-title">Payment {{ $booking->status === 'cancelled' ? 'Cancelled' : 'Failed' }}</div>
  <p class="fl-sub">
    We couldn't confirm your payment for <strong>{{ $booking->package_title }}</strong>.
    No amount has been charged for a failed attempt, or it will be auto-refunded if it was.
  </p>
  <div class="fl-ref">Booking Reference: {{ $booking->booking_reference }}</div>
  <div>
    @if($booking->payment_status !== 'paid')
      <form action="{{ route('bookings.retry', $booking->booking_reference) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn-retry">Retry Payment</button>
      </form>
    @endif
    <div class="mt-3"><a href="{{ route('user.dashboard') }}">Go to My Dashboard →</a></div>
  </div>
</div>
@endsection
