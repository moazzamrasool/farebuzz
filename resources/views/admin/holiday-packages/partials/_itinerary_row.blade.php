@php
  $item = $item ?? null;
  $mealTags = $item->meal_tags ?? [];
  $bulletPoints = $item ? implode("\n", $item->bullet_points ?? []) : '';
  $dayHp = $dayHp ?? ($hp ?? null);
  $dayHotels = ($dayHp && $item?->day_number) ? $dayHp->hotels->where('pivot.day_number', $item->day_number) : collect();
  $dayActivities = ($dayHp && $item?->day_number) ? $dayHp->optionalActivities->where('pivot.day_number', $item->day_number) : collect();
@endphp
<div class="card mb-2" data-repeater-row>
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <strong>Day <span data-repeater-index>{{ $index === '__INDEX__' ? '' : $index + 1 }}</span></strong>
      <button type="button" class="btn btn-danger btn-sm" data-repeater-remove>&times; Remove Day</button>
    </div>
    @if($dayHotels->isNotEmpty() || $dayActivities->isNotEmpty())
      <div class="text-muted small mb-2">
        <i class="fas fa-link mr-1"></i>Attached in Hotels/Activities tabs for Day {{ $item->day_number }}:
        {{ $dayHotels->pluck('name')->concat($dayActivities->pluck('name'))->join(', ') }}
      </div>
    @endif
    <div class="form-row">
      <div class="form-group col-md-2">
        <label class="form-label-sm">Day #</label>
        <input type="number" min="1" name="itineraries[{{ $index }}][day_number]" class="form-control" value="{{ $item->day_number ?? '' }}" placeholder="e.g. 1">
      </div>
      <div class="form-group col-md-10">
        <label class="form-label-sm">Title</label>
        <input type="text" name="itineraries[{{ $index }}][title]" class="form-control" value="{{ $item->title ?? '' }}" placeholder="e.g. Arrival in Goa &middot; Check-in &amp; Beach Evening">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label-sm">Route / Summary Line</label>
      <input type="text" name="itineraries[{{ $index }}][route_summary]" class="form-control" value="{{ $item->route_summary ?? '' }}" placeholder="e.g. Dabolim Airport &rarr; Hotel &middot; North Goa Beaches">
    </div>
    <div class="form-group">
      <label class="form-label-sm">Detail</label>
      <textarea name="itineraries[{{ $index }}][detail]" rows="3" class="form-control rich-text-editor" placeholder="e.g. Arrive at Goa Airport and transfer to your hotel. In the evening, head to Calangute Beach...">{{ $item->detail ?? '' }}</textarea>
    </div>
    <div class="form-group">
      <label class="form-label-sm">Bullet Points <small class="text-muted">(one per line)</small></label>
      <textarea name="itineraries[{{ $index }}][bullet_points]" rows="3" class="form-control" placeholder="e.g.&#10;Airport pickup in an AC vehicle&#10;Hotel check-in (early check-in subject to availability)&#10;Welcome dinner at beachside restaurant">{{ $bulletPoints }}</textarea>
    </div>
    <div class="form-group mb-0">
      <label class="form-label-sm d-block">Meal Tags</label>
      @foreach(['breakfast' => 'Breakfast', 'lunch' => 'Lunch', 'dinner' => 'Dinner'] as $mealKey => $mealLabel)
        <div class="custom-control custom-checkbox custom-control-inline">
          <input type="checkbox" name="itineraries[{{ $index }}][meal_tags][]" value="{{ $mealKey }}" class="custom-control-input" id="meal_{{ $index }}_{{ $mealKey }}"
            {{ in_array($mealKey, $mealTags) ? 'checked' : '' }}>
          <label class="custom-control-label" for="meal_{{ $index }}_{{ $mealKey }}">{{ $mealLabel }}</label>
        </div>
      @endforeach
    </div>
  </div>
</div>
