@csrf
@php $cpn = $coupon ?? null; @endphp

<div class="form-row">
  <div class="form-group col-md-4">
    <label for="code">Coupon Code</label>
    <input type="text" name="code" id="code" class="form-control text-uppercase @error('code') is-invalid @enderror"
      value="{{ old('code', $cpn->code ?? '') }}" placeholder="e.g. WELCOME10" style="text-transform:uppercase;" required>
    @error('code') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-8">
    <label for="title">Title</label>
    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
      value="{{ old('title', $cpn->title ?? '') }}" placeholder="e.g. Flat 10% off on all holiday packages" required>
    @error('title') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<div class="form-group">
  <label for="description">Description / T&amp;Cs</label>
  <textarea name="description" id="description" rows="2" class="form-control @error('description') is-invalid @enderror"
    placeholder="Shown to the customer at checkout / on the homepage offer card.">{{ old('description', $cpn->description ?? '') }}</textarea>
  @error('description') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-row">
  <div class="form-group col-md-3">
    <label for="discount_type">Discount Type</label>
    <select name="discount_type" id="discount_type" class="form-control @error('discount_type') is-invalid @enderror">
      <option value="percentage" {{ old('discount_type', $cpn->discount_type ?? '') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
      <option value="fixed" {{ old('discount_type', $cpn->discount_type ?? '') === 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
    </select>
    @error('discount_type') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-3">
    <label for="discount_value">Discount Value</label>
    <input type="number" step="0.01" min="0.01" name="discount_value" id="discount_value" class="form-control @error('discount_value') is-invalid @enderror"
      value="{{ old('discount_value', $cpn->discount_value ?? '') }}" required>
    @error('discount_value') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-3" id="max_discount_wrap">
    <label for="max_discount_amount">Max Discount Cap (₹)</label>
    <input type="number" step="0.01" min="0" name="max_discount_amount" id="max_discount_amount" class="form-control @error('max_discount_amount') is-invalid @enderror"
      value="{{ old('max_discount_amount', $cpn->max_discount_amount ?? '') }}" placeholder="Only for % discounts">
    @error('max_discount_amount') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-3">
    <label for="min_booking_amount">Min Booking Amount (₹)</label>
    <input type="number" step="0.01" min="0" name="min_booking_amount" id="min_booking_amount" class="form-control @error('min_booking_amount') is-invalid @enderror"
      value="{{ old('min_booking_amount', $cpn->min_booking_amount ?? 0) }}">
    @error('min_booking_amount') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-3">
    <label for="valid_from">Valid From</label>
    <input type="date" name="valid_from" id="valid_from" class="form-control @error('valid_from') is-invalid @enderror"
      value="{{ old('valid_from', optional($cpn->valid_from ?? null)->format('Y-m-d')) }}" required>
    @error('valid_from') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-3">
    <label for="valid_to">Valid To</label>
    <input type="date" name="valid_to" id="valid_to" class="form-control @error('valid_to') is-invalid @enderror"
      value="{{ old('valid_to', optional($cpn->valid_to ?? null)->format('Y-m-d')) }}" required>
    @error('valid_to') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-3">
    <label for="usage_limit">Total Usage Limit <small class="text-muted">(blank = unlimited)</small></label>
    <input type="number" min="1" name="usage_limit" id="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror"
      value="{{ old('usage_limit', $cpn->usage_limit ?? '') }}">
    @error('usage_limit') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-3">
    <label for="per_user_limit">Per-User Limit</label>
    <input type="number" min="1" name="per_user_limit" id="per_user_limit" class="form-control @error('per_user_limit') is-invalid @enderror"
      value="{{ old('per_user_limit', $cpn->per_user_limit ?? 1) }}">
    @error('per_user_limit') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-4">
    <label for="applicable_to">Applicable To</label>
    <select name="applicable_to" id="applicable_to" class="form-control @error('applicable_to') is-invalid @enderror">
      @foreach(['all' => 'All Bookings', 'packages' => 'Holiday Packages Only', 'hotels' => 'Hotels Only', 'specific_packages' => 'Specific Package(s)', 'specific_hotels' => 'Specific Hotel(s)'] as $value => $label)
        <option value="{{ $value }}" {{ old('applicable_to', $cpn->applicable_to ?? 'all') === $value ? 'selected' : '' }}>{{ $label }}</option>
      @endforeach
    </select>
    @error('applicable_to') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-8" id="applicable_packages_wrap">
    <label for="applicable_packages">Select Package(s)</label>
    <select name="applicable_ids[]" id="applicable_packages" class="form-control" multiple size="4">
      @php $selectedIds = old('applicable_ids', $cpn->applicable_ids ?? []); @endphp
      @foreach($packages as $package)
        <option value="{{ $package->id }}" {{ in_array($package->id, $selectedIds) ? 'selected' : '' }}>{{ $package->title }}</option>
      @endforeach
    </select>
  </div>
  <div class="form-group col-md-8" id="applicable_hotels_wrap">
    <label for="applicable_hotels">Select Hotel(s)</label>
    <select name="applicable_ids[]" id="applicable_hotels" class="form-control" multiple size="4">
      @foreach($hotels as $hotel)
        <option value="{{ $hotel->id }}" {{ in_array($hotel->id, $selectedIds ?? old('applicable_ids', $cpn->applicable_ids ?? [])) ? 'selected' : '' }}>{{ $hotel->name }}</option>
      @endforeach
    </select>
  </div>
</div>

<hr>
<div class="form-row">
  <div class="form-group col-md-6">
    <label for="banner_image">Homepage Offer Banner <small class="text-muted">(optional, size: 400x250)</small></label>
    <input type="file" name="banner_image" id="banner_image" class="form-control-file @error('banner_image') is-invalid @enderror" accept="image/*">
    @error('banner_image') <span class="text-danger d-block">{{ $message }}</span> @enderror
    @if($cpn && $cpn->banner_image)
      <div class="mt-2">
        <img src="{{ asset('storage/'.$cpn->banner_image) }}" alt="{{ $cpn->title }}" style="width:100px;height:64px;object-fit:cover;border-radius:6px;">
      </div>
    @endif
  </div>
  <div class="form-group col-md-6">
    <label for="banner_link">Homepage Offer Link <small class="text-muted">(optional)</small></label>
    <input type="text" name="banner_link" id="banner_link" class="form-control @error('banner_link') is-invalid @enderror"
      value="{{ old('banner_link', $cpn->banner_link ?? '') }}" placeholder="https:// or a route path">
    @error('banner_link') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>
<small class="text-muted d-block mb-3">Or, to control the exact image/tag/copy shown on the homepage, link this coupon from a card under Homepage / CMS → Homepage Sections → Offers instead.</small>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="status">Status</label>
    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
      <option value="active" {{ old('status', $cpn->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
      <option value="inactive" {{ old('status', $cpn->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<button type="submit" class="btn btn-primary">{{ $cpn ? 'Update' : 'Create' }} Coupon</button>
<a href="{{ route('crm.coupons.index') }}" class="btn btn-secondary">Cancel</a>

@push('scripts')
<script>
  (function () {
    var discountType = document.getElementById('discount_type');
    var maxDiscountWrap = document.getElementById('max_discount_wrap');
    var applicableTo = document.getElementById('applicable_to');
    var packagesWrap = document.getElementById('applicable_packages_wrap');
    var hotelsWrap = document.getElementById('applicable_hotels_wrap');

    function toggleMaxDiscount() {
      maxDiscountWrap.style.display = discountType.value === 'percentage' ? '' : 'none';
    }

    function toggleApplicable() {
      packagesWrap.style.display = applicableTo.value === 'specific_packages' ? '' : 'none';
      hotelsWrap.style.display = applicableTo.value === 'specific_hotels' ? '' : 'none';
    }

    discountType.addEventListener('change', toggleMaxDiscount);
    applicableTo.addEventListener('change', toggleApplicable);
    toggleMaxDiscount();
    toggleApplicable();
  })();
</script>
@endpush
