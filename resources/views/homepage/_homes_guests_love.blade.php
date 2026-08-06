@php $items = $section->activeItems(); @endphp
<section class="section-pad" style="background:#f8f8f8;">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h2 class="section-title mb-0">{{ $section->heading }}</h2>
    </div>

    <div class="homes-scroll-wrapper">
      <!-- Scroll buttons -->
      <button class="homes-scroll-btn homes-prev" id="homes-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="homes-scroll-btn homes-next" id="homes-next"><i class="bi bi-chevron-right"></i></button>

      <div class="homes-grid-scroll" id="homesGrid">
        @foreach($items as $item)
          @include('homepage._accommodation_card', ['item' => $item])
        @endforeach
      </div>
    </div>
  </div>
</section>
