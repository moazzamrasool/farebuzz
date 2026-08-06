<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Quotation {{ $quotation->quotation_number }}</title>
  <style>
    /* dompdf renders plain CSS2.1-ish — no flexbox/grid, table-based like pdf/booking_invoice.blade.php */
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
    .header-table { width: 100%; margin-bottom: 20px; }
    .brand { font-size: 22px; font-weight: bold; color: #005fcc; }
    .brand span { color: #f47b20; }
    .doc-title { font-size: 16px; font-weight: bold; text-align: right; }
    .ref { text-align: right; color: #666; font-size: 11px; }

    .section-title { font-size: 12px; font-weight: bold; color: #005fcc; border-bottom: 1px solid #ddd; padding-bottom: 4px; margin: 18px 0 8px; }
    table.info-table { width: 100%; border-collapse: collapse; }
    table.info-table td { padding: 4px 0; vertical-align: top; }
    table.info-table td.label { color: #888; width: 150px; }

    table.price-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    table.price-table th { text-align: left; background: #f5f5f5; padding: 6px 8px; font-size: 11px; }
    table.price-table td { padding: 6px 8px; border-bottom: 1px solid #eee; font-size: 11px; }
    table.price-table td.amount, table.price-table th.amount { text-align: right; }
    .total-row td { font-weight: bold; font-size: 13px; border-top: 2px solid #333; color: #005fcc; }

    .day-block { margin-bottom: 14px; }
    .day-header { font-weight: bold; background: #f5f5f5; padding: 6px 8px; font-size: 12px; }
    .day-route { color: #666; font-size: 11px; padding: 4px 8px 0; }
    .day-detail { padding: 6px 8px; font-size: 11px; line-height: 1.6; }
    .day-bullets { padding: 0 8px 4px 22px; font-size: 11px; }
    .day-bullets li { margin-bottom: 2px; }
    .meal-tag { display: inline-block; background: #eef4ff; color: #005fcc; border-radius: 10px; padding: 2px 8px; font-size: 10px; margin: 2px 4px 0 0; }

    .notes-box { background: #fafbff; border: 1px solid #e7ebf3; border-radius: 6px; padding: 10px 12px; font-size: 11px; line-height: 1.6; margin-top: 8px; }
    .footer-note { margin-top: 30px; font-size: 10px; color: #999; text-align: center; }
  </style>
</head>
<body>

  <table class="header-table">
    <tr>
      <td><span class="brand">Fare<span>Buzzer</span></span></td>
      <td>
        <div class="doc-title">QUOTATION</div>
        <div class="ref">Quotation No: {{ $quotation->quotation_number }}</div>
        <div class="ref">Date: {{ $quotation->created_at->format('d M Y') }}</div>
        @if($quotation->valid_until)
          <div class="ref">Valid Until: {{ $quotation->valid_until->format('d M Y') }}</div>
        @endif
      </td>
    </tr>
  </table>

  <div class="section-title">Customer Details</div>
  <table class="info-table">
    <tr><td class="label">Name</td><td>{{ $quotation->customer_name }}</td></tr>
    <tr><td class="label">Email</td><td>{{ $quotation->customer_email }}</td></tr>
    @if($quotation->customer_phone)
      <tr><td class="label">Phone</td><td>{{ $quotation->customer_phone }}</td></tr>
    @endif
    @if($quotation->holidayPackage)
      <tr><td class="label">Package</td><td>{{ $quotation->holidayPackage->title }}</td></tr>
    @endif
  </table>

  <div class="section-title">Price Breakdown</div>
  <table class="price-table">
    <thead>
      <tr>
        <th>Description</th>
        <th class="amount">Qty</th>
        <th class="amount">Unit Price (INR)</th>
        <th class="amount">Amount (INR)</th>
      </tr>
    </thead>
    <tbody>
      @foreach($quotation->items as $item)
        <tr>
          <td>{{ $item->description }}</td>
          <td class="amount">{{ $item->quantity }}</td>
          <td class="amount">₹{{ number_format($item->unit_price, 2) }}</td>
          <td class="amount">₹{{ number_format($item->amount, 2) }}</td>
        </tr>
      @endforeach
      <tr class="total-row">
        <td colspan="3">Total</td>
        <td class="amount">₹{{ number_format($quotation->total_amount, 2) }}</td>
      </tr>
    </tbody>
  </table>

  @if($quotation->holidayPackage && $quotation->holidayPackage->itineraries->isNotEmpty())
    <div class="section-title">Day-by-Day Itinerary</div>
    @include('pdf.partials._itinerary_days', ['itineraries' => $quotation->holidayPackage->itineraries])
  @endif

  @if($quotation->notes)
    <div class="section-title">Notes &amp; Terms</div>
    <div class="notes-box">{!! nl2br(e($quotation->notes)) !!}</div>
  @endif

  <div class="footer-note">
    This quotation is indicative and subject to change based on availability at the time of booking. For any queries, please contact our support team.
  </div>

</body>
</html>
