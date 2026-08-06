{{--
  Day-by-day itinerary breakdown, table-based with inline styles only (no external/
  internal CSS classes) so it renders identically inside the dompdf invoice and both
  plain-HTML confirmation emails. Expects: $booking with hotels/activities loaded.
  Uses each line item's own stored day_number + stay_date/activity_date (set at
  booking time from travel_date + day_number - 1) — never recomputed here.
--}}
@php
  $dayNumbers = $booking->hotels->pluck('day_number')
    ->merge($booking->activities->pluck('day_number'))
    ->filter()
    ->unique()
    ->sort()
    ->values();
  $generalHotels = $booking->hotels->whereNull('day_number');
  $generalActivities = $booking->activities->whereNull('day_number');
@endphp
@if($dayNumbers->isNotEmpty() || $generalHotels->isNotEmpty() || $generalActivities->isNotEmpty())
  <div style="font-size:12px;font-weight:bold;color:#005fcc;border-bottom:1px solid #ddd;padding-bottom:4px;margin:18px 0 8px;">Day-by-Day Itinerary</div>
  @foreach($dayNumbers as $dayNumber)
    @php
      $dayHotels = $booking->hotels->where('day_number', $dayNumber);
      $dayActivities = $booking->activities->where('day_number', $dayNumber);
      $dayDate = $dayHotels->first()->stay_date ?? $dayActivities->first()->activity_date ?? null;
    @endphp
    <table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:12px;border-collapse:collapse;margin-bottom:6px;">
      <tr>
        <td colspan="2" style="font-weight:bold;background:#f5f5f5;padding:6px 8px;">
          Day {{ $dayNumber }}@if($dayDate) &middot; {{ $dayDate->format('d M Y, D') }}@endif
        </td>
      </tr>
      @foreach($dayHotels as $bookingHotel)
        <tr>
          <td style="color:#555;padding:4px 8px;">Hotel: {{ $bookingHotel->name }} &times; {{ $bookingHotel->nights }} night(s)</td>
          <td align="right" style="padding:4px 8px;">₹{{ number_format($bookingHotel->line_total, 2) }}</td>
        </tr>
      @endforeach
      @foreach($dayActivities as $bookingActivity)
        <tr>
          <td style="color:#555;padding:4px 8px;">Activity: {{ $bookingActivity->name }} &times; {{ $bookingActivity->qty }}</td>
          <td align="right" style="padding:4px 8px;">₹{{ number_format($bookingActivity->line_total, 2) }}</td>
        </tr>
      @endforeach
    </table>
  @endforeach

  @if($generalHotels->isNotEmpty() || $generalActivities->isNotEmpty())
    <table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:12px;border-collapse:collapse;margin-bottom:6px;">
      <tr>
        <td colspan="2" style="font-weight:bold;background:#f5f5f5;padding:6px 8px;">General Add-ons <span style="font-weight:normal;color:#999;">(not tied to a specific day)</span></td>
      </tr>
      @foreach($generalHotels as $bookingHotel)
        <tr>
          <td style="color:#555;padding:4px 8px;">Hotel: {{ $bookingHotel->name }} &times; {{ $bookingHotel->nights }} night(s)</td>
          <td align="right" style="padding:4px 8px;">₹{{ number_format($bookingHotel->line_total, 2) }}</td>
        </tr>
      @endforeach
      @foreach($generalActivities as $bookingActivity)
        <tr>
          <td style="color:#555;padding:4px 8px;">Activity: {{ $bookingActivity->name }} &times; {{ $bookingActivity->qty }}</td>
          <td align="right" style="padding:4px 8px;">₹{{ number_format($bookingActivity->line_total, 2) }}</td>
        </tr>
      @endforeach
    </table>
  @endif
@endif
