@extends('layouts.app')

@php
  $seo = \App\Support\Seo\SeoResolver::resolve(
    $pageSeo ?? null,
    'Hotels – FareBuzzer',
    'Browse handpicked hotels across India and international destinations, from budget stays to luxury resorts.',
    null,
    \App\Support\Seo\CanonicalUrl::forListing(),
  );
@endphp
@include('partials._seo_head', ['seo' => $seo])

@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/asset/css/destination-tiles.css') }}">
<style>
  :root { --blue: #005fcc; --orange: #f47b20; }
  body { font-family: 'Inter', sans-serif; background: #f5f5f5; }
  .hp-hero {
    position: relative; padding: 60px 0 90px;
    background: linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.55)),
                url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1400&q=80') center/cover no-repeat;
    display: flex; align-items: center; justify-content: center; text-align: center;
  }
  .hp-hero-text h1 { color:#fff; font-size:36px; font-weight:800; margin-bottom:8px; }
  .hp-hero-text p  { color:rgba(255,255,255,0.85); font-size:16px; margin-bottom:28px; }
  .hotel-search-card { margin-top:-56px; margin-bottom:40px; padding:10px; }
  /* This page's search bar is a single self-contained form (no tabs, no extra
     rows above it), so the button sits inline as the last cell of the field
     row instead of reusing the homepage hero's absolutely-positioned floating
     button — that pattern relies on extra content pushing the card tall
     enough for the button to float clear underneath, which this shorter card
     doesn't have, so it ended up overlapping the fields instead. */
  .hotel-search-row { align-items: stretch; margin-bottom: 0; }
  .hotel-search-btn-cell { flex: 0 0 auto; border-right: none !important; padding: 8px !important; justify-content: center; }
  .btn-search-inline {
    background: linear-gradient(135deg, #0d5cff 0%, #0046d5 100%);
    color: #fff; border: none; border-radius: 10px; padding: 0 32px; height: 100%; min-height: 56px;
    font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: .3px; cursor: pointer;
    white-space: nowrap; display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    box-shadow: 0 4px 16px rgba(0,70,213,0.35); transition: transform .2s ease, box-shadow .2s ease;
  }
  .btn-search-inline:hover { transform: scale(1.02); box-shadow: 0 6px 20px rgba(0,70,213,0.5); }
  @media (max-width: 768px) {
    .hotel-search-btn-cell { flex: 1 1 100%; width: 100%; }
    .btn-search-inline { width: 100%; padding: 14px; }
  }
  .hotel-card { background:#fff; border-radius:12px; box-shadow:0 1px 8px rgba(0,0,0,0.07); overflow:hidden; height:100%; display:flex; flex-direction:column; transition:transform .2s, box-shadow .2s; }
  .hotel-card:hover { transform:translateY(-4px); box-shadow:0 8px 24px rgba(0,0,0,0.12); }
  .hotel-card-img { height:160px; overflow:hidden; }
  .hotel-card-img img { width:100%; height:100%; object-fit:cover; }
  .hotel-card-body { padding:14px; flex:1; display:flex; flex-direction:column; }
  .hotel-card-title { font-size:14px; font-weight:700; color:#111; margin-bottom:4px; }
  .hotel-card-star i { color:#f47b20; font-size:11px; }
  .hotel-card-price { margin-top:auto; font-size:13px; color:#005fcc; font-weight:700; }
  .section-heading { font-size:22px; font-weight:800; margin-bottom:20px; }
</style>
@endpush

@section('content')

<section class="hp-hero">
  <div class="hp-hero-text">
    <h1>Find Your Perfect Stay</h1>
    <p>Search hotels across India and around the world, hand-picked by FareBuzzer</p>
  </div>
</section>

<div class="container">
  <div class="search-card hotel-search-card">
    <form method="GET" action="{{ route('hotels.index') }}">
      <div class="mmt-search-group hotel-search-row">
        <div class="mmt-field-cell flex-grow-2 fb-autocomplete">
          <span class="mmt-label">City / Area / Hotel Name</span>
          <input type="text" id="lp-city" name="destination" class="mmt-input-main" placeholder="Where are you going?"
                 data-autocomplete="location" data-hidden-target="lp-destination-id" autocomplete="off">
          <input type="hidden" id="lp-destination-id" name="destination_id">
          <div class="fb-autocomplete-menu"></div>
        </div>

        <div class="mmt-field-cell date-cell">
          <span class="mmt-label">Check-in</span>
          <div class="date-display-wrap">
            <span class="mmt-input-val">{{ now()->format('d M') }}</span>
          </div>
          <input type="date" name="checkin" class="mmt-date-hidden" value="{{ now()->toDateString() }}">
        </div>

        <div class="mmt-field-cell date-cell">
          <span class="mmt-label">Check-out</span>
          <div class="date-display-wrap">
            <span class="mmt-input-val">{{ now()->addDay()->format('d M') }}</span>
          </div>
          <input type="date" name="checkout" class="mmt-date-hidden" value="{{ now()->addDay()->toDateString() }}">
        </div>

        <div class="mmt-field-cell select-cell">
          <span class="mmt-label">Guests &amp; Rooms</span>
          <select name="rooms" class="form-control">
            <option value="2-1" selected>2 Guests &middot; 1 Room</option>
            <option value="1-1">1 Guest &middot; 1 Room</option>
            <option value="3-1">3 Guests &middot; 1 Room</option>
            <option value="4-2">4 Guests &middot; 2 Rooms</option>
            <option value="6-3">6 Guests &middot; 3 Rooms</option>
          </select>
        </div>

        <div class="mmt-field-cell hotel-search-btn-cell">
          <button type="submit" class="btn-search-inline"><i class="bi bi-search"></i> Search Hotels</button>
        </div>
      </div>
    </form>
  </div>

  @if($stayHotels->isNotEmpty())
    <h2 class="section-heading">For your stay in {{ $featuredDestination->name }}</h2>
    <div class="row g-4 mb-5">
      @foreach($stayHotels as $hotel)
        <div class="col-6 col-md-3">
          <a href="{{ route('hotels.show', $hotel->slug) }}" class="text-decoration-none">
            <div class="hotel-card">
              <div class="hotel-card-img">
                <img src="{{ \App\Support\MediaUrl::resolve($hotel->cover_image) ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=70' }}" alt="{{ $hotel->name }}">
              </div>
              <div class="hotel-card-body">
                <div class="hotel-card-title">{{ $hotel->name }}</div>
                <div class="hotel-card-star">
                  @for($i = 0; $i < $hotel->star_rating; $i++)<i class="bi bi-star-fill"></i>@endfor
                </div>
                @if($hotel->fromPrice)
                  <div class="hotel-card-price">From ₹{{ number_format($hotel->fromPrice) }}/night</div>
                @endif
              </div>
            </div>
          </a>
        </div>
      @endforeach
    </div>
  @endif

  <h2 class="section-heading">Explore Hotels by Destination</h2>
  <div class="row g-4 mb-5">
    @forelse($destinations as $destination)
      <div class="col-6 col-md-4 col-lg-3">
        <a href="{{ route('hotels.index', ['destination_id' => $destination->id]) }}" class="dest-tile">
          <img src="{{ \App\Support\MediaUrl::resolve($destination->cover_image) ?? 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=70' }}" alt="{{ $destination->name }}" loading="lazy">
          <div class="dest-tile-overlay">
            <div class="dest-tile-name">
              {{ $destination->name }}
              <span class="dest-tile-tag">{{ $destination->local === 'domestic' ? 'IN' : $destination->country }} {{ \App\Support\CountryFlag::emoji($destination->country) }}</span>
            </div>
            <div class="dest-tile-count">{{ $destination->hotels_count }} {{ Str::plural('Hotel', $destination->hotels_count) }}</div>
          </div>
        </a>
      </div>
    @empty
      <div class="col-12 text-center text-muted py-5">No destinations available right now.</div>
    @endforelse
  </div>
</div>

@endsection
