{{--
  Shared style system for the premium brand: Orange #FF6B1A / Navy #0B2545 /
  cream #FAF9F6. Included by both pdf.premium.itinerary and pdf.premium.quotation
  so every generated document looks like one consistent template, not a one-off
  per document type.
  dompdf (CSS2.1+ subset, v3) supports background-image/size/position,
  linear-gradient backgrounds and position:fixed headers/footers; it does not
  support flexbox/grid, so layout stays table/block based like the classic templates.
--}}
<style>
  @page { margin: 0; size: a4 landscape; }
  body { margin: 0; font-family: 'DejaVu Sans', sans-serif; color: #222; padding-top: 34px; padding-bottom: 36px; }

  .brand-header {
    position: fixed; top: 0; left: 0; right: 0; height: 34px;
    background: #0B2545; color: #FAF9F6; font-size: 11px; font-weight: bold;
    letter-spacing: 1px; padding: 10px 40px 0;
  }
  .brand-header img { height: 16px; vertical-align: middle; margin-right: 8px; }
  .brand-footer {
    position: fixed; bottom: 0; left: 0; right: 0; height: 34px;
    background: #0B2545; color: #FAF9F6; font-size: 9px; padding: 6px 40px 0;
  }
  .brand-footer .contact { color: #FAF9F6; }
  .brand-footer .contact strong { color: #FF6B1A; }

  .section-title { font-size: 20px; font-weight: bold; color: #0B2545; padding: 26px 44px 4px; }
  .kicker { font-size: 11px; letter-spacing: 2px; color: #FF6B1A; font-weight: bold; padding: 0 44px; }

  table.two-col { width: 100%; }
  table.two-col td { width: 50%; vertical-align: top; padding: 0 44px 24px; }
  ul.plain { margin: 6px 0 0; padding-left: 18px; font-size: 12px; line-height: 1.8; }

  .hotel-card { border: 1px solid #e5e7eb; border-radius: 6px; margin: 0 44px 16px; overflow: hidden; }
  {{--
    contain (not cover) on purpose: hotel cover images are frequently seed/demo
    placeholders with the hotel name baked into the pixels (HotelSeeder's
    placeholderImage()) rather than real photography. Cropping those with
    "cover" clips the baked-in text; "contain" always shows the whole banner,
    letterboxed on the navy backdrop when its aspect ratio doesn't match — and
    is equally safe for a real photo once one is uploaded.
  --}}
  .hotel-card .thumb { height: 150px; background-color: #0B2545; background-size: contain; background-repeat: no-repeat; background-position: center; }
  .hotel-card .body { padding: 10px 16px; }
  .hotel-card .name { font-size: 13px; font-weight: bold; color: #0B2545; }
  .hotel-card .stars { color: #FF6B1A; font-size: 11px; }
  .hotel-card .meta { font-size: 11px; color: #6b7280; margin-top: 2px; }

  .feature-box { width: 46%; display: inline-block; vertical-align: top; border-radius: 6px; padding: 14px 18px; margin: 0 2% 20px; }
  .feature-box.inclusion { border: 1px solid #bbf7d0; background: #f0fdf4; }
  .feature-box.exclusion { border: 1px solid #fecaca; background: #fef2f2; }
  .feature-box h4 { margin: 0 0 8px; font-size: 13px; }
  .feature-box.inclusion h4 { color: #16a34a; }
  .feature-box.exclusion h4 { color: #dc2626; }
  .feature-box ul { margin: 0; padding-left: 16px; font-size: 11px; line-height: 1.8; }

  .cover-collage { width: 100%; }
  .cover-collage td { width: 33.333%; padding: 0 2px; }
  .cover-collage .tile { height: 230px; background-size: cover; background-position: center; }

  .day-word { font-size: 24px; font-weight: bold; color: #0B2545; vertical-align: middle; }
  .day-number { font-size: 64px; font-weight: bold; color: #FF6B1A; line-height: 1; vertical-align: middle; }

  table.day-supporting { width: 100%; margin: 8px 0 0; border-collapse: separate; border-spacing: 6px 0; }
  table.day-supporting td { width: 50%; }
  {{-- contain, not cover: same reasoning as .hotel-card .thumb above — a day's
       supporting image can be a linked Activity's photo, and those are seeded
       the same way (name baked into the pixels), so cropping would clip text. --}}
  .day-supporting .tile { height: 76px; background-color: #0B2545; background-size: contain; background-repeat: no-repeat; background-position: center; border-radius: 4px; }

  table.price-table { width: 100%; border-collapse: collapse; margin: 0 44px; width: calc(100% - 88px); }
  table.price-table th { text-align: left; background: #f5f5f5; padding: 8px 10px; font-size: 11px; color: #0B2545; }
  table.price-table td { padding: 8px 10px; border-bottom: 1px solid #eee; font-size: 11px; }
  table.price-table td.amount, table.price-table th.amount { text-align: right; }
  .total-row td { font-weight: bold; font-size: 13px; border-top: 2px solid #0B2545; color: #0B2545; }
  .notes-box { margin: 8px 44px 0; background: #fafbff; border: 1px solid #e7ebf3; border-radius: 6px; padding: 12px 16px; font-size: 11px; line-height: 1.6; }
</style>
