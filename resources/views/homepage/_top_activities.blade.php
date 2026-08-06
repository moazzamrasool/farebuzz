@php $activities = \App\Services\Homepage\HomepageSliderData::topActivities($section->item_limit ?? 10); @endphp
@if($activities->isNotEmpty())
<section class="section-pad">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h2 class="section-title mb-0">{{ $section->heading ?: $section->name }}</h2>
      <a href="{{ route('activities.index') }}" class="fw-semibold text-decoration-none" style="font-size:13px;color:#005fcc;">View all</a>
    </div>
    @if($section->subheading)<p class="text-muted mb-3">{{ $section->subheading }}</p>@endif

    <div class="activities-scroll-wrapper">
      <button class="activities-scroll-btn activities-prev" id="activities-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="activities-scroll-btn activities-next" id="activities-next"><i class="bi bi-chevron-right"></i></button>

      <div class="activities-grid-scroll" id="activitiesGrid">
        @foreach($activities as $activity)
          @include('activities._card', ['activity' => $activity])
        @endforeach
      </div>
    </div>
  </div>
</section>
@endif
