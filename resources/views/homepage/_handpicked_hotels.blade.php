@php $hotels = \App\Services\Homepage\HomepageSliderData::handpickedHotels($section->item_limit ?? 10); @endphp
@if($hotels->isNotEmpty())
<section class="section-pad" style="background:#f8f8f8;">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h2 class="section-title mb-0">{{ $section->heading ?: $section->name }}</h2>
      <a href="{{ route('hotels.index') }}" class="fw-semibold text-decoration-none" style="font-size:13px;color:#005fcc;">View all</a>
    </div>
    @if($section->subheading)<p class="text-muted mb-3">{{ $section->subheading }}</p>@endif

    <div class="stays-scroll-wrapper">
      <button class="stays-scroll-btn stays-prev" id="stays-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="stays-scroll-btn stays-next" id="stays-next"><i class="bi bi-chevron-right"></i></button>

      <div class="stays-grid-scroll" id="staysGrid">
        @foreach($hotels as $hotel)
          @include('hotels._card', ['hotel' => $hotel])
        @endforeach
      </div>
    </div>
  </div>
</section>
@endif
