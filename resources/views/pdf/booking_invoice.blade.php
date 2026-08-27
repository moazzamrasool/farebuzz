<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice {{ $booking->booking_reference }}</title>
  <style>
    /* dompdf renders plain CSS2.1-ish — no flexbox/grid, so this is deliberately table-based. */
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
    .header-table { width: 100%; margin-bottom: 20px; }
    .brand { font-size: 22px; font-weight: bold; color: #005fcc; }
    .brand span { color: #f47b20; }
    .doc-title { font-size: 16px; font-weight: bold; text-align: right; }
    .ref { text-align: right; color: #666; font-size: 11px; }
    .status-badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 10px; font-weight: bold; color: #fff; background: {{ $booking->status === 'confirmed' ? '#16a34a' : '#dc2626' }}; }

    .section-title { font-size: 12px; font-weight: bold; color: #005fcc; border-bottom: 1px solid #ddd; padding-bottom: 4px; margin: 18px 0 8px; }
    table.info-table { width: 100%; border-collapse: collapse; }
    table.info-table td { padding: 4px 0; vertical-align: top; }
    table.info-table td.label { color: #888; width: 150px; }

    table.price-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    table.price-table th { text-align: left; background: #f5f5f5; padding: 6px 8px; font-size: 11px; }
    table.price-table td { padding: 6px 8px; border-bottom: 1px solid #eee; font-size: 11px; }
    table.price-table td.amount, table.price-table th.amount { text-align: right; }
    .total-row td { font-weight: bold; font-size: 13px; border-top: 2px solid #333; color: #005fcc; }

    .footer-note { margin-top: 30px; font-size: 10px; color: #999; text-align: center; }
  </style>
</head>
<body>

  <table class="header-table">
    <tr>
      <td><span class="brand">Fare<span>Buzzer</span></span></td>
      <td>
        <div class="doc-title">BOOKING INVOICE / VOUCHER</div>
        <div class="ref">Reference: {{ $booking->booking_reference }}</div>
        <div class="ref">Date: {{ $booking->created_at->format('d M Y') }}</div>
        <div class="ref"><span class="status-badge">{{ strtoupper($booking->status) }}</span></div>
      </td>
    </tr>
  </table>

  <div class="section-title">Traveller Details</div>
  <table class="info-table">
    <tr><td class="label">Name</td><td>{{ $booking->traveller_name }}</td></tr>
    <tr><td class="label">Email</td><td>{{ $booking->traveller_email }}</td></tr>
    <tr><td class="label">Phone</td><td>{{ $booking->traveller_phone }}</td></tr>
    <tr><td class="label">Address</td><td>{{ $booking->traveller_address }}</td></tr>
    @if($booking->gst_number)
      <tr><td class="label">GST Number</td><td>{{ $booking->gst_number }}</td></tr>
    @endif
  </table>

  @if($booking->booking_type === 'hotel')
    <div class="section-title">Hotel Details</div>
    <table class="info-table">
      <tr><td class="label">Hotel</td><td>{{ $booking->hotel->name ?? $booking->package_title }}</td></tr>
      <tr><td class="label">Room Type</td><td>{{ $booking->room_type_name }}</td></tr>
      @if($booking->room_type_bed_type)
        <tr><td class="label">Bed Type</td><td>{{ $booking->room_type_bed_type->label() }}</td></tr>
      @endif
      <tr><td class="label">Check-in</td><td>{{ $booking->check_in_date?->format('d M Y') }}</td></tr>
      <tr><td class="label">Check-out</td><td>{{ $booking->check_out_date?->format('d M Y') }}</td></tr>
      <tr><td class="label">Nights / Rooms</td><td>{{ $booking->nights }} night(s) &middot; {{ $booking->rooms }} room(s)</td></tr>
      <tr><td class="label">Guests</td><td>{{ $booking->adults }} Adult(s){{ $booking->children ? ', '.$booking->children.' Child(ren)' : '' }}</td></tr>
      @if($booking->special_requests)
        <tr><td class="label">Special Requests</td><td>{{ $booking->special_requests }}</td></tr>
      @endif
    </table>
  @else
    <div class="section-title">Package Details</div>
    <table class="info-table">
      <tr><td class="label">Package</td><td>{{ $booking->package_title }}</td></tr>
      <tr><td class="label">Travel Date</td><td>{{ $booking->travel_date->format('d M Y') }}</td></tr>
      @if($booking->departure_city)
        <tr><td class="label">Departure City</td><td>{{ $booking->departure_city }}</td></tr>
      @endif
      @if($booking->room_type_name)
        <tr><td class="label">Room Type</td><td>{{ $booking->room_type_name }}</td></tr>
      @endif
      <tr><td class="label">Travellers</td><td>{{ $booking->adults }} Adult(s){{ $booking->children ? ', '.$booking->children.' Child(ren)' : '' }}</td></tr>
      @if($booking->special_requests)
        <tr><td class="label">Special Requests</td><td>{{ $booking->special_requests }}</td></tr>
      @endif
    </table>

    @include('bookings.partials._day_itinerary_table', ['booking' => $booking])
  @endif

  @if($booking->activities->isNotEmpty())
    <div class="section-title">Add-ons</div>
    <table class="price-table">
      <thead><tr><th>Activity</th><th class="amount">Unit Price</th><th class="amount">Qty</th><th class="amount">Amount (INR)</th></tr></thead>
      <tbody>
        @foreach($booking->activities as $bookingActivity)
          <tr>
            <td>{{ $bookingActivity->name }}</td>
            <td class="amount">₹{{ number_format($bookingActivity->unit_price, 2) }}</td>
            <td class="amount">{{ $bookingActivity->qty }}</td>
            <td class="amount">₹{{ number_format($bookingActivity->line_total, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif

  @if($booking->hotels->isNotEmpty())
    <div class="section-title">Hotel</div>
    <table class="price-table">
      <thead><tr><th>Hotel</th><th class="amount">Unit Price / Night</th><th class="amount">Nights</th><th class="amount">Amount (INR)</th></tr></thead>
      <tbody>
        @foreach($booking->hotels as $bookingHotel)
          <tr>
            <td>{{ $bookingHotel->name }}</td>
            <td class="amount">₹{{ number_format($bookingHotel->unit_price, 2) }}</td>
            <td class="amount">{{ $bookingHotel->nights }}</td>
            <td class="amount">₹{{ number_format($bookingHotel->line_total, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif

  <div class="section-title">Price Breakdown</div>
  <table class="price-table">
    <thead><tr><th>Description</th><th class="amount">Amount (INR)</th></tr></thead>
    <tbody>
      <tr><td>Base Fare</td><td class="amount">₹{{ number_format($booking->base_fare, 2) }}</td></tr>
      @if($booking->discount_amount > 0)
        <tr><td>Discount</td><td class="amount">−₹{{ number_format($booking->discount_amount, 2) }}</td></tr>
      @endif
      @if($booking->activities_total > 0)
        <tr><td>Add-ons</td><td class="amount">₹{{ number_format($booking->activities_total, 2) }}</td></tr>
      @endif
      @if($booking->hotels_total > 0)
        <tr><td>Hotel</td><td class="amount">₹{{ number_format($booking->hotels_total, 2) }}</td></tr>
      @endif
      @if($booking->coupon_code)
        <tr><td>Coupon Discount ({{ $booking->coupon_code }})</td><td class="amount">−₹{{ number_format($booking->coupon_discount_amount, 2) }}</td></tr>
      @endif
      <tr><td>Taxes &amp; Fees</td><td class="amount">₹{{ number_format($booking->taxes_fee, 2) }}</td></tr>
      <tr class="total-row"><td>Total Paid</td><td class="amount">₹{{ number_format($booking->total_amount, 2) }}</td></tr>
    </tbody>
  </table>

  <div class="section-title">Payment Details</div>
  <table class="info-table">
    <tr><td class="label">Payment Status</td><td>{{ ucfirst($booking->payment_status) }}</td></tr>
    @if($payment)
      <tr><td class="label">Gateway</td><td>{{ strtoupper($payment->gateway) }} ({{ strtoupper($payment->mode) }} mode)</td></tr>
      <tr><td class="label">Transaction ID</td><td>{{ $payment->gateway_txn_id }}</td></tr>
      @if($payment->gateway_payment_id)
        <tr><td class="label">Payment ID</td><td>{{ $payment->gateway_payment_id }}</td></tr>
      @endif
    @endif
  </table>

  <div class="footer-note">
    This is a system-generated invoice/voucher for booking {{ $booking->booking_reference }}. For any queries, please contact our support team.
  </div>

</body>
</html>
