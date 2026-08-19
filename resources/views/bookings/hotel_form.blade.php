@extends('layouts.app')

@section('title', 'Book '.$hotel->name.' – FareBuzzer')
@section('robots', 'noindex, follow')

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
</style>
@endpush

@section('content')
<div class="bk-wrap">
  <div class="bk-title">Complete Your Booking</div>
  <div class="bk-sub">{{ $hotel->name }} &middot; {{ $roomType->name }}</div>

  <form method="POST" action="{{ route('bookings.hotel.store', ['hotel' => $hotel->slug, 'roomType' => $roomType->id]) }}" id="hotel-booking-form">
    @csrf
    <div class="row g-4">
      <div class="col-lg-8">

        <div class="bk-card">
          <h5><i class="bi bi-calendar-check text-primary me-1"></i> Stay Details</h5>
          <div class="row g-3">
            <div class="col-md-3">
              <div class="bk-label">Check-in</div>
              <input type="date" name="check_in_date" id="check_in_date" class="bk-input" min="{{ now()->toDateString() }}" value="{{ old('check_in_date', $checkIn->toDateString()) }}" required>
            </div>
            <div class="col-md-3">
              <div class="bk-label">Check-out</div>
              <input type="date" name="check_out_date" id="check_out_date" class="bk-input" min="{{ $checkIn->copy()->addDay()->toDateString() }}" value="{{ old('check_out_date', $checkOut->toDateString()) }}" required>
            </div>
            <div class="col-md-2">
              <div class="bk-label">Rooms</div>
              <input type="number" name="rooms_count" id="rooms_count" class="bk-input" min="1" max="10" value="{{ old('rooms_count', $rooms) }}" required>
            </div>
            <div class="col-md-2">
              <div class="bk-label">Adults</div>
              <input type="number" name="adults" id="adults" class="bk-input" min="1" max="20" value="{{ old('adults', $adults) }}" required>
            </div>
            <div class="col-md-2">
              <div class="bk-label">Children</div>
              <input type="number" name="children" id="children" class="bk-input" min="0" max="20" value="{{ old('children', $children) }}">
            </div>
          </div>
        </div>

        <div class="bk-card">
          <h5><i class="bi bi-person text-primary me-1"></i> Guest Details</h5>
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
          <div class="bk-summary-row"><span>Room</span><span>{{ $roomType->name }}</span></div>
          <div class="bk-summary-row"><span>Rate / Night</span><span id="sum-rate">₹{{ number_format($roomType->sellPrice) }}</span></div>
          <div class="bk-summary-row"><span>Base Fare</span><span id="sum-base">₹0</span></div>
          <div class="bk-summary-row discount"><span>Discount</span><span id="sum-discount">−₹0</span></div>
          <div class="bk-summary-row discount" id="sum-coupon-row" style="display:none;"><span>Coupon Discount</span><span id="sum-coupon">−₹0</span></div>
          <div class="bk-summary-row"><span>Taxes &amp; Fees (5%)</span><span id="sum-taxes">₹0</span></div>
          <div class="bk-summary-row total"><span>Total</span><span id="sum-total">₹0</span></div>

          @include('bookings.partials._coupon_box', ['couponEndpoint' => route('bookings.hotel.apply-coupon', ['hotel' => $hotel->slug, 'roomType' => $roomType->id])])

          <button type="submit" class="btn-pay">Proceed to Pay</button>
          <p class="text-center text-muted mt-2" style="font-size:11px;">Secured by PayU &middot; Test Mode</p>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  // Live price preview only — mirrors BookingController::calculateHotelBreakdown()
  // server-side, which always recomputes authoritatively on submit.
  (function () {
    var originalPrice = {{ number_format((float) $roomType->price, 2, '.', '') }};
    var sellPrice = {{ number_format((float) $roomType->sellPrice, 2, '.', '') }};

    function formatMoney(n) {
      return '₹' + n.toLocaleString('en-IN', { maximumFractionDigits: 2 });
    }

    function nightsBetween(checkIn, checkOut) {
      var inDate = new Date(checkIn);
      var outDate = new Date(checkOut);
      var diff = Math.round((outDate - inDate) / 86400000);
      return diff > 0 ? diff : 1;
    }

    function recalc() {
      var rooms = parseInt(document.getElementById('rooms_count').value || '1', 10);
      var nights = nightsBetween(document.getElementById('check_in_date').value, document.getElementById('check_out_date').value);

      var referencePrice = Math.max(originalPrice, sellPrice);
      var baseFare = referencePrice * nights * rooms;
      var sellFare = sellPrice * nights * rooms;
      var discount = Math.max(0, baseFare - sellFare);

      // Display-only mirror of CouponService::applyToBreakdown() — set by the coupon
      // box's Apply/Remove handlers. The server re-validates and recomputes this exact
      // discount authoritatively in hotelStore() before it ever touches total_amount.
      var couponDiscount = window.appliedCouponDiscount || 0;
      var sellFareAfterCoupon = Math.max(0, sellFare - couponDiscount);
      var taxes = sellFareAfterCoupon * 0.05;
      var total = sellFareAfterCoupon + taxes;

      document.getElementById('sum-base').textContent = formatMoney(baseFare);
      document.getElementById('sum-discount').textContent = '−' + formatMoney(discount);
      var couponRow = document.getElementById('sum-coupon-row');
      if (couponRow) {
        couponRow.style.display = couponDiscount > 0 ? 'flex' : 'none';
        document.getElementById('sum-coupon').textContent = '−' + formatMoney(couponDiscount);
      }
      document.getElementById('sum-taxes').textContent = formatMoney(taxes);
      document.getElementById('sum-total').textContent = formatMoney(total);
    }

    window.recalcBookingSummary = recalc;

    ['check_in_date', 'check_out_date', 'rooms_count'].forEach(function (id) {
      document.getElementById(id).addEventListener('input', recalc);
      document.getElementById(id).addEventListener('change', recalc);
    });

    recalc();
  })();
</script>
@endpush
