@php $destinations = \App\Services\Homepage\HomepageSliderData::trendingDestinations($section->item_limit ?? 10); @endphp
@if($destinations->isNotEmpty())
@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/asset/css/destination-tiles.css') }}">
@endpush
<section class="section-pad" style="background:#f5f5f5;">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h2 class="section-title mb-0">{{ $section->heading ?: $section->name }}</h2>
      <a href="{{ route('destinations.index') }}" class="fw-semibold text-decoration-none" style="font-size:13px;color:#005fcc;">View all destinations</a>
    </div>
    @if($section->subheading)<p class="text-muted mb-3">{{ $section->subheading }}</p>@endif

    <div class="row g-3">
      @foreach($destinations as $destination)
        <div class="col-6 col-md-4 col-lg-3">
          <a href="{{ route('destinations.show', $destination->slug) }}" class="dest-tile">
            <img src="{{ \App\Support\MediaUrl::resolve($destination->cover_image) ?? 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=70' }}" alt="{{ $destination->name }}" loading="lazy">
            @if($code = \App\Support\CountryFlag::code($destination->country))
              <img src="https://flagcdn.com/w80/{{ $code }}.png" alt="{{ $destination->country }} flag" class="dest-flag-badge">
            @endif
            <div class="dest-tile-overlay">
              <div class="dest-tile-name">
                {{ $destination->name }}
                <span class="dest-tile-tag">{{ $destination->local === 'domestic' ? 'India' : $destination->country }}</span>
              </div>
              @if($destination->packages_count)
                <div class="dest-tile-count">{{ $destination->packages_count }} {{ Str::plural('Package', $destination->packages_count) }}</div>
              @endif
            </div>
          </a>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif
