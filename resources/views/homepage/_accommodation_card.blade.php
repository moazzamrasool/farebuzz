@php
  $stars = (int) ($item->meta['stars'] ?? 0);
  $genius = (bool) ($item->meta['genius'] ?? false);
  $distanceText = $item->meta['distance_text'] ?? null;
@endphp
<a href="{{ \App\Support\MediaUrl::link($item->link) }}" class="acc-card text-decoration-none">
  <div class="acc-img-wrap">
    <img src="{{ \App\Support\MediaUrl::resolve($item->image) }}" alt="{{ $item->title }}"/>
    <button class="wishlist-btn" type="button"><i class="bi bi-heart"></i></button>
  </div>
  <div class="acc-card-body">
    <div class="acc-badge-row">
      @if($item->label)
        <span class="acc-type">{{ $item->label }}</span>
      @endif
      @if($stars > 0)
        <span class="acc-stars">
          @for($i = 0; $i < $stars; $i++)
            <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
          @endfor
        </span>
      @endif
      @if($genius)
        <span class="genius-badge">Genius</span>
      @endif
    </div>
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
    @if($distanceText)
      <div class="acc-distance">
        <i class="bi bi-geo-alt-fill"></i>
        <span>{{ $distanceText }}</span>
      </div>
    @endif
    <div class="acc-price-row">
      <span class="acc-price-label">Starting from</span>
      @if($item->old_price)
        <span class="acc-price-old">₹{{ number_format($item->old_price) }}</span>
      @endif
      <span class="acc-price-new">₹{{ number_format($item->price) }}</span>
    </div>
  </div>
</a>
