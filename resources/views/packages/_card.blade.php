{{--
  Shared holiday-package card. Everything shown here is derived from real
  package data (no fabricated badges) — see the notes below each block.
  Expects: $package with destination, photos, categories, hotels,
  inclusionFeatures, customInclusions loaded (or lazy-loads them).
--}}
@php
  $tagColors = ['beach', 'adventure', 'honeymoon', 'heritage', 'hill-station', 'mountains', 'international', 'family', 'luxury', 'mice'];

  $tags = [];
  if ($package->is_best_seller) {
    $tags[] = ['label' => 'Best Seller', 'class' => 'best-seller'];
  }
  foreach ($package->categories as $cat) {
    if (count($tags) >= 2) break;
    if (strtolower($cat->name) === 'best seller') continue; // avoid duplicating the flag above
    $slug = \Illuminate\Support\Str::slug($cat->name);
    $tags[] = ['label' => $cat->name, 'class' => in_array($slug, $tagColors) ? $slug : 'default'];
  }

  // "Includes" chips — derived from real relations/fields, not hardcoded per card.
  $inclusionText = strtolower($package->inclusionFeatures->pluck('title')->concat($package->customInclusions->pluck('title'))->join(' '));
  $includes = [];
  if (str_contains($inclusionText, 'flight')) $includes[] = ['icon' => 'airplane-fill', 'label' => 'Flights'];
  if ($package->hotels->isNotEmpty()) $includes[] = ['icon' => 'building', 'label' => 'Hotel'];
  if ($package->meals) $includes[] = ['icon' => 'cup-hot-fill', 'label' => 'Meals'];
  if (str_contains($inclusionText, 'transfer')) $includes[] = ['icon' => 'bus-front', 'label' => 'Transfers'];
  if (str_contains($inclusionText, 'rail') || str_contains($inclusionText, 'train')) $includes[] = ['icon' => 'train-front', 'label' => 'Rail Pass'];

  // Visa badge — only for packages whose inclusions actually mention visa support.
  $visaOnArrival = str_contains($inclusionText, 'visa') && str_contains($inclusionText, 'arrival');
  $visaIncluded = str_contains($inclusionText, 'visa') && !$visaOnArrival;

  $coverImage = $package->photos->firstWhere('is_cover', true)?->path ?? $package->photos->first()?->path;
@endphp
<div class="pkg-card">
  <div class="pkg-card-img-wrap">
    <img src="{{ \App\Support\MediaUrl::resolve($coverImage) ?? 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=70' }}" alt="{{ $package->title }}"/>
    <span class="duration-badge">{{ $package->days }}D / {{ $package->nights }}N</span>
    @if($visaOnArrival)
      <span class="visa-badge visa-on-arrival"><i class="bi bi-file-earmark-check-fill"></i> VISA ON ARRIVAL</span>
    @elseif($visaIncluded)
      <span class="visa-badge visa-assistance"><i class="bi bi-file-earmark-check-fill"></i> VISA ASSISTANCE INCLUDED</span>
    @endif
    @if(!empty($tags))
      <div class="pkg-card-tags">
        @foreach($tags as $tag)
          <span class="pkg-tag {{ $tag['class'] }}">{{ $tag['label'] }}</span>
        @endforeach
      </div>
    @endif
  </div>
  <div class="pkg-card-body">
    <div class="pkg-card-title">{{ $package->title }}</div>
    <div class="pkg-card-dest"><i class="bi bi-geo-alt-fill"></i> {{ $package->destination?->name }}{{ $package->destination?->country ? ', '.$package->destination->country : '' }}</div>

    @if($package->places_to_visit)
      <div class="pkg-card-places"><i class="bi bi-signpost-split"></i> {{ \Illuminate\Support\Str::limit($package->places_to_visit, 60) }}</div>
    @endif

    @if(!empty($includes))
      <div class="pkg-card-includes">
        @foreach($includes as $include)
          <span><i class="bi bi-{{ $include['icon'] }}"></i> {{ $include['label'] }}</span>
        @endforeach
      </div>
    @endif

    @if($package->averageRating)
      <div class="pkg-card-rating">
        @include('packages._stars', ['rating' => $package->averageRating])
        <span>{{ $package->averageRating }} &middot; {{ $package->reviews->count() }} reviews</span>
      </div>
    @endif

    <div class="pkg-card-price-row">
      <div class="from-label">Starting from</div>
      @if($package->discounted_price && $package->discounted_price < $package->price)
        <div class="orig-price">₹{{ number_format($package->price) }}</div>
        <div class="curr-price">₹{{ number_format($package->discounted_price) }} <span class="per-person">/ person</span></div>
      @else
        <div class="curr-price">₹{{ number_format($package->price) }} <span class="per-person">/ person</span></div>
      @endif
    </div>
  </div>
  <div class="pkg-card-footer">
    <a href="{{ route('packages.show', $package->slug) }}" class="btn-view">VIEW DETAILS</a>
    <div class="d-flex align-items-center gap-2">
      @if(config('app.contact_phone'))
        <a href="tel:{{ config('app.contact_phone') }}" class="btn-call" title="Call us" aria-label="Call us">
          <i class="bi bi-telephone-fill"></i>
        </a>
      @endif
      <a href="{{ route('packages.show', $package->slug) }}" class="btn-book-now">BOOK NOW</a>
    </div>
  </div>
</div>
