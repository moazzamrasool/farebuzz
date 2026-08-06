{{-- Shared hotel card. Expects: $hotel with amenities + roomTypes loaded (or lazy-loads them). --}}
<a href="{{ route('hotels.show', array_merge(['hotel' => $hotel->slug], request()->only(['checkin', 'checkout', 'rooms']))) }}" class="text-decoration-none">
  <div class="hotel-card">
    <div class="hotel-card-img">
      <img src="{{ \App\Support\MediaUrl::resolve($hotel->cover_image) ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=70' }}" alt="{{ $hotel->name }}">
    </div>
    <div class="hotel-card-body">
      <div class="hotel-card-title">{{ $hotel->name }}</div>
      <div class="hotel-card-star">
        @for($i = 0; $i < $hotel->star_rating; $i++)<i class="bi bi-star-fill"></i>@endfor
      </div>
      <div class="hotel-card-address"><i class="bi bi-geo-alt-fill"></i> {{ $hotel->destination?->name ?? $hotel->address }}</div>
      <div>
        @foreach($hotel->amenities->take(3) as $amenity)
          <span class="hotel-amenity-pill"><i class="bi bi-{{ $amenity->icon ?: 'check2' }}"></i> {{ $amenity->name }}</span>
        @endforeach
      </div>
      <div class="hotel-card-footer mt-2">
        @if($hotel->rating_score)
          <span class="hotel-score">{{ $hotel->rating_score }} &middot; {{ $hotel->review_count }} reviews</span>
        @else
          <span></span>
        @endif
        <div class="text-end">
          @if($hotel->fromPrice)
            <div class="hotel-price-from">Starting from</div>
            <div class="hotel-price-value">₹{{ number_format($hotel->fromPrice) }}</div>
            <div class="hotel-price-from">per night + taxes</div>
          @endif
        </div>
      </div>
    </div>
  </div>
</a>
