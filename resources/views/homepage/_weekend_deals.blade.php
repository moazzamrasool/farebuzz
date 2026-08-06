@php $items = $section->activeItems(); @endphp
<section class="bg-white-section section-pad">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-1">
      <h2 class="section-title mb-0">{{ $section->heading }}</h2>
    </div>
    @if($section->extra['date_range_text'] ?? null)
      <p class="text-muted mb-3" style="font-size:13px;">{{ $section->extra['date_range_text'] }}</p>
    @endif

    <div class="deals-scroll-wrapper">
      <!-- Scroll buttons -->
      <button class="deals-scroll-btn deals-prev" id="deals-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="deals-scroll-btn deals-next" id="deals-next"><i class="bi bi-chevron-right"></i></button>

      <div class="deals-grid-scroll" id="dealsGrid">
        @foreach($items as $item)
          @php
            $genius = (bool) ($item->meta['genius'] ?? false);
            $dealLabel = $item->meta['deal_label'] ?? null;
            $nights = $item->meta['nights'] ?? null;
          @endphp
          <a href="{{ \App\Support\MediaUrl::link($item->link) }}" class="acc-card text-decoration-none">
            <div class="acc-img-wrap">
              <img src="{{ \App\Support\MediaUrl::resolve($item->image) }}" alt="{{ $item->title }}"/>
              <button class="wishlist-btn" type="button"><i class="bi bi-heart"></i></button>
            </div>
            <div class="acc-card-body">
              @if($genius)
                <div class="acc-badge-row">
                  <span class="genius-badge">Genius</span>
                </div>
              @endif
              <h6 class="acc-title">{{ $item->title }}</h6>
              <div class="acc-location">{{ $item->subtitle }}</div>
              @if($item->rating)
                <div class="acc-rating-row">
                  <div class="acc-rating-badge">{{ number_format($item->rating, 1) }}</div>
                  <div class="acc-rating-text">
                    <span class="acc-rating-label">{{ \App\Support\RatingLabel::forScore($item->rating) }}</span>
                    <span class="acc-rating-count">{{ $item->review_count }} reviews</span>
                  </div>
                </div>
              @endif
              @if($dealLabel)
                <span class="acc-deal-badge">{{ $dealLabel }}</span>
              @endif
              <div class="acc-price-row">
                <span class="acc-price-label">{{ $nights ? $nights.' nights' : 'Starting from' }}</span>
                @if($item->old_price)
                  <span class="acc-price-old">₹{{ number_format($item->old_price) }}</span>
                @endif
                <span class="acc-price-new">₹{{ number_format($item->price) }}</span>
              </div>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </div>
</section>
