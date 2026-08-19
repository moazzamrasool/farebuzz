@php $items = $section->activeItems()->loadMissing('coupon'); @endphp
<section class="bg-white-section section-pad">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
      <div class="d-flex align-items-center gap-4 flex-wrap">
        <h2 class="section-title mb-0" style="font-size: 24px; font-weight: 800; color: #111;">{{ $section->heading }}</h2>
        <div class="offers-tabs">
          <button class="active" data-filter="all">All Offers</button>
          <button data-filter="flights">Flights</button>
          <button data-filter="hotels">Hotels</button>
          <button data-filter="holidays">Holidays</button>
          <button data-filter="trains">Trains</button>
          <button data-filter="cabs">Cabs</button>
        </div>
      </div>
      <div class="d-flex align-items-center gap-3">
        <a href="#" class="view-all-link text-primary fw-bold text-decoration-none" style="font-size: 13px;">VIEW ALL &rarr;</a>
        <div class="carousel-nav" style="display: flex; gap: 8px;">
          <button class="carousel-btn" id="offers-prev" style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid #dde3f0; background: #fff; display: flex; align-items: center; justify-content: center; color: #0084ff;"><i class="bi bi-chevron-left"></i></button>
          <button class="carousel-btn" id="offers-next" style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid #dde3f0; background: #fff; display: flex; align-items: center; justify-content: center; color: #0084ff;"><i class="bi bi-chevron-right"></i></button>
        </div>
      </div>
    </div>
    <div class="offers-grid-scroll" id="offersGrid">
      @foreach($items as $item)
        <div class="offer-card" data-category="{{ $item->group_key ?? 'all' }}">
          <div class="offer-img-wrap">
            <img src="{{ \App\Support\MediaUrl::resolve($item->image) }}" alt="{{ $item->title }}"/>
          </div>
          <div class="card-body">
            <div class="offer-header-row">
              <span class="offer-tag">{{ $item->label }}</span>
              <span class="offer-tc">T&amp;C'S APPLY</span>
            </div>
            <div>
              <h6 class="offer-title">{{ $item->title }}</h6>
              <div class="offer-accent-line"></div>
              <p class="offer-desc">{{ $item->description }}</p>
              @if($item->coupon)
                <button type="button" class="offer-coupon-code" data-copy-code="{{ $item->coupon->code }}"
                  style="border:1px dashed #0084ff;border-radius:4px;padding:4px 10px;display:inline-block;font-weight:700;font-size:12px;color:#0084ff;margin-bottom:8px;background:#fff;cursor:pointer;">
                  <span class="offer-coupon-code-text">CODE: {{ $item->coupon->code }}</span>
                </button>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
