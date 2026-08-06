@php
  $footerSection = \App\Models\HomepageSection::forSite()->where('key', 'footer')->active()->first();
  $footerItems = $footerSection ? $footerSection->activeItems() : collect();
  $footerExtra = $footerSection->extra ?? [];
  $footerGroup = fn (string $key) => $footerItems->where('group_key', $key);
@endphp
<footer class="site-footer py-5">
  <div class="container">
    <div class="row g-4 mb-4">
      <div class="col-12 col-md-3">
        <div class="footer-logo mb-2"><img src="{{ asset('frontend/img/logo.png') }}" alt="FareBuzzer"></div>
        <p style="font-size:12px;color:#aaa;line-height:1.6;">{{ $footerExtra['blurb'] ?? '' }}</p>
        <div class="d-flex gap-3 mt-3">
          <a href="{{ $footerExtra['social_facebook'] ?? '#' }}" style="color:#aaa;font-size:18px;"><i class="bi bi-facebook"></i></a>
          <a href="{{ $footerExtra['social_twitter'] ?? '#' }}" style="color:#aaa;font-size:18px;"><i class="bi bi-twitter-x"></i></a>
          <a href="{{ $footerExtra['social_instagram'] ?? '#' }}" style="color:#aaa;font-size:18px;"><i class="bi bi-instagram"></i></a>
          <a href="{{ $footerExtra['social_youtube'] ?? '#' }}" style="color:#aaa;font-size:18px;"><i class="bi bi-youtube"></i></a>
        </div>
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
          @php $icon = $link->meta['icon'] ?? 'apple'; @endphp
          <a href="{{ $link->link ?? '#' }}" class="d-flex align-items-center gap-2 bg-dark text-white rounded p-2 mb-2 text-decoration-none" style="font-size:12px;max-width:160px;">
            <i class="bi bi-{{ $icon }}" style="font-size:20px;"></i>
            <div><div style="font-size:9px;opacity:.7;">{{ $icon === 'apple' ? 'Download on the' : 'Get it on' }}</div><div style="font-weight:700;">{{ $link->title }}</div></div>
          </a>
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
