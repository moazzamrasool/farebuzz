@php $tabs = collect($section->extra['tabs'] ?? []); @endphp
<section class="bg-white-section section-pad">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
      <h2 class="section-title mb-0">{{ $section->heading }}</h2>
    </div>
    <div class="dest-tabs mb-3">
      @foreach($tabs as $tab)
        <button class="{{ $loop->first ? 'active' : '' }}" data-tab-target="{{ $tab['key'] }}">{{ $tab['label'] }}</button>
      @endforeach
    </div>
    <div class="dest-scroll-wrapper">
      <!-- Scroll buttons -->
      <button class="dest-scroll-btn dest-prev" id="dest-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="dest-scroll-btn dest-next" id="dest-next"><i class="bi bi-chevron-right"></i></button>

      <div class="dest-grid-scroll" id="destGrid">
        @foreach($tabs as $tab)
          @foreach($section->activeItems($tab['key']) as $item)
            <a href="{{ \App\Support\MediaUrl::link($item->link) }}" class="dest-card text-decoration-none" data-tab-group="{{ $tab['key'] }}" style="{{ $loop->parent->first ? '' : 'display:none;' }}">
              <img src="{{ \App\Support\MediaUrl::resolve($item->image) }}" alt="{{ $item->title }}"/>
              <div class="dest-card-body">
                <h6>{{ $item->title }}</h6>
                <p>{{ $item->subtitle }}</p>
              </div>
            </a>
          @endforeach
        @endforeach
      </div>
    </div>
  </div>
</section>
