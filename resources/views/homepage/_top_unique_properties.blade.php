@php $items = $section->activeItems(); @endphp
<section class="bg-white-section section-pad">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-1">
      <h2 class="section-title mb-0">{{ $section->heading }}</h2>
    </div>
    @if($section->subheading)
      <p class="text-muted mb-3" style="font-size:13px;">{{ $section->subheading }}</p>
    @endif

    <div class="unique-scroll-wrapper">
      <!-- Scroll buttons -->
      <button class="unique-scroll-btn unique-prev" id="unique-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="unique-scroll-btn unique-next" id="unique-next"><i class="bi bi-chevron-right"></i></button>

      <div class="unique-grid-scroll" id="uniqueGrid">
        @foreach($items as $item)
          @include('homepage._accommodation_card', ['item' => $item])
        @endforeach
      </div>
    </div>
  </div>
</section>
