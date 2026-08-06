@extends('layouts.app')

@section('title', 'Book '.$package->title.' – FareBuzzer')

@push('styles')
<style>
  :root { --blue:#005fcc; --orange:#f47b20; }
  body { font-family:'Inter',sans-serif; background:#f5f5f5; }
  .bk-wrap { max-width:1100px; margin:0 auto; padding:32px 16px 60px; }
  .bk-title { font-size:24px; font-weight:800; margin-bottom:4px; }
  .bk-sub { color:#888; font-size:13px; margin-bottom:24px; }
  .bk-card { background:#fff; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08); padding:26px; margin-bottom:20px; }
  .bk-card h5 { font-weight:800; font-size:15px; margin-bottom:16px; }
  .bk-label { font-size:12px; font-weight:600; color:#888; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px; }
  .bk-input { border:1.5px solid #e0e0e0; border-radius:8px; padding:10px 12px; font-size:14px; width:100%; outline:none; }
  .bk-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(0,95,204,.1); }
  .bk-summary { background:#fff; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.1); padding:26px; position:sticky; top:20px; }
  .bk-summary-row { display:flex; justify-content:space-between; font-size:13px; margin-bottom:10px; color:#444; }
  .bk-summary-row.total { font-size:17px; font-weight:800; color:var(--blue); border-top:1px solid #eee; padding-top:12px; margin-top:6px; }
  .bk-summary-row.discount { color:#16a34a; }
  .btn-pay { background:var(--blue); color:#fff; border:none; border-radius:10px; font-size:15px; font-weight:700; padding:14px; width:100%; cursor:pointer; margin-top:16px; }
  .btn-pay:hover { background:#004bb5; }

  .addon-check-row { display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid #f0f0f0; }
  .addon-check-row:last-child { border-bottom:none; }
  .addon-check-row .form-check-input { width:18px; height:18px; flex-shrink:0; margin-top:0; }
  .addon-check-row .addon-check-name { font-size:13px; font-weight:600; flex:1; }
  .addon-check-row .addon-check-note { font-size:11px; color:#888; }
  .addon-check-row .addon-check-price { font-size:13px; font-weight:700; color:var(--blue); white-space:nowrap; }
</style>
@endpush

@section('content')
<div class="bk-wrap">
  <div class="bk-title">Complete Your Booking</div>
  <div class="bk-sub">{{ $package->title }} &middot; {{ $package->nights }}N/{{ $package->days }}D</div>

  <form method="POST" action="{{ route('bookings.store', $package->slug) }}" id="booking-form">
    @csrf
    <div class="row g-4">
      <div class="col-lg-8">

        <div class="bk-card">
          <h5><i class="bi bi-geo-alt text-primary me-1"></i> Trip Details</h5>
          <div class="row g-3">
            @if($package->departureCities->isNotEmpty())
              <div class="col-md-4">
                <div class="bk-label">Departure City</div>
                <select name="departure_city" class="bk-input" id="departure_city">
                  @foreach($package->departureCities as $city)
                    <option value="{{ $city->city_name }}" {{ old('departure_city', request('departure_city')) === $city->city_name ? 'selected' : '' }}>{{ $city->city_name }}</option>
                  @endforeach
                </select>
              </div>
            @endif
            <div class="col-md-4">
              <div class="bk-label">Travel Date</div>
              <input type="date" name="travel_date" class="bk-input" id="travel_date" min="{{ now()->toDateString() }}" value="{{ old('travel_date', request('travel_date')) }}" required>
            </div>
            @if($package->roomTypes->isNotEmpty())
              <div class="col-md-4">
                <div class="bk-label">Room Type</div>
                <select name="room_type_id" class="bk-input" id="room_type_id">
                  @foreach($package->roomTypes as $room)
                    <option value="{{ $room->id }}"
                      data-original-price="{{ number_format((float) $room->price, 2, '.', '') }}"
                      data-discounted-price="{{ number_format((float) $room->sell_price, 2, '.', '') }}"
                      {{ (string) old('room_type_id', request('room_type_id')) === (string) $room->id ? 'selected' : '' }}>
                      {{ $room->name }} — ₹{{ number_format($room->sell_price) }}
                    </option>
                  @endforeach
                </select>
              </div>
            @endif
            <div class="col-md-3">
              <div class="bk-label">Adults</div>
              <input type="number" name="adults" id="adults" class="bk-input" min="1" max="20" value="{{ old('adults', 1) }}" required>
            </div>
            <div class="col-md-3">
              <div class="bk-label">Children</div>
              <input type="number" name="children" id="children" class="bk-input" min="0" max="20" value="{{ old('children', 0) }}">
            </div>
          </div>
        </div>

        @php
          $optionalAddOns = $package->optionalActivities->where('pivot.is_optional', true);
          $preselectedActivityIds = array_map('intval', old('activity_ids', request('activity_ids', [])));
        @endphp
        @if($optionalAddOns->isNotEmpty())
          <div class="bk-card">
            <h5><i class="bi bi-stars text-primary me-1"></i> Add Activities <span class="text-muted" style="font-weight:400;font-size:12px;">(Optional Add-ons)</span></h5>
            @foreach($optionalAddOns as $activity)
              @php $effectivePrice = (float) ($activity->pivot->price ?? $activity->price ?? 0); @endphp
              <div class="addon-check-row">
                <input type="checkbox" class="form-check-input activity-checkbox" name="activity_ids[]" id="activity_{{ $activity->id }}"
                  value="{{ $activity->id }}" data-price="{{ number_format($effectivePrice, 2, '.', '') }}"
                  {{ in_array($activity->id, $preselectedActivityIds, true) ? 'checked' : '' }}>
                <label for="activity_{{ $activity->id }}" style="flex:1;cursor:pointer;">
                  <div class="addon-check-name">{{ $activity->name }}</div>
                  @if($activity->pivot->note)<div class="addon-check-note">{{ $activity->pivot->note }}</div>@endif
                </label>
                <div class="addon-check-price">₹{{ number_format($effectivePrice) }} <span class="text-muted" style="font-weight:400;font-size:10px;">/ person</span></div>
              </div>
            @endforeach
          </div>
        @endif

        @php
          $optionalHotels = $package->hotels->where('pivot.is_optional', true);
          $preselectedHotelIds = array_map('intval', old('hotel_ids', request('hotel_ids', [])));
        @endphp
        @if($optionalHotels->isNotEmpty())
          <div class="bk-card">
            <h5><i class="bi bi-building text-primary me-1"></i> Add Hotel <span class="text-muted" style="font-weight:400;font-size:12px;">(Optional Stay — one per day)</span></h5>
            @foreach($optionalHotels as $hotel)
              @php
                $hotelRoomType = $hotel->pivot->room_type_id ? $hotel->roomTypes->firstWhere('id', $hotel->pivot->room_type_id) : null;
                $hotelPricePerNight = (float) ($hotel->pivot->price ?? $hotelRoomType?->sell_price ?? 0);
                $hotelNights = (int) ($hotel->pivot->nights ?? 1);
              @endphp
              <div class="addon-check-row">
                <input type="checkbox" class="form-check-input hotel-checkbox" name="hotel_ids[]" id="hotel_{{ $hotel->id }}"
                  value="{{ $hotel->id }}" data-price="{{ number_format($hotelPricePerNight, 2, '.', '') }}" data-nights="{{ $hotelNights }}"
                  {{ in_array($hotel->id, $preselectedHotelIds, true) ? 'checked' : '' }}>
                <label for="hotel_{{ $hotel->id }}" style="flex:1;cursor:pointer;">
                  <div class="addon-check-name">
                    {{ $hotel->name }} <span class="text-muted" style="font-weight:400;">({{ $hotel->star_rating }}★)</span>
                    @if($hotel->pivot->day_number)<span class="text-muted" style="font-weight:400;font-size:11px;"> · Day {{ $hotel->pivot->day_number }}</span>@endif
                  </div>
                  @if($hotel->pivot->note)<div class="addon-check-note">{{ $hotel->pivot->note }}</div>@endif
                </label>
                <div class="addon-check-price">₹{{ number_format($hotelPricePerNight) }} <span class="text-muted" style="font-weight:400;font-size:10px;">/ night &times; {{ $hotelNights }}N</span></div>
              </div>
            @endforeach
            <button type="button" class="btn btn-link btn-sm px-0 mt-1" id="clear-hotel-selection">Clear selection</button>
          </div>
        @endif

        <div class="bk-card">
          <h5><i class="bi bi-person text-primary me-1"></i> Traveller Details</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="bk-label">Full Name</div>
              <input type="text" name="traveller_name" class="bk-input" value="{{ old('traveller_name', auth()->user()->name) }}" required>
            </div>
            <div class="col-md-6">
              <div class="bk-label">Email</div>
              <input type="email" name="traveller_email" class="bk-input" value="{{ old('traveller_email', auth()->user()->email) }}" required>
            </div>
            <div class="col-md-6">
              <div class="bk-label">Phone</div>
              <input type="text" name="traveller_phone" class="bk-input" value="{{ old('traveller_phone') }}" required>
            </div>
            <div class="col-md-6">
              <div class="bk-label">GST Number <span class="text-muted normal-case">(optional)</span></div>
              <input type="text" name="gst_number" class="bk-input" value="{{ old('gst_number') }}">
            </div>
            <div class="col-12">
              <div class="bk-label">Address</div>
              <textarea name="traveller_address" rows="2" class="bk-input" required>{{ old('traveller_address') }}</textarea>
            </div>
            <div class="col-12">
              <div class="bk-label">Special Requests <span class="text-muted normal-case">(optional)</span></div>
              <textarea name="special_requests" rows="2" class="bk-input">{{ old('special_requests') }}</textarea>
            </div>
          </div>
        </div>

        @if($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
          </div>
        @endif
      </div>

      <div class="col-lg-4">
        <div class="bk-summary">
          <h5 style="font-weight:800;font-size:15px;margin-bottom:16px;">Price Summary</h5>
          <div class="bk-summary-row"><span>Base Fare</span><span id="sum-base">₹0</span></div>
          <div class="bk-summary-row discount"><span>Discount</span><span id="sum-discount">−₹0</span></div>
          <div class="bk-summary-row" id="sum-addons-row" style="display:none;"><span>Add-ons</span><span id="sum-addons">₹0</span></div>
          <div class="bk-summary-row" id="sum-hotel-row" style="display:none;"><span>Hotel</span><span id="sum-hotel">₹0</span></div>
          <div class="bk-summary-row discount" id="sum-coupon-row" style="display:none;"><span>Coupon Discount</span><span id="sum-coupon">−₹0</span></div>
          <div class="bk-summary-row"><span>Taxes &amp; Fees (5%)</span><span id="sum-taxes">₹0</span></div>
          <div class="bk-summary-row total"><span>Total</span><span id="sum-total">₹0</span></div>

          @include('bookings.partials._coupon_box', ['couponEndpoint' => route('bookings.apply-coupon', $package->slug)])

          <button type="submit" class="btn-pay">Proceed to Pay</button>
          <p class="text-center text-muted mt-2" style="font-size:11px;">Secured by PayU · Test Mode</p>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  // Live price preview — mirrors BookingController::calculateBreakdown() server-side.
  // The server always recomputes authoritatively on submit; this is display-only.
  (function () {
    var packageMrpPerPerson = {{ number_format((float) $package->price, 2, '.', '') }};
    var packageSellPerPerson = {{ number_format((float) ($package->discounted_price ?: $package->price), 2, '.', '') }};

    // A selected room type is its own source of truth for both the original (strike-
    // through) price and the discounted (sell) price — it no longer borrows the
    // package's MRP. Only falls back to package-level pricing when nothing is selected.
    function selectedRoom() {
      var select = document.getElementById('room_type_id');
      if (!select) return null;
      var opt = select.options[select.selectedIndex];
      if (!opt || !opt.value) return null;
      return {
        original: parseFloat(opt.dataset.originalPrice),
        discounted: parseFloat(opt.dataset.discountedPrice),
      };
    }

    function formatMoney(n) {
      return '₹' + n.toLocaleString('en-IN', { maximumFractionDigits: 2 });
    }

    // Sum of selected optional activities, per person — multiplied by travellers below,
    // exactly like room type pricing. Server re-fetches each price from the package's
    // own pivot on submit; this is display-only.
    function selectedActivitiesPerPerson() {
      var total = 0;
      document.querySelectorAll('.activity-checkbox:checked').forEach(function (cb) {
        total += parseFloat(cb.dataset.price) || 0;
      });
      return total;
    }

    // Hotel add-ons are a flat stay cost (price × nights) each, summed across every
    // selected day's hotel — not multiplied by travellers, unlike activities. Server
    // mirrors this exactly, re-fetching each price from the package's own pivot.
    function selectedHotelTotal() {
      var total = 0;
      document.querySelectorAll('.hotel-checkbox:checked').forEach(function (cb) {
        total += (parseFloat(cb.dataset.price) || 0) * (parseInt(cb.dataset.nights, 10) || 1);
      });
      return total;
    }

    function recalc() {
      var adults = parseInt(document.getElementById('adults').value || '1', 10);
      var children = parseInt(document.getElementById('children').value || '0', 10);
      var travellers = Math.max(1, adults + children);

      var room = selectedRoom();
      var mrpPerPerson = room && !isNaN(room.original) ? room.original : packageMrpPerPerson;
      var sellPerPerson = room && !isNaN(room.discounted) ? room.discounted : packageSellPerPerson;

      // Mirrors BookingController::calculateBreakdown() exactly — the reference/strike-
      // through price can never sit below the actual sell price (e.g. a Suite priced
      // above the package MRP has no discount, and Base Fare must equal its own price).
      var referencePerPerson = Math.max(mrpPerPerson, sellPerPerson);
      var baseFare = referencePerPerson * travellers;
      var discount = Math.max(0, referencePerPerson - sellPerPerson) * travellers;
      var activitiesTotal = selectedActivitiesPerPerson() * travellers;
      var hotelsTotal = selectedHotelTotal();
      var subtotal = baseFare - discount + activitiesTotal + hotelsTotal;

      // Display-only mirror of CouponService::applyToBreakdown() — set by the coupon
      // box's Apply/Remove handlers. The server re-validates and recomputes this exact
      // discount authoritatively in store() before it ever touches total_amount.
      var couponDiscount = window.appliedCouponDiscount || 0;
      var subtotalAfterCoupon = Math.max(0, subtotal - couponDiscount);
      var taxes = subtotalAfterCoupon * 0.05;
      var total = subtotalAfterCoupon + taxes;

      document.getElementById('sum-base').textContent = formatMoney(baseFare);
      document.getElementById('sum-discount').textContent = '−' + formatMoney(discount);
      var addonsRow = document.getElementById('sum-addons-row');
      if (addonsRow) {
        addonsRow.style.display = activitiesTotal > 0 ? 'flex' : 'none';
        document.getElementById('sum-addons').textContent = formatMoney(activitiesTotal);
      }
      var hotelRow = document.getElementById('sum-hotel-row');
      if (hotelRow) {
        hotelRow.style.display = hotelsTotal > 0 ? 'flex' : 'none';
        document.getElementById('sum-hotel').textContent = formatMoney(hotelsTotal);
      }
      var couponRow = document.getElementById('sum-coupon-row');
      if (couponRow) {
        couponRow.style.display = couponDiscount > 0 ? 'flex' : 'none';
        document.getElementById('sum-coupon').textContent = '−' + formatMoney(couponDiscount);
      }
      document.getElementById('sum-taxes').textContent = formatMoney(taxes);
      document.getElementById('sum-total').textContent = formatMoney(total);
    }

    window.recalcBookingSummary = recalc;

    ['adults', 'children', 'room_type_id'].forEach(function (id) {
      var el = document.getElementById(id);
      if (!el) return;
      el.addEventListener('input', recalc);
      el.addEventListener('change', recalc); // belt-and-braces: some browsers don't fire "input" for <select>
    });

    document.querySelectorAll('.activity-checkbox').forEach(function (el) {
      el.addEventListener('change', recalc);
    });

    document.querySelectorAll('.hotel-checkbox').forEach(function (el) {
      el.addEventListener('change', recalc);
    });

    var clearHotelBtn = document.getElementById('clear-hotel-selection');
    if (clearHotelBtn) {
      clearHotelBtn.addEventListener('click', function () {
        document.querySelectorAll('.hotel-checkbox').forEach(function (el) { el.checked = false; });
        recalc();
      });
    }

    recalc();
  })();
</script>
@endpush
