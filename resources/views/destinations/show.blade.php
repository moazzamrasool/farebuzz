@extends('layouts.app')

@php
  $seo = \App\Support\Seo\SeoResolver::resolve(
    $destination,
    $destination->name.' – FareBuzzer',
    $destination->description,
    $destination->cover_image,
    route('destinations.show', $destination->slug),
  );
@endphp
@include('partials._seo_head', ['seo' => $seo])

@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/asset/css/destination-tiles.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/asset/css/listing-filters.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/asset/css/package-card.css') }}">
<style>
  :root { --blue: #005fcc; --orange: #f47b20; }
  body { font-family: 'Inter', sans-serif; background: #f5f5f5; }

  .hp-hero {
    position: relative; height: 380px;
    background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.62)),
                url('{{ \App\Support\MediaUrl::resolve($destination->cover_image) ?? 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1400&q=80' }}') center/cover no-repeat;
    display: flex; align-items: center; justify-content: center; text-align: center;
  }
  .hp-hero-text h1 { color:#fff; font-size:40px; font-weight:800; margin-bottom:8px; }
  .hp-hero-desc, .hp-hero-desc p { color:rgba(255,255,255,0.85); font-size:16px; max-width:640px; margin:0 auto; }
  .hp-hero-desc p { margin-top:0; margin-bottom:0; }

  .dest-section { margin-top:40px; }
  .dest-section-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
  .dest-section-head h2 { font-size:22px; font-weight:800; margin:0; }
  .dest-section-head a { font-size:13px; font-weight:700; color:var(--blue); text-decoration:none; }

  @media(max-width:768px) {
    .hp-hero { height:260px; }
    .hp-hero-text h1 { font-size:26px; }
  }
</style>
@endpush

@section('content')

<section class="hp-hero">
  <div class="hp-hero-text">
    <h1>{{ $destination->name }}</h1>
    @if($destination->description)
      <div class="hp-hero-desc">{!! $destination->description !!}</div>
    @endif
  </div>
</section>

<div class="container" style="margin-bottom:48px;">

  @if($packages->isNotEmpty())
    <div class="dest-section">
      <div class="dest-section-head">
        <h2>Holiday Packages in {{ $destination->name }}</h2>
        <a href="{{ route('packages.byDestination', $destination->slug) }}">View all</a>
      </div>
      <div class="row g-4">
        @foreach($packages as $package)
          <div class="col-12 col-md-6 col-lg-4">
            @include('packages._card', ['package' => $package])
          </div>
        @endforeach
      </div>
    </div>
  @endif

  @if($hotels->isNotEmpty())
    <div class="dest-section">
      <div class="dest-section-head">
        <h2>Hotels in {{ $destination->name }}</h2>
        <a href="{{ route('hotels.index', ['destination_id' => $destination->id]) }}">View all</a>
      </div>
      <div class="row g-4">
        @foreach($hotels as $hotel)
          <div class="col-12 col-md-6 col-lg-4">
            @include('hotels._card', ['hotel' => $hotel])
          </div>
        @endforeach
      </div>
    </div>
  @endif

  @if($activities->isNotEmpty())
    <div class="dest-section">
      <div class="dest-section-head">
        <h2>Things to Do in {{ $destination->name }}</h2>
        <a href="{{ route('activities.index', ['destination_id' => $destination->id]) }}">View all</a>
      </div>
      <div class="row g-4">
        @foreach($activities as $activity)
          <div class="col-12 col-md-6 col-lg-4">
            @include('activities._card', ['activity' => $activity])
          </div>
        @endforeach
      </div>
    </div>
  @endif

  @if($packages->isEmpty() && $hotels->isEmpty() && $activities->isEmpty())
    <div class="dest-section text-center text-muted py-5">Nothing published for {{ $destination->name }} yet — check back soon.</div>
  @endif

  @if($destination->seo_content)
    <div class="dest-section" id="seo-content">
      <div class="dest-section-head">
        <h2>{{ $destination->name }} Travel Guide</h2>
      </div>
      <div class="rich-text-content ck-content" style="font-size:14px;color:#444;line-height:1.7;">{!! $destination->seo_content !!}</div>
    </div>
  @endif

</div>

@endsection
