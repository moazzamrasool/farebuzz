{{-- Shared activity card. Expects: $activity with destination (and ideally activityCategory) loaded. --}}
@php
  $categoryIcon = $activity->activityCategory->icon ?? 'bi-stars';
@endphp
<a href="{{ route('activities.show', $activity->slug) }}" class="text-decoration-none activity-card-link">
  <div class="activity-card">
    <div class="activity-card-img">
      <img src="{{ \App\Support\MediaUrl::resolve($activity->image) ?? 'https://images.unsplash.com/photo-1526772662000-3f88f10405ff?w=700&q=70' }}" alt="{{ $activity->name }}" loading="lazy">
      @if($activity->activityCategory)
        <span class="activity-badge-category"><i class="bi {{ $categoryIcon }}"></i> {{ $activity->activityCategory->name }}</span>
      @endif
      <span class="activity-badge-price">{{ $activity->price ? '₹'.number_format($activity->price) : 'Included' }}</span>
    </div>
    <div class="activity-card-body">
      <div class="activity-card-title">{{ $activity->name }}</div>
      @if($activity->destination)
        <div class="activity-card-dest"><i class="bi bi-geo-alt-fill"></i> {{ $activity->destination->name }}</div>
      @endif
      @if($activity->duration)
        <div class="activity-card-duration"><i class="bi bi-clock"></i> {{ $activity->duration }}</div>
      @endif
      <p class="activity-card-desc">{{ \Illuminate\Support\Str::limit($activity->description, 88) }}</p>
      <div class="activity-card-footer">
        @if($activity->price)
          <div>
            <div class="activity-price-label">per person</div>
            <div class="activity-price-value">₹{{ number_format($activity->price) }}</div>
          </div>
        @else
          <span></span>
        @endif
        <span class="btn-view-activity">View Details <i class="bi bi-arrow-right"></i></span>
      </div>
    </div>
  </div>
</a>
