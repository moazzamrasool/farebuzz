@extends('layouts.app')

@php
  $seo = \App\Support\Seo\SeoResolver::resolve(
    null,
    'All Destinations – FareBuzzer',
    'Browse every India and international destination FareBuzzer covers — packages, hotels and activities in one place.',
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
    position: relative; height: 260px;
    background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.62)),
                url('https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=1400&q=80') center/cover no-repeat;
    display: flex; align-items: center; justify-content: center; text-align: center;
  }
  .hp-hero-text h1 { color:#fff; font-size:32px; font-weight:800; margin-bottom:6px; }
  .hp-hero-text p  { color:rgba(255,255,255,0.85); font-size:14px; max-width:560px; margin:0 auto; }

  .dest-tabs { display:flex; gap:8px; flex-wrap:wrap; padding:4px 0; margin-bottom:20px; }
  .dest-tabs .tab-btn {
    display:inline-block; padding:8px 18px; border-radius:20px; border:1px solid #ddd;
    font-size:13px; font-weight:600; color:#444; text-decoration:none; background:#fff;
  }
  .dest-tabs .tab-btn:hover { border-color:var(--blue); color:var(--blue); }
  .dest-tabs .tab-btn.active { background:var(--blue); border-color:var(--blue); color:#fff; }

  .dest-tile { height: 190px; }

  @media(max-width:768px) {
    .hp-hero { height:200px; }
    .hp-hero-text h1 { font-size:22px; }
    .dest-tile { height: 150px; }
  }
</style>
@endpush

@section('content')

<section class="hp-hero">
  <div class="hp-hero-text">
    <h1>All Destinations</h1>
    <p>Explore every India and international destination we cover — packages, hotels and things to do in one place.</p>
  </div>
</section>

<div class="container" style="margin-top:28px; margin-bottom:48px;">

  <div class="dest-tabs">
    <a href="{{ route('destinations.index') }}" class="tab-btn {{ !$local ? 'active' : '' }}">All</a>
    <a href="{{ route('destinations.index', ['local' => 'domestic']) }}" class="tab-btn {{ $local === 'domestic' ? 'active' : '' }}">India</a>
    <a href="{{ route('destinations.index', ['local' => 'international']) }}" class="tab-btn {{ $local === 'international' ? 'active' : '' }}">International</a>
  </div>

  <div class="row g-3">
    @forelse($destinations as $destination)
      <div class="col-6 col-md-4 col-lg-3">
        <a href="{{ route('destinations.show', $destination->slug) }}" class="dest-tile">
          <img src="{{ \App\Support\MediaUrl::resolve($destination->cover_image) ?? 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=70' }}" alt="{{ $destination->name }}" loading="lazy">
          @if($code = \App\Support\CountryFlag::code($destination->country))
            <img src="https://flagcdn.com/w80/{{ $code }}.png" alt="{{ $destination->country }} flag" class="dest-flag-badge">
          @endif
          <div class="dest-tile-overlay">
            <div class="dest-tile-name">
              {{ $destination->name }}
              <span class="dest-tile-tag">{{ $destination->local === 'domestic' ? 'India' : $destination->country }}</span>
            </div>
            @if($destination->packages_count)
              <div class="dest-tile-count">{{ $destination->packages_count }} {{ Str::plural('Package', $destination->packages_count) }}</div>
            @endif
          </div>
        </a>
      </div>
    @empty
      <div class="col-12 text-center text-muted py-5">No destinations available right now.</div>
    @endforelse
  </div>

  <div class="mt-4">
    {{ $destinations->links() }}
  </div>

</div>

@endsection
