{{--
  Branded fallback used wherever PdfImage::resolve() returned null (no photo on
  file for this package/hotel/destination). Pure CSS — no image file dependency —
  so a package with zero photos still renders a clean brochure instead of a
  broken <img> or a missing render.
  Expects: $height (px), optional $label (e.g. destination/day title).
  Font size scales with $height so a small slot (e.g. a hotel thumb) never
  overflows its box the way a fixed font size would.
--}}
@php
  $label = $label ?? null;
  $fontSize = max(10, min(22, (int) round($height / 11)));
  $showKicker = $height >= 140;
@endphp
<div style="height: {{ $height }}px; background: linear-gradient(135deg, #0B2545, #163a63); display: table; width: 100%;">
  <div style="display: table-cell; vertical-align: middle; text-align: center; padding: 0 16px;">
    @if($showKicker)
      <div style="font-family: 'DejaVu Sans', sans-serif; font-size: 13px; letter-spacing: 2px; color: #FF6B1A; font-weight: bold;">FAREBUZZER</div>
    @endif
    @if($label)
      <div style="font-family: 'DejaVu Sans', sans-serif; font-size: {{ $fontSize }}px; line-height: 1.25; color: #FAF9F6; margin-top: {{ $showKicker ? 6 : 0 }}px; word-wrap: break-word;">{{ $label }}</div>
    @endif
  </div>
</div>
