<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Confirmed</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;color:#111;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:24px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="580" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;">
          <tr>
            <td style="background:#005fcc;padding:20px 28px;">
              <span style="color:#ffffff;font-size:20px;font-weight:800;">Fare<span style="color:#f47b20;">Buzzer</span></span>
            </td>
          </tr>
          <tr>
            <td style="padding:28px;">
              <h2 style="margin:0 0 4px;font-size:18px;">Your booking is confirmed, {{ $booking->traveller_name }}!</h2>
              <p style="font-size:13px;color:#888;margin:0 0 20px;">Booking Reference: <strong style="color:#005fcc;">{{ $booking->booking_reference }}</strong></p>

              @if($booking->booking_type === 'hotel')
                <table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:13px;border-collapse:collapse;">
                  <tr><td style="color:#888;width:160px;">Hotel</td><td>{{ $booking->hotel->name ?? $booking->package_title }}</td></tr>
                  <tr><td style="color:#888;">Room Type</td><td>{{ $booking->room_type_name }}</td></tr>
                  <tr><td style="color:#888;">Check-in</td><td>{{ $booking->check_in_date?->format('d M Y') }}</td></tr>
                  <tr><td style="color:#888;">Check-out</td><td>{{ $booking->check_out_date?->format('d M Y') }}</td></tr>
                  <tr><td style="color:#888;">Nights / Rooms</td><td>{{ $booking->nights }} night(s) &middot; {{ $booking->rooms }} room(s)</td></tr>
                  <tr><td style="color:#888;">Guests</td><td>{{ $booking->adults }} Adult(s){{ $booking->children ? ', '.$booking->children.' Child(ren)' : '' }}</td></tr>
                </table>
                <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">
              @else
                <table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:13px;border-collapse:collapse;">
                  <tr><td style="color:#888;width:160px;">Package</td><td>{{ $booking->package_title }}</td></tr>
                  <tr><td style="color:#888;">Travel Date</td><td>{{ $booking->travel_date->format('d M Y') }}</td></tr>
                  @if($booking->departure_city)
                    <tr><td style="color:#888;">Departure City</td><td>{{ $booking->departure_city }}</td></tr>
                  @endif
                  @if($booking->room_type_name)
                    <tr><td style="color:#888;">Room Type</td><td>{{ $booking->room_type_name }}</td></tr>
                  @endif
                  <tr><td style="color:#888;">Travellers</td><td>{{ $booking->adults }} Adult(s){{ $booking->children ? ', '.$booking->children.' Child(ren)' : '' }}</td></tr>
                </table>

                <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">

                @include('bookings.partials._day_itinerary_table', ['booking' => $booking])
              @endif

              @if($booking->activities->isNotEmpty())
                <table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:13px;border-collapse:collapse;">
                  <tr><td colspan="2" style="font-weight:700;padding-bottom:2px;">Add-ons</td></tr>
                  @foreach($booking->activities as $bookingActivity)
                    <tr><td style="color:#888;">{{ $bookingActivity->name }} &times; {{ $bookingActivity->qty }}</td><td align="right">₹{{ number_format($bookingActivity->line_total, 2) }}</td></tr>
                  @endforeach
                </table>
                <hr style="border:none;border-top:1px solid #eee;margin:12px 0;">
              @endif

              @if($booking->hotels->isNotEmpty())
                <table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:13px;border-collapse:collapse;">
                  <tr><td colspan="2" style="font-weight:700;padding-bottom:2px;">Hotel</td></tr>
                  @foreach($booking->hotels as $bookingHotel)
                    <tr><td style="color:#888;">{{ $bookingHotel->name }} &times; {{ $bookingHotel->nights }} night(s)</td><td align="right">₹{{ number_format($bookingHotel->line_total, 2) }}</td></tr>
                  @endforeach
                </table>
                <hr style="border:none;border-top:1px solid #eee;margin:12px 0;">
              @endif

              <table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:13px;border-collapse:collapse;">
                <tr><td style="color:#888;">Base Fare</td><td align="right">₹{{ number_format($booking->base_fare, 2) }}</td></tr>
                @if($booking->discount_amount > 0)
                  <tr><td style="color:#16a34a;">Discount</td><td align="right" style="color:#16a34a;">−₹{{ number_format($booking->discount_amount, 2) }}</td></tr>
                @endif
                @if($booking->activities_total > 0)
                  <tr><td style="color:#888;">Add-ons Total</td><td align="right">₹{{ number_format($booking->activities_total, 2) }}</td></tr>
                @endif
                @if($booking->hotels_total > 0)
                  <tr><td style="color:#888;">Hotel Total</td><td align="right">₹{{ number_format($booking->hotels_total, 2) }}</td></tr>
                @endif
                @if($booking->coupon_code)
                  <tr><td style="color:#16a34a;">Coupon Discount ({{ $booking->coupon_code }})</td><td align="right" style="color:#16a34a;">−₹{{ number_format($booking->coupon_discount_amount, 2) }}</td></tr>
                @endif
                <tr><td style="color:#888;">Taxes &amp; Fees</td><td align="right">₹{{ number_format($booking->taxes_fee, 2) }}</td></tr>
                <tr><td style="font-weight:700;padding-top:10px;">Total Paid</td><td align="right" style="font-weight:700;color:#005fcc;padding-top:10px;">₹{{ number_format($booking->total_amount, 2) }}</td></tr>
              </table>

              <p style="font-size:12px;color:#aaa;margin-top:24px;">Your invoice/voucher is attached to this email. You can also download it anytime from My Bookings in your FareBuzzer dashboard.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
