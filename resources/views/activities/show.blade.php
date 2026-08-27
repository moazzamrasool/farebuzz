@extends('layouts.app')

@php
  $seo = \App\Support\Seo\SeoResolver::resolve(
    $activity,
    $activity->name.' – FareBuzzer',
    $activity->description,
    $activity->image,
    route('activities.show', $activity->slug),
  );
@endphp
@include('partials._seo_head', ['seo' => $seo])

@push('styles')
<style>
  :root { --blue:#005fcc; }
  body { font-family:'Inter',sans-serif; background:#f5f5f5; color:#111; }
  .pkg-hero {
    position:relative; height:360px;
    background: linear-gradient(rgba(0,0,0,0.35), rgba(0,0,0,0.55)),
      url('{{ \App\Support\MediaUrl::resolve($activity->image) ?? "https://images.unsplash.com/photo-1526772662000-3f88f10405ff?w=1400&q=80" }}') center/cover no-repeat;
    display:flex; align-items:flex-end;
  }
  .pkg-hero-inner { padding:36px 0 32px; width:100%; }
  .pkg-title-main { font-size:32px; font-weight:800; color:#fff; margin-bottom:8px; }
  .pkg-meta-row { display:flex; flex-wrap:wrap; align-items:center; gap:16px; }
  .badge-meta { background:rgba(255,255,255,0.18); color:#fff; border:1px solid rgba(255,255,255,0.3); border-radius:20px; padding:5px 14px; font-size:12px; font-weight:600; display:flex; align-items:center; gap:6px; }
  .sec-title { font-size:20px; font-weight:800; margin-bottom:16px; }
  .booking-sidebar { background:#fff; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.1); padding:24px; position:sticky; top:20px; }
  .book-price-block .curr { font-size:28px; font-weight:800; color:var(--blue); }
  .btn-book-main { background:var(--blue); color:#fff; border:none; border-radius:10px; font-size:15px; font-weight:700; padding:14px; width:100%; margin-top:12px; }
  .btn-enquire { background:#fff; color:var(--blue); border:2px solid var(--blue); border-radius:10px; font-size:14px; font-weight:700; padding:11px; width:100%; margin-top:10px; }
  .sim-card { background:#fff; border-radius:12px; box-shadow:0 1px 8px rgba(0,0,0,0.07); overflow:hidden; }
  .sim-card img { width:100%; height:120px; object-fit:cover; }
  .sim-card-body { padding:12px; }
  .sim-card-body h6 { font-size:13px; font-weight:700; }
  .tag { font-size:10px; font-weight:700; padding:3px 9px; border-radius:12px; text-transform:uppercase; letter-spacing:.4px; background:#dbeafe; color:#1d4ed8; display:inline-block; }
</style>
@endpush

@section('content')

<section class="pkg-hero">
  <div class="container pkg-hero-inner">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color:rgba(255,255,255,.75);text-decoration:none;font-size:13px;">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('activities.index') }}" style="color:rgba(255,255,255,.75);text-decoration:none;font-size:13px;">Activities</a></li>
        <li class="breadcrumb-item active" style="color:rgba(255,255,255,.9);font-size:13px;">{{ $activity->name }}</li>
      </ol>
    </nav>
    <h1 class="pkg-title-main">{{ $activity->name }}</h1>
    <div class="pkg-meta-row">
      @if($activity->activityCategory)<span class="badge-meta">{{ $activity->activityCategory->name }}</span>@endif
      @if($activity->destination)<span class="badge-meta"><i class="bi bi-geo-alt-fill"></i> {{ $activity->destination->name }}</span>@endif
    </div>
  </div>
</section>

<div class="container" style="margin-top:32px;">
  <div class="row g-4">
    <div class="col-lg-8">
      <h2 class="sec-title">About this activity</h2>
      <p style="font-size:14px;color:#444;line-height:1.7;">{{ $activity->description }}</p>

      @if($related->isNotEmpty())
        <hr style="margin:32px 0;">
        <h2 class="sec-title">You Might Also Like</h2>
        <div class="row g-3">
          @foreach($related as $sim)
            <div class="col-6 col-md-3">
              <a href="{{ route('activities.show', $sim->slug) }}" class="text-decoration-none">
                <div class="sim-card">
                  <img src="{{ \App\Support\MediaUrl::resolve($sim->image) ?? 'https://images.unsplash.com/photo-1526772662000-3f88f10405ff?w=400&q=70' }}" alt="{{ $sim->name }}">
                  <div class="sim-card-body">
                    <h6>{{ $sim->name }}</h6>
                    @if($sim->price)<div style="font-size:12px;color:var(--blue);font-weight:700;">₹{{ number_format($sim->price) }}</div>@endif
                  </div>
                </div>
              </a>
            </div>
          @endforeach
        </div>
      @endif
    </div>

    <div class="col-lg-4">
      <div class="booking-sidebar">
        @if($activity->price)
          <div class="book-price-block mb-2">
            <div class="curr">₹{{ number_format($activity->price) }}</div>
            <div style="font-size:12px;color:#888;">per person</div>
          </div>
        @endif
        <button type="button" class="btn-book-main">Book Now</button>
        <button type="button" class="btn-enquire">Enquire Now</button>
      </div>
    </div>
  </div>
</div>

@endsection
