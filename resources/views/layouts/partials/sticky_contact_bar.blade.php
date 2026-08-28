{{-- Mobile-only full-width sticky Call/WhatsApp bar. Desktop keeps the floating
     circular buttons (floating_buttons.blade.php) untouched. --}}
@if(config('app.contact_phone') || config('app.contact_whatsapp'))
  @php $waNumber = preg_replace('/[^0-9]/', '', config('app.contact_whatsapp') ?? config('app.contact_phone')); @endphp
  <style>
    .fb-sticky-bar { display: none; }
    @media (max-width: 767.98px) {
      .fb-sticky-bar {
        display: flex;
        position: fixed; left: 0; right: 0; bottom: 0; z-index: 1060;
        box-shadow: 0 -2px 12px rgba(0,0,0,.18);
        padding-bottom: env(safe-area-inset-bottom);
      }
      /* Reserve space so the bar never covers the footer or last section. */
      body:not(.hide-sticky-contact-bar) { padding-bottom: calc(58px + env(safe-area-inset-bottom)); }
      body.hide-sticky-contact-bar .fb-sticky-bar { display: none; }
    }
    .fb-sticky-btn {
      flex: 1 1 0; display: flex; align-items: center; justify-content: center; gap: 8px;
      min-height: 58px; font-size: 15px; font-weight: 700; color: #fff; text-decoration: none;
    }
    .fb-sticky-btn:hover, .fb-sticky-btn:focus { color: #fff; }
    .fb-sticky-btn.fb-sticky-call { background: #0a1929; }
    .fb-sticky-btn.fb-sticky-whatsapp { background: #25d366; }
  </style>
  <div class="fb-sticky-bar">
    @if(config('app.contact_phone'))
    <a href="tel:{{ config('app.contact_phone') }}" class="fb-sticky-btn fb-sticky-call" aria-label="Call us">
      <i class="bi bi-telephone-fill"></i> Call Now
    </a>
    @endif
    @if(config('app.contact_whatsapp'))
    <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener" class="fb-sticky-btn fb-sticky-whatsapp" aria-label="Chat on WhatsApp">
      <i class="bi bi-whatsapp"></i> WhatsApp
    </a>
    @endif
  </div>
@endif
