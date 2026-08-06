@php $tabs = collect($section->extra['tabs'] ?? []); @endphp
<section class="planner-section section-pad" style="background:#f8f8f8;">
  <div class="container">
    <h2 class="section-title mb-1">{{ $section->heading }}</h2>
    @if($section->subheading)
      <p class="text-muted mb-3" style="font-size:13px;">{{ $section->subheading }}</p>
    @endif

    <div class="planner-tabs">
      @foreach($tabs as $tab)
        <button class="{{ $loop->first ? 'active' : '' }}" data-tab-target="{{ $tab['key'] }}">{{ $tab['label'] }}</button>
      @endforeach
    </div>

    <div class="planner-scroll-wrapper">
      <!-- Scroll buttons -->
      <button class="planner-scroll-btn planner-prev" id="planner-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="planner-scroll-btn planner-next" id="planner-next"><i class="bi bi-chevron-right"></i></button>

      <div class="planner-grid-scroll" id="plannerGrid">
        @foreach($tabs as $tab)
          @foreach($section->activeItems($tab['key']) as $item)
            <a href="{{ \App\Support\MediaUrl::link($item->link) }}" class="city-card text-decoration-none" data-tab-group="{{ $tab['key'] }}" style="{{ $loop->parent->first ? '' : 'display:none;' }}">
              <img src="{{ \App\Support\MediaUrl::resolve($item->image) }}" alt="{{ $item->title }}"/>
              <h6>{{ $item->title }}</h6>
              <small>{{ $item->meta['distance_km'] ?? null }} km away</small>
            </a>
          @endforeach
        @endforeach
      </div>
    </div>
  </div>
</section>
