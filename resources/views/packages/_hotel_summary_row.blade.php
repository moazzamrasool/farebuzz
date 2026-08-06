{{--
  Selected-hotel summary card shown inline in the day plan (MakeMyTrip pattern) —
  a lightweight, non-interactive display of one option from the day's "Change" modal.
  Visibility is toggled by JS based on which .hotel-addon-radio (living in the modal)
  is checked; this row carries no form inputs of its own. Expects: $hotel, $dayNumber
  (a day number or the string 'general').
--}}
@php
  $hotelRoomType = $hotel->pivot->room_type_id ? $hotel->roomTypes->firstWhere('id', $hotel->pivot->room_type_id) : null;
  $hotelPricePerNight = (float) ($hotel->pivot->price ?? $hotelRoomType?->sell_price ?? 0);
  $hotelNights = (int) ($hotel->pivot->nights ?? 1);
  $hotelIsOptional = (bool) $hotel->pivot->is_optional;
@endphp
<div class="day-summary-row" data-summary-kind="hotel" data-day="{{ $dayNumber }}" data-hotel-id="{{ $hotel->id }}" style="display:none;">
  <div class="summary-row-head">
    <span class="summary-row-label"><i class="bi bi-building"></i> {{ $hotelIsOptional ? 'Hotel' : 'Included Hotel' }} &middot; {{ $hotelNights }} Night{{ $hotelNights > 1 ? 's' : '' }}</span>
    @if($hotelIsOptional)
      <a href="#" class="summary-change-link" data-bs-toggle="modal" data-bs-target="#hotel-modal-day-{{ $dayNumber }}">Change</a>
    @endif
  </div>
  <div class="summary-row-body">
    <img src="{{ \App\Support\MediaUrl::resolve($hotel->cover_image) }}" alt="{{ $hotel->name }}">
    <div class="summary-row-info">
      <div class="summary-row-name">{{ $hotel->name }}</div>
      <div class="hotel-star">
        @for($i = 0; $i < $hotel->star_rating; $i++)<i class="bi bi-star-fill"></i>@endfor
      </div>
      <div class="summary-row-loc"><i class="bi bi-geo-alt-fill"></i> {{ $hotel->address }}</div>
    </div>
    @if($hotelIsOptional)
      <div class="summary-row-price">₹{{ number_format($hotelPricePerNight) }}<span class="per d-block">/ night &times; {{ $hotelNights }}N</span></div>
    @endif
  </div>
</div>
