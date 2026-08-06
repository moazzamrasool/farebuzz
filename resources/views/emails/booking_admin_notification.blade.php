<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>New Booking</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;color:#111;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:24px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="580" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;">
          <tr>
            <td style="background:#111827;padding:20px 28px;">
              <span style="color:#ffffff;font-size:16px;font-weight:700;">New Confirmed Booking — {{ $booking->booking_reference }}</span>
            </td>
          </tr>
          <tr>
            <td style="padding:28px;">
              <table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:13px;border-collapse:collapse;">
                @if($booking->booking_type === 'hotel')
                  <tr><td style="color:#888;width:160px;">Hotel</td><td>{{ $booking->hotel->name ?? $booking->package_title }}</td></tr>
                  <tr><td style="color:#888;">Room Type</td><td>{{ $booking->room_type_name }}</td></tr>
                @else
                  <tr><td style="color:#888;width:160px;">Package</td><td>{{ $booking->package_title }}</td></tr>
                @endif
                <tr><td style="color:#888;">Traveller</td><td>{{ $booking->traveller_name }}</td></tr>
                <tr><td style="color:#888;">Email</td><td>{{ $booking->traveller_email }}</td></tr>
                <tr><td style="color:#888;">Phone</td><td>{{ $booking->traveller_phone }}</td></tr>
                @if($booking->booking_type === 'hotel')
                  <tr><td style="color:#888;">Check-in</td><td>{{ $booking->check_in_date?->format('d M Y') }}</td></tr>
                  <tr><td style="color:#888;">Check-out</td><td>{{ $booking->check_out_date?->format('d M Y') }}</td></tr>
                  <tr><td style="color:#888;">Nights / Rooms</td><td>{{ $booking->nights }} night(s) &middot; {{ $booking->rooms }} room(s)</td></tr>
                @else
                  <tr><td style="color:#888;">Travel Date</td><td>{{ $booking->travel_date->format('d M Y') }}</td></tr>
                @endif
                <tr><td style="color:#888;">Travellers</td><td>{{ $booking->adults }} Adult(s){{ $booking->children ? ', '.$booking->children.' Child(ren)' : '' }}</td></tr>
                @php
                  $dayNumbers = $booking->hotels->pluck('day_number')->merge($booking->activities->pluck('day_number'))->filter()->unique()->sort()->values();
                @endphp
                @foreach($dayNumbers as $dayNumber)
                  @php
                    $dayHotels = $booking->hotels->where('day_number', $dayNumber);
                    $dayActivities = $booking->activities->where('day_number', $dayNumber);
                    $dayParts = $dayHotels->pluck('name')->concat($dayActivities->pluck('name'));
                  @endphp
                  <tr><td style="color:#888;">Day {{ $dayNumber }}</td><td>{{ $dayParts->join(', ') }}</td></tr>
                @endforeach
                @php
                  $generalNames = $booking->hotels->whereNull('day_number')->pluck('name')->concat($booking->activities->whereNull('day_number')->pluck('name'));
                @endphp
                @if($generalNames->isNotEmpty())
                  <tr><td style="color:#888;">Other Add-ons</td><td>{{ $generalNames->join(', ') }}</td></tr>
                @endif
                @if($booking->activities_total > 0)
                  <tr><td style="color:#888;">Activities Total</td><td>₹{{ number_format($booking->activities_total, 2) }}</td></tr>
                @endif
                @if($booking->hotels_total > 0)
                  <tr><td style="color:#888;">Hotels Total</td><td>₹{{ number_format($booking->hotels_total, 2) }}</td></tr>
                @endif
                <tr><td style="color:#888;">Total Paid</td><td><strong>₹{{ number_format($booking->total_amount, 2) }}</strong></td></tr>
                <tr><td style="color:#888;">Booked At</td><td>{{ $booking->created_at->format('d M Y, h:i A') }}</td></tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
