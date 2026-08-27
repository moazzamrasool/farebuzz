<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Quotation {{ $quotation->quotation_number }}</title>
  @include('pdf.premium.partials._styles')
</head>
<body>

  <div class="brand-header">
    @if($logo)<img src="{{ $logo }}">@endif FAREBUZZER
  </div>
  @include('pdf.premium.partials._footer', ['note' => 'This quotation is indicative and subject to change based on availability at the time of booking.'])

  {{-- Cover --}}
  <div style="page-break-after: always;">
    @if($coverImage)
      <div style="height: 340px; background-image: url('{{ $coverImage }}'); background-size: cover; background-position: center;"></div>
    @else
      @include('pdf.premium.partials._placeholder', ['height' => 340, 'label' => $package?->destination?->name ?? $package?->title])
    @endif
    <div style="padding: 30px 44px;">
      <div class="kicker">QUOTATION</div>
      <div style="font-size: 34px; font-weight: bold; color: #0B2545; margin-top: 4px;">{{ $package?->title ?? 'Your Trip' }}</div>
      <div style="font-size: 14px; color: #6b7280; margin-top: 8px;">Prepared for {{ $quotation->customer_name }}</div>
      <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">
        Quotation No: {{ $quotation->quotation_number }} &middot;
        Date: {{ $quotation->created_at->format('d M Y') }}
        @if($quotation->valid_until) &middot; Valid Until: {{ $quotation->valid_until->format('d M Y') }}@endif
      </div>
    </div>
  </div>

  {{-- Price breakdown --}}
  <div style="page-break-after: always;">
    <div class="kicker">COST</div>
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
    @if($quotation->notes)
      <div style="padding: 0 44px; margin-top: 16px;">
        <strong style="font-size: 12px; color: #0B2545;">Notes &amp; Terms</strong>
        <div class="notes-box" style="margin-left: 0; margin-right: 0;">{!! nl2br(e($quotation->notes)) !!}</div>
      </div>
    @endif
  </div>

  @if($package)
    @include('pdf.premium.partials._package_pages', [
      'package' => $package, 'days' => $days, 'hotels' => $hotels,
      'inclusions' => $inclusions, 'exclusions' => $exclusions,
    ])
  @endif

</body>
</html>
