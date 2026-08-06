@php
  $items = $section->activeItems();
  // Admin-edited heading, still colour-splitting "Fare"/"Buzz(er)" wherever it
  // appears so the brand-styled spans survive even though the copy is editable.
  $flagshipHeading = $section->heading ?: 'Flagship Hotel Stores on FareBuzzer';
  $flagshipHeadingHtml = preg_replace('/(Fare)(Buzz(?:er)?)/i', '<span class="logo-fare">$1</span><span class="logo-buzz">$2</span>', e($flagshipHeading));
@endphp
<section class="flagship-section section-pad">
  <div class="container">
    <div class="flagship-inner">
      <div class="flagship-title-block">
        <h3>{!! $flagshipHeadingHtml !!}</h3>
      </div>
      <div class="flagship-cards-grid">
        @foreach($items as $item)
          <a href="{{ \App\Support\MediaUrl::link($item->link) }}" class="hotel-store-card text-decoration-none">
            @if($item->meta['logo'] ?? null)
              <div class="logo-overlay">
                <img src="{{ \App\Support\MediaUrl::resolve($item->meta['logo']) }}" alt="{{ $item->title }} Logo"/>
              </div>
            @endif
            <img src="{{ \App\Support\MediaUrl::resolve($item->image) }}" alt="{{ $item->title }}" class="card-bg-img"/>
            <div class="gradient-overlay"></div>
            <div class="card-name-overlay">
              <h6>{{ $item->title }}</h6>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </div>
</section>
