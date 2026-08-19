@if(config('app.contact_phone') || config('app.contact_whatsapp'))
  @php $waNumber = preg_replace('/[^0-9]/', '', config('app.contact_whatsapp') ?? config('app.contact_phone')); @endphp
  <style>
    .fb-floating-actions { position: fixed; left: 20px; bottom: 20px; z-index: 1050; display: flex; flex-direction: column; gap: 12px; }
    .fb-floating-btn {
      width: 54px; height: 54px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
      color: #fff; font-size: 24px; box-shadow: 0 4px 14px rgba(0,0,0,.25); transition: transform .15s, box-shadow .15s;
    }
    .fb-floating-btn:hover { color: #fff; transform: scale(1.08); box-shadow: 0 6px 18px rgba(0,0,0,.3); }
    .fb-floating-btn.fb-whatsapp { background: #25d366; }
    .fb-floating-btn.fb-call { background: #005fcc; }
    @media (max-width: 576px) {
      .fb-floating-actions { left: 14px; bottom: 14px; }
      .fb-floating-btn { width: 48px; height: 48px; font-size: 20px; }
    }
  </style>
  <div class="fb-floating-actions">
    @if(config('app.contact_whatsapp'))
    <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener" class="fb-floating-btn fb-whatsapp" title="Chat on WhatsApp" aria-label="Chat on WhatsApp">
      <i class="bi bi-whatsapp"></i>
    </a>
    @endif
    @if(config('app.contact_phone'))
    <a href="tel:{{ config('app.contact_phone') }}" class="fb-floating-btn fb-call" title="Call us" aria-label="Call us">
      <i class="bi bi-telephone-fill"></i>
    </a>
    @endif
  </div>
@endif
