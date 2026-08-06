@extends('layouts.user_dashboard')

@section('title', 'My Trips – FareBuzzer')

@section('content')
<div class="dash-page-title">My Trips</div>

<div class="dash-card">
  <h5 style="font-weight:800;font-size:15px;margin-bottom:16px;"><i class="bi bi-airplane-engines text-primary me-1"></i> Upcoming Trips</h5>
  @if($upcoming->isEmpty())
    <div class="empty-state">No upcoming trips. Time to plan your next getaway!</div>
  @else
    <div style="overflow-x:auto;">
      <table class="dash-table">
        <thead><tr><th>Package</th><th>Travel Date</th><th>Reference</th><th></th></tr></thead>
        <tbody>
          @foreach($upcoming as $booking)
            <tr>
              <td>{{ $booking->package_title }}</td>
              <td>{{ $booking->travel_date->format('d M Y') }}</td>
              <td><code>{{ $booking->booking_reference }}</code></td>
              <td><a href="{{ route('user.bookings.show', $booking->booking_reference) }}">View →</a></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>

<div class="dash-card">
  <h5 style="font-weight:800;font-size:15px;margin-bottom:16px;"><i class="bi bi-clock-history text-muted me-1"></i> Past Trips</h5>
  @if($past->isEmpty())
    <div class="empty-state">No past trips yet.</div>
  @else
    <div style="overflow-x:auto;">
      <table class="dash-table">
        <thead><tr><th>Package</th><th>Travel Date</th><th>Reference</th><th></th></tr></thead>
        <tbody>
          @foreach($past as $booking)
            <tr>
              <td>{{ $booking->package_title }}</td>
              <td>{{ $booking->travel_date->format('d M Y') }}</td>
              <td><code>{{ $booking->booking_reference }}</code></td>
              <td><a href="{{ route('user.bookings.show', $booking->booking_reference) }}">View →</a></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection
