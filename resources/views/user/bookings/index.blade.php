@extends('layouts.user_dashboard')

@section('title', 'My Bookings – FareBuzzer')

@section('content')
<div class="dash-page-title">My Bookings</div>

<div class="dash-card">
  @if($bookings->isEmpty())
    <div class="empty-state">
      <i class="bi bi-briefcase" style="font-size:28px;display:block;margin-bottom:8px;"></i>
      You haven't made any bookings yet.
      <div class="mt-2"><a href="{{ route('packages.index') }}">Browse Holiday Packages →</a> &middot; <a href="{{ route('hotels.index') }}">Browse Hotels →</a></div>
    </div>
  @else
    <div style="overflow-x:auto;">
      <table class="dash-table">
        <thead>
          <tr><th>Reference</th><th>Type</th><th>Package / Hotel</th><th>Date</th><th>Total</th><th>Status</th><th>Payment</th><th>Actions</th></tr>
        </thead>
        <tbody>
          @foreach($bookings as $booking)
            <tr>
              <td><code>{{ $booking->booking_reference }}</code></td>
              <td>{{ $booking->booking_type === 'hotel' ? 'Hotel' : 'Package' }}</td>
              <td>{{ $booking->package_title }}</td>
              <td>
                @if($booking->booking_type === 'hotel')
                  {{ $booking->check_in_date?->format('d M Y') }} &ndash; {{ $booking->check_out_date?->format('d M Y') }}
                @else
                  {{ $booking->travel_date->format('d M Y') }}
                @endif
              </td>
              <td>
                ₹{{ number_format($booking->total_amount, 2) }}
                @if($booking->coupon_code)
                  <br><small style="color:#16a34a;">{{ $booking->coupon_code }} applied</small>
                @endif
              </td>
              <td>@include('user.partials._booking_status_badge', ['status' => $booking->status])</td>
              <td>@include('user.partials._payment_status_badge', ['status' => $booking->payment_status])</td>
              <td>
                <a href="{{ route('user.bookings.show', $booking->booking_reference) }}">View</a>
                @if($booking->payment_status === 'pending')
                  &middot;
                  <form action="{{ route('bookings.retry', $booking->booking_reference) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link p-0" style="font-size:13px;">Retry Payment</button>
                  </form>
                @endif
                @if($booking->status === 'confirmed')
                  &middot; <a href="{{ route('user.bookings.invoice', $booking) }}">Invoice</a>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $bookings->links() }}</div>
  @endif
</div>
@endsection
