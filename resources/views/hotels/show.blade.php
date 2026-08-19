@extends('layouts.app')

@php
  $seo = \App\Support\Seo\SeoResolver::resolve(
    $hotel,
    $hotel->name.' – FareBuzzer',
    $hotel->description,
    $hotel->cover_image,
    route('hotels.show', $hotel->slug),
  );
@endphp
@include('partials._seo_head', ['seo' => $seo])

@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/asset/css/package-card.css') }}">
<style>
  :root { --blue:#005fcc; --orange:#f47b20; }
  body { font-family:'Inter',sans-serif; background:#f5f5f5; color:#111; }
  .pkg-hero {
    position:relative; height:320px;
    background: linear-gradient(rgba(0,0,0,0.35), rgba(0,0,0,0.55)),
      url('{{ \App\Support\MediaUrl::resolve($hotel->cover_image) ?? "https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1400&q=80" }}') center/cover no-repeat;
    display:flex; align-items:flex-end;
  }
  .pkg-hero-inner { padding:36px 0 32px; width:100%; }
  .pkg-title-main { font-size:32px; font-weight:800; color:#fff; margin-bottom:8px; }
  .pkg-meta-row { display:flex; flex-wrap:wrap; align-items:center; gap:16px; }
  .badge-meta { background:rgba(255,255,255,0.18); color:#fff; border:1px solid rgba(255,255,255,0.3); border-radius:20px; padding:5px 14px; font-size:12px; font-weight:600; display:flex; align-items:center; gap:6px; }
  .sec-title { font-size:20px; font-weight:800; margin-bottom:16px; }
  .hotel-amenity { display:inline-flex; align-items:center; gap:5px; background:#f5f5f5; border-radius:6px; padding:4px 10px; font-size:11px; margin:3px; }
  .gallery-strip { display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr)); gap:8px; }
  .gallery-strip img { width:100%; height:140px; object-fit:cover; border-radius:10px; }
  .dates-bar { background:#fff; border-radius:12px; box-shadow:0 1px 8px rgba(0,0,0,0.07); padding:16px; margin:24px 0; }
  .room-card { background:#fff; border-radius:12px; box-shadow:0 1px 8px rgba(0,0,0,0.07); padding:16px; margin-bottom:16px; display:flex; gap:16px; flex-wrap:wrap; }
  .room-card-img { width:180px; height:130px; border-radius:8px; overflow:hidden; flex-shrink:0; }
  .room-card-img img { width:100%; height:100%; object-fit:cover; }
  .room-card-body { flex:1; min-width:200px; }
  .room-card-name { font-size:16px; font-weight:700; }
  .room-card-meta { font-size:12px; color:#666; margin:4px 0; }
  .room-badge { display:inline-block; font-size:10px; font-weight:700; border-radius:6px; padding:3px 8px; margin-right:6px; }
  .room-badge-meal { background:#e8f4ff; color:var(--blue); }
  .room-badge-refundable { background:#e7f9ef; color:#16a34a; }
  .room-badge-nonrefundable { background:#fdecec; color:#dc2626; }
  .room-card-price { text-align:right; min-width:160px; }
  .room-price-value { font-size:20px; font-weight:800; color:#111; }
  .room-price-sub { font-size:11px; color:#888; }
  .btn-book-room { background:var(--blue); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; padding:10px 20px; text-decoration:none; display:inline-block; margin-top:8px; }
  .rating-bar-row { display:flex; align-items:center; gap:10px; margin-bottom:6px; font-size:12px; }
  .rating-bar-track { flex:1; height:6px; background:#eee; border-radius:4px; overflow:hidden; }
  .rating-bar-fill { height:100%; background:var(--blue); }
  .review-card { border-bottom:1px solid #eee; padding:14px 0; }
  .review-score-badge { background:#16a34a; color:#fff; border-radius:6px; padding:2px 8px; font-size:12px; font-weight:700; }
  .similar-card { background:#fff; border-radius:12px; box-shadow:0 1px 8px rgba(0,0,0,0.07); overflow:hidden; }
  .similar-card img { width:100%; height:130px; object-fit:cover; }
  .map-embed { width:100%; height:280px; border:0; border-radius:12px; }
</style>
@endpush

@section('content')

<section class="pkg-hero">
  <div class="container pkg-hero-inner">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color:rgba(255,255,255,.75);text-decoration:none;font-size:13px;">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('hotels.index') }}" style="color:rgba(255,255,255,.75);text-decoration:none;font-size:13px;">Hotels</a></li>
        <li class="breadcrumb-item active" style="color:rgba(255,255,255,.9);font-size:13px;">{{ $hotel->name }}</li>
      </ol>
    </nav>
    <h1 class="pkg-title-main">{{ $hotel->name }} <i class="bi bi-heart" style="font-size:20px;cursor:pointer;" title="Add to Wishlist"></i></h1>
    <div class="pkg-meta-row">
      <span class="badge-meta">
        @for($i = 0; $i < $hotel->star_rating; $i++)<i class="bi bi-star-fill"></i>@endfor
        &nbsp;{{ $hotel->star_rating }} Star
      </span>
      <span class="badge-meta"><i class="bi bi-geo-alt-fill"></i> {{ $hotel->address }}</span>
      @if($hotel->rating_score)
        <span class="badge-meta" style="background:#16a34a;border-color:#16a34a;">{{ $hotel->rating_score }}/10 &middot; {{ $hotel->review_count }} reviews</span>
      @endif
    </div>
  </div>
</section>

<div class="container" style="margin-top:32px;">
  <div class="row g-4">
    <div class="col-lg-8">
      @if(!empty($hotel->gallery_images))
        <div class="gallery-strip mb-4">
          @foreach($hotel->gallery_images as $image)
            <img src="{{ \App\Support\MediaUrl::resolve($image) }}" alt="{{ $hotel->name }}">
          @endforeach
        </div>
      @endif

      <h2 class="sec-title">About this hotel</h2>
      <p style="font-size:14px;color:#444;line-height:1.7;">{{ $hotel->description }}</p>

      @if($hotel->amenities->isNotEmpty())
        <h2 class="sec-title mt-4">Amenities</h2>
        <div class="d-flex flex-wrap">
          @foreach($hotel->amenities as $amenity)
            <span class="hotel-amenity"><i class="bi bi-{{ $amenity->icon ?: 'check2' }}"></i> {{ $amenity->name }}</span>
          @endforeach
        </div>
      @endif

      <div class="dates-bar">
        <form method="GET" class="row g-2 align-items-end">
          <div class="col-6 col-md-3">
            <label class="form-label" style="font-size:11px;color:#888;">Check-in</label>
            <input type="date" name="checkin" class="form-control form-control-sm" value="{{ $checkIn->toDateString() }}">
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label" style="font-size:11px;color:#888;">Check-out</label>
            <input type="date" name="checkout" class="form-control form-control-sm" value="{{ $checkOut->toDateString() }}">
          </div>
          <div class="col-4 col-md-2">
            <label class="form-label" style="font-size:11px;color:#888;">Adults</label>
            <input type="number" name="adults" min="1" max="20" class="form-control form-control-sm" value="{{ $adults }}">
          </div>
          <div class="col-4 col-md-2">
            <label class="form-label" style="font-size:11px;color:#888;">Children</label>
            <input type="number" name="children" min="0" max="20" class="form-control form-control-sm" value="{{ $children }}">
          </div>
          <div class="col-4 col-md-1">
            <label class="form-label" style="font-size:11px;color:#888;">Rooms</label>
            <input type="number" name="rooms_count" min="1" max="10" class="form-control form-control-sm" value="{{ $rooms }}">
          </div>
          <div class="col-12 col-md-1">
            <button type="submit" class="btn btn-primary btn-sm w-100">Update</button>
          </div>
        </form>
      </div>

      <h2 class="sec-title">Room Types <small class="text-muted" style="font-size:13px;font-weight:400;">for {{ $nights }} {{ Str::plural('night', $nights) }}, {{ $rooms }} {{ Str::plural('room', $rooms) }}</small></h2>
      @forelse($hotel->roomTypes as $room)
        @php $subtotal = $room->sellPrice * $nights * $rooms; @endphp
        <div class="room-card">
          <div class="room-card-img">
            <img src="{{ \App\Support\MediaUrl::resolve($room->images[0] ?? null) ?? \App\Support\MediaUrl::resolve($hotel->cover_image) ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&q=70' }}" alt="{{ $room->name }}">
          </div>
          <div class="room-card-body">
            <div class="room-card-name">{{ $room->name }}</div>
            <div class="room-card-meta">
              {{ $room->occupancy_adults }} Adult(s){{ $room->occupancy_children ? ', '.$room->occupancy_children.' Child(ren)' : '' }}
              @if($room->bed_type) &middot; {{ $room->bed_type }} @endif
              @if($room->size_sqft) &middot; {{ $room->size_sqft }} sqft @endif
            </div>
            <div>
              <span class="room-badge room-badge-meal">{{ $room->mealPlanLabel }}</span>
              @if($room->refundable)
                <span class="room-badge room-badge-refundable">Refundable</span>
              @else
                <span class="room-badge room-badge-nonrefundable">Non-refundable</span>
              @endif
            </div>
          </div>
          <div class="room-card-price">
            <div class="room-price-sub">₹{{ number_format($room->sellPrice) }} / night</div>
            <div class="room-price-value">₹{{ number_format($subtotal) }}</div>
            <div class="room-price-sub">for {{ $nights }} {{ Str::plural('night', $nights) }} &times; {{ $rooms }} {{ Str::plural('room', $rooms) }} + taxes</div>
            <a href="{{ route('bookings.hotel.form', ['hotel' => $hotel->slug, 'roomType' => $room->id, 'checkin' => $checkIn->toDateString(), 'checkout' => $checkOut->toDateString(), 'adults' => $adults, 'children' => $children, 'rooms_count' => $rooms]) }}" class="btn-book-room">Book Now</a>
          </div>
        </div>
      @empty
        <p class="text-muted">No bookable room types are available for this hotel yet.</p>
      @endforelse

      @if($hotel->latitude && $hotel->longitude)
        <h2 class="sec-title mt-4">Location</h2>
        <p style="font-size:13px;color:#666;">{{ $hotel->address }}</p>
        <iframe class="map-embed" loading="lazy" src="https://maps.google.com/maps?q={{ $hotel->latitude }},{{ $hotel->longitude }}&z=15&output=embed"></iframe>
      @elseif($hotel->address)
        <h2 class="sec-title mt-4">Location</h2>
        <iframe class="map-embed" loading="lazy" src="https://maps.google.com/maps?q={{ urlencode($hotel->address) }}&z=13&output=embed"></iframe>
      @endif

      @if($hotel->check_in_time || $hotel->check_out_time || $hotel->property_rules)
        <h2 class="sec-title mt-4">Property Rules</h2>
        <div class="d-flex gap-4 mb-2">
          @if($hotel->check_in_time)<span class="badge-meta" style="color:#111;background:#f5f5f5;">Check-in: {{ $hotel->check_in_time }}</span>@endif
          @if($hotel->check_out_time)<span class="badge-meta" style="color:#111;background:#f5f5f5;">Check-out: {{ $hotel->check_out_time }}</span>@endif
        </div>
        @if($hotel->property_rules)
          <ul style="font-size:13px;color:#555;">
            @foreach(explode("\n", $hotel->property_rules) as $rule)
              @if(trim($rule) !== '')<li>{{ trim($rule) }}</li>@endif
            @endforeach
          </ul>
        @endif
      @endif

      @if($hotel->reviews->isNotEmpty())
        <h2 class="sec-title mt-4">Guest Reviews</h2>
        <div class="d-flex align-items-center gap-4 mb-3">
          <div style="text-align:center;">
            <div style="font-size:32px;font-weight:800;color:#16a34a;">{{ $hotel->averageRating ?? $hotel->rating_score }}</div>
            <div style="font-size:11px;color:#888;">/ 10 &middot; {{ $hotel->reviews->count() }} reviews</div>
          </div>
          <div style="flex:1;max-width:320px;">
            @php $bars = $hotel->categoryRatingBars; @endphp
            @foreach(['location' => 'Location', 'cleanliness' => 'Cleanliness', 'service' => 'Service', 'value' => 'Value for Money'] as $key => $label)
              @if($bars[$key])
                <div class="rating-bar-row">
                  <span style="width:110px;">{{ $label }}</span>
                  <div class="rating-bar-track"><div class="rating-bar-fill" style="width:{{ $bars[$key] * 10 }}%"></div></div>
                  <span>{{ $bars[$key] }}</span>
                </div>
              @endif
            @endforeach
          </div>
        </div>

        @foreach($hotel->reviews->take(10) as $review)
          <div class="review-card">
            <div class="d-flex align-items-center gap-2 mb-1">
              <span class="review-score-badge">{{ $review->rating }}</span>
              <strong style="font-size:13px;">{{ $review->reviewer_name }}</strong>
              @if($review->verified)<span class="text-muted" style="font-size:11px;">&middot; Verified Stay</span>@endif
              <span class="text-muted" style="font-size:11px;margin-left:auto;">{{ $review->review_date->format('M Y') }}</span>
            </div>
            @if($review->comment)<p style="font-size:13px;color:#444;margin:0;">{{ $review->comment }}</p>@endif
          </div>
        @endforeach
      @endif

      @if($hotel->holidayPackages->isNotEmpty())
        <h2 class="sec-title mt-4">Holiday Packages Featuring This Hotel</h2>
        <div class="row g-4">
          @foreach($hotel->holidayPackages as $package)
            <div class="col-12 col-md-6">
              @include('packages._card', ['package' => $package])
            </div>
          @endforeach
        </div>
      @endif

      @if($similarHotels->isNotEmpty())
        <h2 class="sec-title mt-4">Similar Properties</h2>
        <div class="row g-4">
          @foreach($similarHotels as $similar)
            <div class="col-6 col-md-3">
              <a href="{{ route('hotels.show', $similar->slug) }}" class="text-decoration-none">
                <div class="similar-card">
                  <img src="{{ \App\Support\MediaUrl::resolve($similar->cover_image) ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&q=70' }}" alt="{{ $similar->name }}">
                  <div class="p-2">
                    <div style="font-size:13px;font-weight:700;color:#111;">{{ $similar->name }}</div>
                    @if($similar->fromPrice)
                      <div style="font-size:12px;color:#005fcc;font-weight:700;">From ₹{{ number_format($similar->fromPrice) }}</div>
                    @endif
                  </div>
                </div>
              </a>
            </div>
          @endforeach
        </div>
      @endif
    </div>

    <div class="col-lg-4">
      <div class="booking-sidebar" style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.1);padding:24px;position:sticky;top:20px;">
        <h5 style="font-size:15px;font-weight:700;margin-bottom:12px;">Your Stay</h5>
        <p style="font-size:13px;color:#666;">
          {{ $checkIn->format('d M Y') }} &ndash; {{ $checkOut->format('d M Y') }}<br>
          {{ $nights }} {{ Str::plural('Night', $nights) }} &middot; {{ $adults }} {{ Str::plural('Adult', $adults) }}{{ $children ? ', '.$children.' '.Str::plural('Child', $children) : '' }} &middot; {{ $rooms }} {{ Str::plural('Room', $rooms) }}
        </p>
        @if($hotel->roomTypes->isNotEmpty())
          <p style="font-size:12px;color:#888;">Prices start from</p>
          <div style="font-size:24px;font-weight:800;color:#111;">₹{{ number_format($hotel->roomTypes->min('sellPrice')) }}<span style="font-size:13px;font-weight:400;color:#888;"> / night</span></div>
        @endif
        <p style="font-size:12px;color:#666;margin-top:12px;">Pick a room type on the left to see the full price breakdown and book instantly.</p>
      </div>
    </div>
  </div>
</div>

@endsection
