{{--
  One day's (or the "General Add-ons" bucket's) activity add-ons: summary rows for
  every added activity (Remove/Change), plus an always-available "Add Activity" button
  that opens a modal listing every activity attached to this day. Expects:
  $dayActivities (collection), $dayNumber (a day number or the string 'general').
--}}
<div class="itin-day-subhead d-flex justify-content-between align-items-center">
  <span>Activities @if($dayNumber !== 'general') — Day {{ $dayNumber }} @endif</span>
  <button type="button" class="btn-add-to-day" data-bs-toggle="modal" data-bs-target="#activity-modal-day-{{ $dayNumber }}"><i class="bi bi-plus-circle"></i> Add Activity to your day</button>
</div>

<div class="day-summary-slot" data-slot-kind="activity" data-slot-day="{{ $dayNumber }}">
  @foreach($dayActivities as $activity)
    @include('packages._activity_summary_row', ['activity' => $activity, 'dayNumber' => $dayNumber])
  @endforeach
</div>

<div class="modal fade" id="activity-modal-day-{{ $dayNumber }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius:16px;border:none;">
      <div class="modal-header" style="border-bottom:1px solid #eee;">
        <h5 class="modal-title" style="font-weight:800;">Select Activities @if($dayNumber !== 'general') — Day {{ $dayNumber }} @endif</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          @foreach($dayActivities as $activity)
            @include('packages._activity_option_card', ['activity' => $activity, 'dayNumber' => $dayNumber])
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
