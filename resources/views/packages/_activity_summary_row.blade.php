{{--
  Selected-activity summary row shown inline in the day plan (MakeMyTrip pattern) —
  a lightweight, non-interactive display of one added optional activity, with
  Remove/Change controls. Visibility is toggled by JS based on whether the matching
  .addon-checkbox (living in the modal) is checked; this row carries no form inputs
  of its own. Expects: $activity, $dayNumber (a day number or the string 'general').
--}}
@php
  $effectivePrice = (float) ($activity->pivot->price ?? $activity->price ?? 0);
@endphp
<div class="day-summary-row" data-summary-kind="activity" data-day="{{ $dayNumber }}" data-activity-id="{{ $activity->id }}" style="display:none;">
  <div class="summary-row-head">
    <span class="summary-row-label"><i class="bi bi-stars"></i> Activity</span>
    <span>
      <a href="#" class="summary-remove-link" data-remove-activity="{{ $activity->id }}" data-day="{{ $dayNumber }}">Remove</a>
      <a href="#" class="summary-change-link ms-2" data-bs-toggle="modal" data-bs-target="#activity-modal-day-{{ $dayNumber }}" data-change-activity="{{ $activity->id }}">Change</a>
    </span>
  </div>
  <div class="summary-row-body">
    @if($activity->image)
      <img src="{{ asset('storage/'.$activity->image) }}" alt="{{ $activity->name }}">
    @endif
    <div class="summary-row-info">
      <div class="summary-row-name">{{ $activity->name }}</div>
      @if($activity->description)
        <div class="summary-row-loc">{{ Str::limit($activity->description, 80) }}</div>
      @endif
    </div>
    <div class="summary-row-price">₹{{ number_format($effectivePrice) }}<span class="per d-block">/ person</span></div>
  </div>
</div>
