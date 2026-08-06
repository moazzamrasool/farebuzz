@php $packages = \App\Services\Homepage\HomepageSliderData::internationalPackages($section->item_limit ?? 10); @endphp
@if($packages->isNotEmpty())
@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/asset/css/package-card.css') }}">
@endpush
<section class="section-pad" style="background:#f8f8f8;">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h2 class="section-title mb-0">{{ $section->heading ?: $section->name }}</h2>
      <a href="{{ route('packages.international') }}" class="fw-semibold text-decoration-none" style="font-size:13px;color:#005fcc;">View all</a>
    </div>
    @if($section->subheading)<p class="text-muted mb-3">{{ $section->subheading }}</p>@endif

    <div class="intl-scroll-wrapper">
      <button class="intl-scroll-btn intl-prev" id="intl-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="intl-scroll-btn intl-next" id="intl-next"><i class="bi bi-chevron-right"></i></button>

      <div class="intl-grid-scroll" id="intlGrid">
        @foreach($packages as $package)
          @include('packages._card', ['package' => $package])
        @endforeach
      </div>
    </div>
  </div>
</section>
@endif
