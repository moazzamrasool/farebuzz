<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Itinerary — {{ $package->title }}</title>
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

    .day-block { margin-bottom: 14px; }
    .day-header { font-weight: bold; background: #f5f5f5; padding: 6px 8px; font-size: 12px; }
    .day-route { color: #666; font-size: 11px; padding: 4px 8px 0; }
    .day-detail { padding: 6px 8px; font-size: 11px; line-height: 1.6; }
    .day-bullets { padding: 0 8px 4px 22px; font-size: 11px; }
    .day-bullets li { margin-bottom: 2px; }
    .meal-tag { display: inline-block; background: #eef4ff; color: #005fcc; border-radius: 10px; padding: 2px 8px; font-size: 10px; margin: 2px 4px 0 0; }

    .footer-note { margin-top: 30px; font-size: 10px; color: #999; text-align: center; }
  </style>
</head>
<body>

  <table class="header-table">
    <tr>
      <td><span class="brand">Fare<span>Buzzer</span></span></td>
      <td>
        <div class="doc-title">TRIP ITINERARY</div>
        <div class="ref">{{ $referenceLabel }}: {{ $referenceValue }}</div>
        <div class="ref">Date: {{ now()->format('d M Y') }}</div>
      </td>
    </tr>
  </table>

  <div class="section-title">Trip Details</div>
  <table class="info-table">
    <tr><td class="label">Package</td><td>{{ $package->title }}</td></tr>
    @if($travellerName)
      <tr><td class="label">Traveller</td><td>{{ $travellerName }}</td></tr>
    @endif
    @if($travelDate)
      <tr><td class="label">Travel Date</td><td>{{ $travelDate->format('d M Y') }}</td></tr>
    @endif
  </table>

  <div class="section-title">Day-by-Day Itinerary</div>
  @include('pdf.partials._itinerary_days', ['itineraries' => $package->itineraries])

  <div class="footer-note">
    This itinerary is indicative and subject to change based on availability. For any queries, please contact our support team.
  </div>

</body>
</html>
