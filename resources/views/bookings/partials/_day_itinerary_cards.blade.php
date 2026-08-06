{{--
  Day-by-day itinerary breakdown for the admin and user "My Bookings" detail pages.
  Expects: $booking (with hotels/activities loaded), $variant ('admin' default, or
  'user' to match the dashboard's .dash-card/.dash-table styling instead of AdminLTE's
  plain bootstrap tables). Uses each line item's own stored day_number +
  stay_date/activity_date (set at booking time from travel_date + day_number - 1).
--}}
@php
  $variant = $variant ?? 'admin';
  $dayNumbers = $booking->hotels->pluck('day_number')
    ->merge($booking->activities->pluck('day_number'))
    ->filter()
    ->unique()
    ->sort()
    ->values();
  $generalHotels = $booking->hotels->whereNull('day_number');
  $generalActivities = $booking->activities->whereNull('day_number');
  $hasAny = $dayNumbers->isNotEmpty() || $generalHotels->isNotEmpty() || $generalActivities->isNotEmpty();
@endphp
@if($hasAny)
  @foreach($dayNumbers as $dayNumber)
    @php
      $dayHotels = $booking->hotels->where('day_number', $dayNumber);
      $dayActivities = $booking->activities->where('day_number', $dayNumber);
      $dayDate = $dayHotels->first()->stay_date ?? $dayActivities->first()->activity_date ?? null;
      $dayLabel = 'Day '.$dayNumber.($dayDate ? ' · '.$dayDate->format('d M Y, D') : '');
    @endphp
    @if($variant === 'user')
      <div class="dash-card">
        <h6 style="font-weight:800;font-size:13px;color:#888;text-transform:uppercase;margin-bottom:12px;">{{ $dayLabel }}</h6>
        <table class="dash-table" style="max-width:420px;">
          @foreach($dayHotels as $bookingHotel)
            <tr><td>Hotel: {{ $bookingHotel->name }} <span class="text-muted">&times; {{ $bookingHotel->nights }} night(s)</span></td><td style="text-align:right;">₹{{ number_format($bookingHotel->line_total, 2) }}</td></tr>
          @endforeach
          @foreach($dayActivities as $bookingActivity)
            <tr><td>Activity: {{ $bookingActivity->name }} <span class="text-muted">&times; {{ $bookingActivity->qty }}</span></td><td style="text-align:right;">₹{{ number_format($bookingActivity->line_total, 2) }}</td></tr>
          @endforeach
        </table>
      </div>
    @else
      <h6>{{ $dayLabel }}</h6>
      <table class="table table-sm">
        <tbody>
          @foreach($dayHotels as $bookingHotel)
            <tr><td>Hotel: {{ $bookingHotel->name }}</td><td>{{ $bookingHotel->nights }} night(s)</td><td>₹{{ number_format($bookingHotel->line_total, 2) }}</td></tr>
          @endforeach
          @foreach($dayActivities as $bookingActivity)
            <tr><td>Activity: {{ $bookingActivity->name }}</td><td>{{ $bookingActivity->qty }} pax</td><td>₹{{ number_format($bookingActivity->line_total, 2) }}</td></tr>
          @endforeach
        </tbody>
      </table>
    @endif
  @endforeach

  @if($generalHotels->isNotEmpty() || $generalActivities->isNotEmpty())
    @if($variant === 'user')
      <div class="dash-card">
        <h6 style="font-weight:800;font-size:13px;color:#888;text-transform:uppercase;margin-bottom:12px;">General Add-ons <span class="text-muted" style="text-transform:none;font-weight:400;">(not tied to a specific day)</span></h6>
        <table class="dash-table" style="max-width:420px;">
          @foreach($generalHotels as $bookingHotel)
            <tr><td>Hotel: {{ $bookingHotel->name }} <span class="text-muted">&times; {{ $bookingHotel->nights }} night(s)</span></td><td style="text-align:right;">₹{{ number_format($bookingHotel->line_total, 2) }}</td></tr>
          @endforeach
          @foreach($generalActivities as $bookingActivity)
            <tr><td>Activity: {{ $bookingActivity->name }} <span class="text-muted">&times; {{ $bookingActivity->qty }}</span></td><td style="text-align:right;">₹{{ number_format($bookingActivity->line_total, 2) }}</td></tr>
          @endforeach
        </table>
      </div>
    @else
      <h6>General Add-ons <small class="text-muted">(not tied to a specific day)</small></h6>
      <table class="table table-sm">
        <tbody>
          @foreach($generalHotels as $bookingHotel)
            <tr><td>Hotel: {{ $bookingHotel->name }}</td><td>{{ $bookingHotel->nights }} night(s)</td><td>₹{{ number_format($bookingHotel->line_total, 2) }}</td></tr>
          @endforeach
          @foreach($generalActivities as $bookingActivity)
            <tr><td>Activity: {{ $bookingActivity->name }}</td><td>{{ $bookingActivity->qty }} pax</td><td>₹{{ number_format($bookingActivity->line_total, 2) }}</td></tr>
          @endforeach
        </tbody>
      </table>
    @endif
  @endif
@endif
