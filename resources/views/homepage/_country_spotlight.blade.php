@php $items = $section->activeItems(); @endphp
<section class="section-pad" style="background:#f5f5f5;">
  <div class="container">
    <div class="srilanka-box">
      <div class="mb-4">
        <h2 class="section-title mb-1" style="font-size: 24px; font-weight: 800; margin-top: 0;">{{ $section->heading }}</h2>
        @if($section->subheading)
          <p class="text-muted mb-0" style="font-size: 14px;">{{ $section->subheading }}</p>
        @endif
      </div>
      <div class="row g-3">
        @foreach($items as $item)
          <div class="{{ $loop->first ? 'col-6 col-md-4' : ($loop->last ? 'col-12 col-md-4' : 'col-6 col-md-4') }}">
            <a href="{{ \App\Support\MediaUrl::link($item->link) }}" class="srilanka-dest-card text-decoration-none {{ $loop->first ? 'overlay-style' : '' }}">
              <img src="{{ \App\Support\MediaUrl::resolve($item->image) }}" alt="{{ $item->title }}"/>
              <div class="srilanka-dest-body">
                <h6>{{ $item->title }}</h6>
                <p>{{ $item->description }}</p>
              </div>
            </a>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>
