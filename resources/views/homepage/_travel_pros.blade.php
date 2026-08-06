@php $items = $section->activeItems(); @endphp
<section class="pros-section section-pad">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h2 class="section-title mb-0">{{ $section->heading }}</h2>
      <div class="carousel-nav">
        <button class="carousel-btn"><i class="bi bi-chevron-left"></i></button>
        <button class="carousel-btn"><i class="bi bi-chevron-right"></i></button>
      </div>
    </div>
    <div class="row g-3">
      @foreach($items as $item)
        <div class="col-6 col-md-3">
          <a href="{{ \App\Support\MediaUrl::link($item->link) }}" class="pro-card text-decoration-none">
            <div class="pro-card-header">
              <h6>{{ $item->title }}</h6>
              <p>{{ $item->subtitle }}</p>
            </div>
            <img src="{{ \App\Support\MediaUrl::resolve($item->image) }}" alt="{{ $item->title }}"/>
          </a>
        </div>
      @endforeach
    </div>
  </div>
</section>
