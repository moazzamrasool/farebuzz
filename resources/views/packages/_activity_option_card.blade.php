{{--
  Shared optional-activity add-on card used both inside a day's plan and in the
  "General Add-ons" fallback. Expects: $activity (with pivot: price, note).
--}}
@php
  $effectivePrice = (float) ($activity->pivot->price ?? $activity->price ?? 0);
@endphp
<div class="col-md-6">
  <div class="addon-card" data-addon-id="{{ $activity->id }}">
    @if($activity->image)
      <img src="{{ asset('storage/'.$activity->image) }}" alt="{{ $activity->name }}" class="addon-img">
    @endif
    <div class="addon-body">
      <div class="d-flex justify-content-between align-items-start gap-2">
        <div class="addon-name">{{ $activity->name }}</div>
        <div class="addon-price">₹{{ number_format($effectivePrice) }}<span class="per d-block">/ person</span></div>
      </div>
      @if($activity->description)
        <p class="addon-desc">{{ Str::limit($activity->description, 90) }}</p>
      @endif
      @if($activity->pivot->note)
        <p class="addon-desc" style="color:#f47b20;">{{ $activity->pivot->note }}</p>
      @endif
      <label class="addon-toggle">
        <input type="checkbox" class="addon-checkbox" value="{{ $activity->id }}"
          data-name="{{ $activity->name }}" data-price="{{ number_format($effectivePrice, 2, '.', '') }}"
          @if(isset($dayNumber)) data-day="{{ $dayNumber }}" @endif>
        <i class="bi bi-plus-circle"></i> Add
      </label>
    </div>
  </div>
</div>
