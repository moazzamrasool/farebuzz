{{--
  One day's (or the "General Add-ons" bucket's) hotel selection: a summary slot
  showing only the currently-selected option, plus a "Change" modal listing every
  hotel attached to this day. Expects: $dayHotels (collection), $dayNumber (a day
  number or the string 'general').
--}}
<div class="itin-day-subhead">Hotel @if($dayNumber !== 'general') — Day {{ $dayNumber }} @endif</div>

<div class="day-summary-slot" data-slot-kind="hotel" data-slot-day="{{ $dayNumber }}">
  @foreach($dayHotels as $hotel)
    @include('packages._hotel_summary_row', ['hotel' => $hotel, 'dayNumber' => $dayNumber])
  @endforeach
  <div class="day-summary-empty" data-empty-kind="hotel" data-empty-day="{{ $dayNumber }}" style="display:none;">
    <button type="button" class="btn-add-to-day" data-bs-toggle="modal" data-bs-target="#hotel-modal-day-{{ $dayNumber }}"><i class="bi bi-plus-circle"></i> Add Hotel</button>
  </div>
</div>

<div class="modal fade" id="hotel-modal-day-{{ $dayNumber }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius:16px;border:none;">
      <div class="modal-header" style="border-bottom:1px solid #eee;">
        <h5 class="modal-title" style="font-weight:800;">Select Hotel @if($dayNumber !== 'general') — Day {{ $dayNumber }} @endif</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @foreach($dayHotels as $hotel)
          @include('packages._hotel_option_card', ['hotel' => $hotel, 'radioGroup' => 'hotel_day_'.$dayNumber])
        @endforeach
      </div>
    </div>
  </div>
</div>
