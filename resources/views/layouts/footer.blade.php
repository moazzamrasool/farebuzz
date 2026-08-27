@php
  $footerSection = \App\Models\HomepageSection::forSite()->where('key', 'footer')->active()->first();
  $footerItems = $footerSection ? $footerSection->activeItems() : collect();
  $footerExtra = $footerSection->extra ?? [];
  $footerGroup = fn (string $key) => $footerItems->where('group_key', $key);
  $hasUrl = fn (?string $url) => $url && $url !== '#';
  $socials = [
    ['icon' => 'bi-facebook',   'url' => $footerExtra['social_facebook'] ?? null],
    ['icon' => 'bi-twitter-x',  'url' => $footerExtra['social_twitter'] ?? null],
    ['icon' => 'bi-instagram',  'url' => $footerExtra['social_instagram'] ?? null],
    ['icon' => 'bi-youtube',    'url' => $footerExtra['social_youtube'] ?? null],
  ];
@endphp
<footer class="site-footer py-5">
  <div class="container">
    <div class="row g-4 mb-4">
      <div class="col-12 col-md-3">
        <div class="footer-logo mb-2"><img src="{{ asset('frontend/img/logo.png') }}" alt="FareBuzzer"></div>
        <p style="font-size:12px;color:#aaa;line-height:1.6;">{{ $footerExtra['blurb'] ?? '' }}</p>
        @if(collect($socials)->contains(fn ($s) => $hasUrl($s['url'])))
        <div class="d-flex gap-3 mt-3">
          @foreach($socials as $s)
            @if($hasUrl($s['url']))
              <a href="{{ $s['url'] }}" target="_blank" rel="noopener noreferrer" style="color:#aaa;font-size:18px;"><i class="bi {{ $s['icon'] }}"></i></a>
            @endif
          @endforeach
        </div>
        @endif

        @if(!empty($footerExtra['google_review_enabled']) && $hasUrl($footerExtra['google_review_url'] ?? null))
        <div class="mt-4 pt-3" style="border-top:1px solid #1e3a5f;">
          <h6 style="color:#fff;font-size:13px;font-weight:700;margin-bottom:6px;">Review us on Google</h6>
          <p style="font-size:12px;color:#aaa;margin-bottom:10px;">Enjoyed your trip? Share your experience</p>
          <div class="d-flex align-items-center gap-3 flex-wrap">
            <img src="{{ \App\Support\GoogleReviewQr::forUrl($footerSection->unique_id ?? 'default', $footerExtra['google_review_url']) }}"
                 alt="QR code to review {{ config('app.name') }} on Google"
                 width="64" height="64" style="background:#fff;padding:4px;border-radius:6px;flex-shrink:0;">
            <a href="{{ $footerExtra['google_review_url'] }}" target="_blank" rel="noopener"
               style="background:#005fcc;color:#fff;font-size:12px;font-weight:700;padding:8px 14px;border-radius:8px;text-decoration:none;white-space:nowrap;">
              Write a Review
            </a>
          </div>
        </div>
        @endif
      </div>
      <div class="col-6 col-md-2 footer-links">
        <h6>Company</h6>
        <ul>
          @foreach($footerGroup('company') as $link)
            <li><a href="{{ url($link->link ?? '#') }}">{{ $link->title }}</a></li>
          @endforeach
        </ul>
      </div>
      <div class="col-6 col-md-2 footer-links">
        <h6>Products</h6>
        <ul>
          @foreach($footerGroup('products') as $link)
            <li><a href="{{ $link->link && $link->link !== '#' ? url($link->link) : '#' }}">{{ $link->title }}</a></li>
          @endforeach
        </ul>
      </div>
      <div class="col-6 col-md-2 footer-links">
        <h6>Support</h6>
        <ul>
          @foreach($footerGroup('support') as $link)
            <li><a href="{{ $link->link && $link->link !== '#' ? url($link->link) : '#' }}">{{ $link->title }}</a></li>
          @endforeach
        </ul>
      </div>
      <div class="col-6 col-md-3 footer-links">
        <h6>Download App</h6>
        <p style="font-size:12px;color:#aaa;margin-bottom:10px;">Get the best deals on the go</p>
        @foreach($footerGroup('download_app') as $link)
          @php $icon = $link->meta['icon'] ?? 'apple'; $live = $hasUrl($link->link); @endphp
          @if($live)
            <a href="{{ $link->link }}" target="_blank" rel="noopener noreferrer" class="d-flex align-items-center gap-2 bg-dark text-white rounded p-2 mb-2 text-decoration-none" style="font-size:12px;max-width:160px;">
              <i class="bi bi-{{ $icon }}" style="font-size:20px;"></i>
              <div><div style="font-size:9px;opacity:.7;">{{ $icon === 'apple' ? 'Download on the' : 'Get it on' }}</div><div style="font-weight:700;">{{ $link->title }}</div></div>
            </a>
          @else
            <span class="d-flex align-items-center gap-2 bg-dark text-white rounded p-2 mb-2" style="font-size:12px;max-width:160px;opacity:.45;cursor:not-allowed;" aria-disabled="true" title="Coming soon">
              <i class="bi bi-{{ $icon }}" style="font-size:20px;"></i>
              <div><div style="font-size:9px;opacity:.7;">{{ $icon === 'apple' ? 'Coming soon on the' : 'Coming soon on' }}</div><div style="font-weight:700;">{{ $link->title }}</div></div>
            </span>
          @endif
        @endforeach
      </div>
    </div>
    <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
      <div>{{ $footerExtra['copyright_text'] ?? '' }}</div>
      <div class="d-flex gap-3 flex-wrap">
        @foreach($footerGroup('legal') as $link)
          <a href="{{ url($link->link ?? '#') }}" style="color:#888;text-decoration:none;font-size:12px;">{{ $link->title }}</a>
        @endforeach
      </div>
    </div>
  </div>
</footer>
