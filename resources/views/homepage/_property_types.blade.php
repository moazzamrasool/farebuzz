@php $items = $section->activeItems(); @endphp
<section class="section-pad" style="background:#f5f5f5;">
  <div class="container">
    <h2 class="section-title mb-3">{{ $section->heading }}</h2>

    <div class="browse-scroll-wrapper">
      <!-- Scroll buttons -->
      <button class="browse-scroll-btn browse-prev" id="browse-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="browse-scroll-btn browse-next" id="browse-next"><i class="bi bi-chevron-right"></i></button>

      <div class="browse-grid-scroll" id="browseGrid">
        @foreach($items as $item)
          <a href="{{ \App\Support\MediaUrl::link($item->link) }}" class="property-card text-decoration-none">
            <img src="{{ \App\Support\MediaUrl::resolve($item->image) }}" alt="{{ $item->title }}"/>
            <div class="property-card-label">{{ $item->title }}</div>
          </a>
        @endforeach
      </div>
    </div>
  </div>
</section>
