@extends('layouts.app')

@section('title', 'Hotels – FareBuzzer')

@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/asset/css/listing-filters.css') }}">
<style>
  :root { --blue: #005fcc; }
  body { font-family: 'Inter', sans-serif; background: #f5f5f5; }
  .hp-hero {
    position: relative; height: 220px;
    background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.62)),
                url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1400&q=80') center/cover no-repeat;
    display: flex; align-items: center; justify-content: center; text-align: center;
  }
  .hp-hero-text h1 { color:#fff; font-size:30px; font-weight:800; margin-bottom:4px; }
  .hp-hero-text p  { color:rgba(255,255,255,0.85); font-size:14px; }
  .results-bar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:20px; }
  .results-count { font-size:15px; font-weight:600; color:#222; }
  .stay-summary { font-size:12px; color:#666; }
  .hotel-card { background:#fff; border-radius:12px; box-shadow:0 1px 8px rgba(0,0,0,0.07); overflow:hidden; height:100%; display:flex; flex-direction:column; transition:transform .2s, box-shadow .2s; }
  .hotel-card:hover { transform:translateY(-4px); box-shadow:0 8px 24px rgba(0,0,0,0.12); }
  .hotel-card-img { height:180px; overflow:hidden; }
  .hotel-card-img img { width:100%; height:100%; object-fit:cover; }
  .hotel-card-body { padding:16px; flex:1; display:flex; flex-direction:column; }
  .hotel-card-title { font-size:15px; font-weight:700; color:#111; margin-bottom:4px; }
  .hotel-card-star i { color:#f47b20; font-size:12px; }
  .hotel-card-address { font-size:12px; color:#888; margin:6px 0 10px; }
  .hotel-card-address i { color:var(--blue); }
  .hotel-score { background:#16a34a; color:#fff; border-radius:8px; padding:3px 10px; font-size:12px; font-weight:700; display:inline-block; }
  .hotel-amenity-pill { display:inline-block; background:#f5f5f5; border-radius:6px; padding:2px 8px; font-size:10px; margin:2px 4px 2px 0; color:#555; }
  .hotel-card-footer { margin-top:auto; display:flex; align-items:flex-end; justify-content:space-between; }
  .hotel-price-from { font-size:11px; color:#888; }
  .hotel-price-value { font-size:17px; font-weight:800; color:#111; }
  .btn-view-hotel { background:var(--blue); color:#fff; border:none; border-radius:8px; font-size:12px; font-weight:700; padding:8px 16px; text-decoration:none; }
</style>
@endpush

@section('content')

<section class="hp-hero">
  <div class="hp-hero-text">
    <h1>Hotels</h1>
    <p>{{ $checkIn->format('d M Y') }} &ndash; {{ $checkOut->format('d M Y') }} &middot; {{ $nights }} {{ Str::plural('Night', $nights) }} &middot; {{ $adults }} {{ Str::plural('Guest', $adults) }} &middot; {{ $rooms }} {{ Str::plural('Room', $rooms) }}</p>
  </div>
</section>

<div class="container" style="margin-top:28px;">
  @php
    $chips = [];
    if (request('destination')) { $chips['destination'] = 'Location: '.request('destination'); }
    if (request('rating_min')) { $chips['rating_min'] = request('rating_min').'+ Guest Rating'; }
    if (request('price_min') || request('price_max')) { $chips['price_min'] = '₹'.request('price_min', 0).' - ₹'.(request('price_max') ?: 'Any'); }
  @endphp
  @include('partials._filter_chips', ['chips' => $chips])

  <div class="row g-4">
    <div class="col-lg-3">
      <div class="fb-filter-sidebar mb-4">
        <form method="GET">
          <input type="hidden" name="checkin" value="{{ request('checkin') }}">
          <input type="hidden" name="checkout" value="{{ request('checkout') }}">
          <input type="hidden" name="rooms" value="{{ request('rooms') }}">
          <input type="hidden" name="destination_id" value="{{ request('destination_id') }}">

          <h6>Location</h6>
          <input type="text" name="destination" class="form-control form-control-sm mb-2" placeholder="City or hotel name" value="{{ request('destination') }}">

          <h6>Price per Night</h6>
          <div class="d-flex gap-2 mb-2">
            <input type="number" name="price_min" class="form-control form-control-sm" placeholder="Min" value="{{ request('price_min') }}">
            <input type="number" name="price_max" class="form-control form-control-sm" placeholder="Max" value="{{ request('price_max') }}">
          </div>

          <h6>Star Rating</h6>
          @foreach([5, 4, 3, 2, 1] as $stars)
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="star_rating[]" value="{{ $stars }}" id="star-{{ $stars }}" {{ in_array($stars, (array) request('star_rating', [])) ? 'checked' : '' }}>
              <label class="form-check-label" for="star-{{ $stars }}">{{ $stars }} Star</label>
            </div>
          @endforeach

          <h6 class="mt-2">Guest Rating</h6>
          @foreach(['9' => 'Excellent 9+', '8' => 'Very Good 8+', '7' => 'Good 7+'] as $value => $label)
            <div class="form-check">
              <input class="form-check-input" type="radio" name="rating_min" value="{{ $value }}" id="rating-{{ $value }}" {{ (string) request('rating_min') === $value ? 'checked' : '' }}>
              <label class="form-check-label" for="rating-{{ $value }}">{{ $label }}</label>
            </div>
          @endforeach

          @if($amenities->isNotEmpty())
            <h6 class="mt-2">Amenities</h6>
            @foreach($amenities as $amenity)
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity->id }}" id="am-{{ $amenity->id }}" {{ in_array($amenity->id, (array) request('amenities', [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="am-{{ $amenity->id }}">{{ $amenity->name }}</label>
              </div>
            @endforeach
          @endif

          <button type="submit" class="btn-apply-filters">Apply Filters</button>
        </form>
      </div>
    </div>

    <div class="col-lg-9">
      <div class="results-bar">
        <div class="results-count">{{ $hotels->total() }} hotels found</div>
        <form method="GET" class="d-flex align-items-center gap-2">
          @foreach(request()->except(['sort', 'page']) as $key => $value)
            @if(is_array($value))
              @foreach($value as $v)
                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
              @endforeach
            @else
              <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
          @endforeach
          <label class="mb-0" style="font-size:12px;color:#888;">Sort by</label>
          <select name="sort" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
            <option value="" {{ !request('sort') ? 'selected' : '' }}>Recommended</option>
            <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
            <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
            <option value="popularity" {{ request('sort') === 'popularity' ? 'selected' : '' }}>Popularity</option>
          </select>
        </form>
      </div>

      <div class="row g-4">
        @forelse($hotels as $hotel)
          <div class="col-12 col-md-6">
            @include('hotels._card', ['hotel' => $hotel])
          </div>
        @empty
          <div class="col-12 text-center text-muted py-5">No hotels matched your search. Try clearing a filter.</div>
        @endforelse
      </div>

      <div class="mt-4">
        {{ $hotels->links() }}
      </div>
    </div>
  </div>
</div>

@endsection
