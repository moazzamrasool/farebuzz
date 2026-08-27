{{--
  Shared hotel card used both inside a day's plan and in the "General Add-ons"
  fallback. Expects: $hotel (with pivot: is_optional, room_type_id, price, nights,
  note), $radioGroup (name for the "Add" radio — scoped per day so only one hotel
  per day/stay can be selected at a time).
--}}
@php
  $hotelIsOptional = (bool) $hotel->pivot->is_optional;
  $hotelRoomType = $hotel->pivot->room_type_id ? $hotel->roomTypes->firstWhere('id', $hotel->pivot->room_type_id) : null;
  $hotelPricePerNight = (float) ($hotel->pivot->price ?? $hotelRoomType?->sell_price ?? 0);
  $hotelNights = (int) ($hotel->pivot->nights ?? 1);
@endphp
<div class="hotel-detail-card mb-3 {{ $hotelIsOptional ? 'addon-card' : '' }}" @if($hotelIsOptional) data-addon-id="{{ $hotel->id }}" @endif>
  <img src="{{ \App\Support\MediaUrl::resolve($hotel->cover_image) }}" alt="{{ $hotel->name }}">
  <div class="hotel-detail-body">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-2">
      <div>
        <h5 style="font-size:16px;font-weight:700;margin-bottom:4px;">{{ $hotel->name }}</h5>
        <div class="hotel-star mb-1">
          @for($i = 0; $i < $hotel->star_rating; $i++)<i class="bi bi-star-fill"></i>@endfor
          <span style="font-size:12px;color:#888;margin-left:6px;">{{ $hotel->star_rating }} Star Hotel</span>
        </div>
        <div style="font-size:12px;color:#888;"><i class="bi bi-geo-alt-fill" style="color:var(--blue);"></i> {{ $hotel->address }}</div>
      </div>
      @if($hotelIsOptional)
        <div class="text-end">
          <div class="addon-price">₹{{ number_format($hotelPricePerNight) }}<span class="per d-block">/ night &times; {{ $hotelNights }}N</span></div>
        </div>
      @elseif($hotel->rating_score)
        <div style="text-align:right;">
          <div style="background:#16a34a;color:#fff;border-radius:8px;padding:4px 12px;font-size:13px;font-weight:700;display:inline-block;">{{ $hotel->rating_score }}</div>
          <div style="font-size:11px;color:#888;margin-top:2px;">{{ $hotel->review_count }} reviews</div>
        </div>
      @endif
    </div>
    @if($hotelIsOptional && $hotelRoomType)
      <div style="font-size:12px;color:#555;margin-bottom:8px;">
        <i class="bi bi-door-open"></i> {{ $hotelRoomType->name }}
        @if($hotelRoomType->bed_type) &middot; {{ $hotelRoomType->bed_type->label() }} @endif
        &middot; {{ $hotelRoomType->occupancy_adults }} Adult{{ $hotelRoomType->occupancy_adults > 1 ? 's' : '' }}{{ $hotelRoomType->occupancy_children ? ', '.$hotelRoomType->occupancy_children.' Child(ren)' : '' }}
      </div>
    @endif
    <p style="font-size:13px;color:#555;line-height:1.6;margin-bottom:12px;">{{ $hotel->description }}</p>
    <div class="d-flex flex-wrap">
      @foreach($hotel->amenities as $amenity)
        <span class="hotel-amenity"><i class="bi bi-{{ $amenity->icon ?: 'check2' }}"></i> {{ $amenity->name }}</span>
      @endforeach
    </div>
    @if($hotelIsOptional)
      @if($hotel->pivot->note)
        <p class="addon-desc" style="color:#f47b20;">{{ $hotel->pivot->note }}</p>
      @endif
      <label class="addon-toggle mt-2">
        <input type="radio" class="hotel-addon-radio" name="{{ $radioGroup ?? 'hotel_addon_general' }}" value="{{ $hotel->id }}"
          data-name="{{ $hotel->name }}" data-price="{{ number_format($hotelPricePerNight, 2, '.', '') }}" data-nights="{{ $hotelNights }}"
          data-room-type-id="{{ $hotelRoomType?->id }}" data-room-type-name="{{ $hotelRoomType?->name }}" data-bed-type="{{ $hotelRoomType?->bed_type?->label() }}">
        <i class="bi bi-plus-circle"></i> Add
      </label>
    @endif
  </div>
</div>
