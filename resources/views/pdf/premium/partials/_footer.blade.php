{{--
  Branded bottom bar, identical on every generated document (itinerary and
  quotation) so the template reads as one system. Expects $note (the
  document-specific disclaimer line, e.g. "this itinerary is indicative...").
  Contact numbers come from config('app.contact_phone'/'contact_whatsapp') —
  the same values the public site's Call/WhatsApp buttons already use.
  The actual "Page X of Y" text is NOT in this markup: dompdf has no CSS-level
  page-number substitution, so it's drawn separately onto the right side of
  this bar via App\Support\PdfPageNumbers::apply() (Canvas::page_text()) after
  render() — see ItineraryService/QuotationService.
--}}
<div class="brand-footer">
  <span class="contact">
    <strong>FAREBUZZER</strong>
    @if(config('app.contact_phone')) &middot; Call: {{ config('app.contact_phone') }} @endif
    @if(config('app.contact_whatsapp')) &middot; WhatsApp: {{ config('app.contact_whatsapp') }} @endif
    &middot; www.farebuzzertravel.com
  </span>
  <div style="font-size: 8px; color: #8fa0bd; margin-top: 2px;">{{ $note }}</div>
</div>
