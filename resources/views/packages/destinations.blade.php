@extends('layouts.app')

@section('title', $heading.' – FareBuzzer')

@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/asset/css/destination-tiles.css') }}">
<style>
  :root { --blue: #005fcc; --orange: #f47b20; --bg: #f5f5f5; }
  body { font-family: 'Inter', sans-serif; background: var(--bg); }

  .hp-hero {
    position: relative; height: 380px;
    background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.62)),
                url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1400&q=80') center/cover no-repeat;
    display: flex; align-items: center; justify-content: center; text-align: center;
  }
  .hp-hero-text h1 { color:#fff; font-size:40px; font-weight:800; margin-bottom:8px; }
  .hp-hero-text p  { color:rgba(255,255,255,0.85); font-size:16px; }

  @media(max-width:768px) {
    .hp-hero { height:260px; }
    .hp-hero-text h1 { font-size:26px; }
  }
</style>
@endpush

@section('content')

<section class="hp-hero">
  <div class="hp-hero-text">
    <h1>{{ $heading }}</h1>
    <p>{{ $subheading }}</p>
  </div>
</section>

<div class="container" style="margin-top:28px; margin-bottom:48px;">
  <div class="row g-4">
    @forelse($destinations as $destination)
      <div class="col-6 col-md-4 col-lg-3">
        <a href="{{ route('packages.byDestination', $destination->slug) }}" class="dest-tile">
          <img src="{{ \App\Support\MediaUrl::resolve($destination->cover_image) ?? 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=70' }}" alt="{{ $destination->name }}" loading="lazy">
          <div class="dest-tile-overlay">
            <div class="dest-tile-name">
              {{ $destination->name }}
              <span class="dest-tile-tag">{{ $destination->local === 'domestic' ? 'IN' : $destination->country }} {{ \App\Support\CountryFlag::emoji($destination->country) }}</span>
            </div>
            @if($destination->cheapest_price)
              <div class="dest-tile-price">From ₹{{ number_format($destination->cheapest_price) }}</div>
            @endif
            <div class="dest-tile-count">{{ $destination->packages_count }} {{ Str::plural('Package', $destination->packages_count) }}</div>
          </div>
        </a>
      </div>
    @empty
      <div class="col-12 text-center text-muted py-5">No destinations available right now.</div>
    @endforelse
  </div>
</div>

@endsection
