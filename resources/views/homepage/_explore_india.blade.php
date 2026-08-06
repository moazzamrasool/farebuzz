@php $items = $section->activeItems(); @endphp
<section class="bg-white-section section-pad">
  <div class="container">
    <h2 class="section-title mb-1">{{ $section->heading }}</h2>
    @if($section->subheading)
      <p class="text-muted mb-3" style="font-size:13px;">{{ $section->subheading }}</p>
    @endif

    <div class="explore-scroll-wrapper">
      <!-- Scroll buttons -->
      <button class="explore-scroll-btn explore-prev" id="explore-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="explore-scroll-btn explore-next" id="explore-next"><i class="bi bi-chevron-right"></i></button>

      <div class="explore-grid-scroll" id="exploreGrid">
        @foreach($items as $item)
          <a href="{{ \App\Support\MediaUrl::link($item->link) }}" class="explore-card text-decoration-none">
            <img src="{{ \App\Support\MediaUrl::resolve($item->image) }}" alt="{{ $item->title }}"/>
            <div class="card-body">
              <h6>{{ $item->title }}</h6>
              <small>{{ number_format((int) ($item->meta['property_count'] ?? 0)) }} properties</small>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </div>
</section>
