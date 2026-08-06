@php $packages = \App\Services\Homepage\HomepageSliderData::domesticPackages($section->item_limit ?? 10); @endphp
@if($packages->isNotEmpty())
@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/asset/css/package-card.css') }}">
@endpush
<section class="section-pad">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h2 class="section-title mb-0">{{ $section->heading ?: $section->name }}</h2>
      <a href="{{ route('packages.india') }}" class="fw-semibold text-decoration-none" style="font-size:13px;color:#005fcc;">View all</a>
    </div>
    @if($section->subheading)<p class="text-muted mb-3">{{ $section->subheading }}</p>@endif

    <div class="domestic-scroll-wrapper">
      <button class="domestic-scroll-btn domestic-prev" id="domestic-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="domestic-scroll-btn domestic-next" id="domestic-next"><i class="bi bi-chevron-right"></i></button>

      <div class="domestic-grid-scroll" id="domesticGrid">
        @foreach($packages as $package)
          @include('packages._card', ['package' => $package])
        @endforeach
      </div>
    </div>
  </div>
</section>
@endif
