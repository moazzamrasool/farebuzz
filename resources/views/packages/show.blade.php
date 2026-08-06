@extends('layouts.app')

@section('title', $package->meta_title ?: $package->title.' – Holiday Package – FareBuzzer')
@section('meta_description', $package->meta_description ?: Str::limit(strip_tags($package->overview), 160))
@if($package->meta_keywords)
  @section('meta_keywords', $package->meta_keywords)
@endif
@section('og_title', $package->meta_title ?: $package->title)
@section('og_description', $package->meta_description ?: Str::limit(strip_tags($package->overview), 160))
@if($package->photos->first())
  @section('og_image', \App\Support\MediaUrl::resolve($package->photos->first()->path))
@endif
@section('og_url', route('packages.show', $package->slug))
@section('canonical', route('packages.show', $package->slug))

@if($package->faqs->isNotEmpty())
  @push('jsonld')
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      @foreach($package->faqs as $faq)
      {
        "@type": "Question",
        "name": @json(strip_tags($faq->question)),
        "acceptedAnswer": {
          "@type": "Answer",
          "text": @json(strip_tags($faq->answer))
        }
      }@if(!$loop->last),@endif
      @endforeach
    ]
  }
  </script>
  @endpush
@endif

@php
  $photos = $package->photos;
  $heroImage = \App\Support\MediaUrl::resolve($photos->first()?->path) ?? 'https://images.unsplash.com/photo-1587474260584-136574528ed5?w=1400&q=80';
  // pluck() (not map->title) returns a plain Support Collection even when empty,
  // avoiding an Eloquent Collection::merge() crash ("getKey() on string") that
  // occurs when merging an empty/model Eloquent Collection with a string Collection.
  $inclusions = $package->inclusionFeatures->pluck('title')->merge($package->customInclusions->pluck('title'));
  $exclusions = $package->exclusionFeatures->pluck('title')->merge($package->customExclusions->pluck('title'));
  $optionalAddOns = $package->optionalActivities->where('pivot.is_optional', true);
  $optionalHotels = $package->hotels->where('pivot.is_optional', true);

  // Day-wise grouping: any hotel/activity whose pivot day_number matches a real
  // itinerary day renders under that day; everything else (no day set, or a day
  // number that doesn't match any itinerary row) falls into "General Add-ons" so
  // nothing silently disappears.
  $itineraryDayNumbers = $package->itineraries->pluck('day_number')->all();
  $hotelsByDay = $package->hotels->filter(fn ($h) => in_array($h->pivot->day_number, $itineraryDayNumbers))->groupBy('pivot.day_number');
  $activitiesByDay = $optionalAddOns->filter(fn ($a) => in_array($a->pivot->day_number, $itineraryDayNumbers))->groupBy('pivot.day_number');
  $generalHotels = $package->hotels->reject(fn ($h) => in_array($h->pivot->day_number, $itineraryDayNumbers));
  $generalActivities = $optionalAddOns->reject(fn ($a) => in_array($a->pivot->day_number, $itineraryDayNumbers));
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/asset/css/package-card.css') }}">
<style>
  :root { --blue:#005fcc; --orange:#f47b20; --bg:#f5f5f5; }
  body { font-family:'Inter',sans-serif; background:var(--bg); color:#111; }

  .pkg-hero {
    position:relative; height:460px;
    background: linear-gradient(rgba(0,0,0,0.35), rgba(0,0,0,0.55)),
      url('{{ $heroImage }}') center/cover no-repeat;
    display:flex; align-items:flex-end;
  }
  .pkg-hero-inner { padding:36px 0 32px; width:100%; }
  .pkg-hero-inner .breadcrumb { margin-bottom:10px; }
  .pkg-hero-inner .breadcrumb-item a { color:rgba(255,255,255,0.75); text-decoration:none; font-size:13px; }
  .pkg-hero-inner .breadcrumb-item.active { color:rgba(255,255,255,0.9); font-size:13px; }
  .pkg-hero-inner .breadcrumb-item+.breadcrumb-item::before { color:rgba(255,255,255,0.5); }
  .pkg-title-main { font-size:36px; font-weight:800; color:#fff; margin-bottom:10px; }
  .pkg-meta-row { display:flex; flex-wrap:wrap; align-items:center; gap:16px; }
  .pkg-meta-row .badge-meta { background:rgba(255,255,255,0.18); color:#fff; border:1px solid rgba(255,255,255,0.3); border-radius:20px; padding:5px 14px; font-size:12px; font-weight:600; display:flex; align-items:center; gap:6px; }
  .pkg-rating-hero { display:flex; align-items:center; gap:6px; color:#fff; font-size:13px; }
  .pkg-rating-hero .stars { color:#fbbf24; }

  .pkg-sticky-nav { background:#fff; border-bottom:1px solid #e8e8e8; position:sticky; top:0; z-index:900; box-shadow:0 2px 6px rgba(0,0,0,0.06); }
  .pkg-sticky-nav ul { list-style:none; margin:0; padding:0; display:flex; gap:0; overflow-x:auto; scrollbar-width:none; }
  .pkg-sticky-nav ul::-webkit-scrollbar { display:none; }
  .pkg-sticky-nav ul li a { display:block; padding:14px 22px; font-size:13px; font-weight:600; color:#555; text-decoration:none; border-bottom:3px solid transparent; white-space:nowrap; transition:color .2s, border-color .2s; }
  .pkg-sticky-nav ul li a:hover, .pkg-sticky-nav ul li a.active { color:var(--blue); border-bottom-color:var(--blue); }

  .gallery-strip { display:grid; grid-template-columns:2fr 1fr 1fr; grid-template-rows:1fr 1fr; gap:6px; border-radius:14px; overflow:hidden; height:320px; }
  .gallery-strip .g-main { grid-row:1/3; }
  .gallery-strip img { width:100%; height:100%; object-fit:cover; }
  .gallery-more { position:relative; }
  .gallery-more img { filter:brightness(.55); }
  .gallery-more-label { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; color:#fff; font-size:15px; font-weight:700; }

  .sec-title { font-size:20px; font-weight:800; color:#111; margin-bottom:16px; }
  .section-divider { border:none; border-top:1px solid #eee; margin:32px 0; }

  .highlight-pill { background:#f0f6ff; color:var(--blue); border-radius:20px; padding:6px 16px; font-size:12px; font-weight:600; display:inline-flex; align-items:center; gap:6px; }

  .incl-item { display:flex; align-items:flex-start; gap:10px; margin-bottom:10px; font-size:13px; }
  .incl-item i { margin-top:2px; flex-shrink:0; }

  .itinerary-item { background:#fff; border-radius:12px; border:1.5px solid #eee; margin-bottom:12px; overflow:hidden; }
  .itin-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; cursor:pointer; user-select:none; transition:background .15s; }
  .itin-header:hover { background:#f8f9ff; }
  .itin-day-badge { background:var(--blue); color:#fff; border-radius:8px; padding:4px 12px; font-size:11px; font-weight:700; flex-shrink:0; margin-right:14px; }
  .itin-title { font-size:14px; font-weight:700; flex:1; }
  .itin-sub { font-size:12px; color:#888; margin-top:2px; }
  .itin-body { padding:0 20px 18px; display:none; }
  .itin-body.open { display:block; }
  .itin-body p { font-size:13px; color:#444; line-height:1.7; margin-bottom:10px; }
  .itin-body ul { padding-left:18px; }
  .itin-body ul li { font-size:13px; color:#444; margin-bottom:6px; }
  .itin-meal-tags { display:flex; gap:8px; flex-wrap:wrap; margin-top:8px; }
  .meal-tag { background:#dcfce7; color:#16a34a; border-radius:12px; padding:3px 10px; font-size:11px; font-weight:600; }
  .itin-date-badge { font-weight:700; color:var(--blue); }
  .itin-date-badge:not(:empty)::before { content:"·"; margin:0 6px; color:#ccc; }
  .itin-day-subhead { font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#888; margin:16px 0 10px; }

  .hotel-detail-card { background:#fff; border-radius:12px; box-shadow:0 1px 8px rgba(0,0,0,0.07); display:flex; gap:0; overflow:hidden; }
  .hotel-detail-card img { width:200px; object-fit:cover; flex-shrink:0; }
  .hotel-detail-body { padding:20px; flex:1; }
  .hotel-star i { color:#f47b20; font-size:13px; }
  .hotel-amenity { display:inline-flex; align-items:center; gap:5px; background:#f5f5f5; border-radius:6px; padding:4px 10px; font-size:11px; margin:3px; }

  .review-card { background:#fff; border-radius:12px; padding:20px; box-shadow:0 1px 8px rgba(0,0,0,0.06); height:100%; }
  .review-avatar { width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:15px; flex-shrink:0; background:#dbeafe; color:var(--blue); }
  .review-stars i { color:#f47b20; font-size:12px; }
  .review-text { font-size:13px; color:#444; line-height:1.65; margin:10px 0; }
  .review-date { font-size:11px; color:#aaa; }

  .booking-sidebar { background:#fff; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.1); padding:28px 24px; position:sticky; top:20px; }
  .book-price-block .orig { font-size:14px; color:#aaa; text-decoration:line-through; }
  .book-price-block .curr { font-size:32px; font-weight:800; color:var(--blue); line-height:1.1; }
  .book-price-block .per { font-size:12px; color:#888; }
  .book-price-block .saving { background:#dcfce7; color:#16a34a; border-radius:6px; padding:3px 10px; font-size:12px; font-weight:700; display:inline-block; margin-top:4px; }
  .form-label-sm { font-size:11px; font-weight:600; color:#888; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; }
  .form-control-sm2 { border:1.5px solid #e0e0e0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; }
  .form-control-sm2:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(0,95,204,.1); }

  .travel-date-block { background:#fff8ec; border:1.5px solid #f5c563; border-radius:10px; padding:12px; margin-bottom:12px; }
  .travel-date-block .form-label-sm { color:#b45309; }
  .travel-date-block .form-control-sm2 { border-color:#f5c563; }
  .travel-date-block .form-control-sm2.is-invalid { border-color:#dc2626; box-shadow:0 0 0 3px rgba(220,38,38,.1); }
  .travel-date-hint { font-size:11px; color:#b45309; margin-top:6px; display:flex; align-items:center; gap:5px; }
  .btn-book-main { background:var(--blue); color:#fff; border:none; border-radius:10px; font-size:15px; font-weight:700; padding:14px; width:100%; cursor:pointer; transition:background .2s; }
  .btn-book-main:hover { background:#004bb5; }
  .btn-enquire { background:#fff; color:var(--blue); border:2px solid var(--blue); border-radius:10px; font-size:14px; font-weight:700; padding:11px; width:100%; cursor:pointer; transition:background .2s; margin-top:10px; }
  .btn-enquire:hover { background:#f0f6ff; }
  .trust-row { display:flex; gap:8px; flex-wrap:wrap; margin-top:16px; }
  .trust-item { display:flex; align-items:center; gap:5px; font-size:11px; color:#666; }
  .trust-item i { color:#16a34a; }

  .tag { font-size:10px; font-weight:700; padding:3px 9px; border-radius:12px; text-transform:uppercase; letter-spacing:.4px; }
  .tag-deal { background:#dcfce7; color:#16a34a; }
  .tag-new  { background:#dbeafe; color:#1d4ed8; }

  .overview-box { background:#fff; border-radius:12px; padding:18px 16px; text-align:center; box-shadow:0 1px 8px rgba(0,0,0,0.06); }
  .overview-box i { font-size:22px; margin-bottom:8px; color:var(--blue); }
  .overview-box .ov-label { font-size:10px; color:#888; text-transform:uppercase; letter-spacing:.5px; }
  .overview-box .ov-val { font-size:15px; font-weight:700; color:#111; margin-top:2px; }

  .addon-card { background:#fff; border-radius:12px; box-shadow:0 1px 8px rgba(0,0,0,0.06); overflow:hidden; display:flex; height:100%; transition:box-shadow .2s; }
  .addon-card.is-selected { box-shadow:0 0 0 2px var(--blue), 0 1px 8px rgba(0,0,0,0.06); }
  .addon-img { width:110px; object-fit:cover; flex-shrink:0; }
  .addon-body { padding:14px 16px; flex:1; min-width:0; }
  .addon-name { font-size:14px; font-weight:700; margin-bottom:2px; }
  .addon-price { font-size:14px; font-weight:800; color:var(--blue); white-space:nowrap; }
  .addon-price .per { font-size:10px; color:#888; font-weight:600; }
  .addon-desc { font-size:12px; color:#777; line-height:1.5; margin:4px 0 10px; }
  .addon-toggle { display:flex; align-items:center; gap:6px; cursor:pointer; font-size:12px; font-weight:700; color:var(--blue); margin:0; }
  .addon-toggle input { cursor:pointer; }

  /* Selected-only summary rows (MakeMyTrip pattern) — the real radio/checkbox inputs
     live in the "Change" modal; these rows are pure display, toggled by JS. */
  .day-summary-slot { margin-bottom:10px; }
  .day-summary-row { background:#fff; border:1.5px solid #eee; border-radius:10px; padding:12px 14px; margin-bottom:8px; }
  .summary-row-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
  .summary-row-label { font-size:11px; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:.4px; }
  .summary-change-link, .summary-remove-link { font-size:12px; font-weight:700; color:var(--blue); text-decoration:none; cursor:pointer; }
  .summary-remove-link { color:#dc2626; }
  .summary-row-body { display:flex; align-items:center; gap:12px; }
  .summary-row-body img { width:64px; height:64px; object-fit:cover; border-radius:8px; flex-shrink:0; }
  .summary-row-info { flex:1; min-width:0; }
  .summary-row-name { font-size:14px; font-weight:700; }
  .summary-row-loc { font-size:11px; color:#888; margin-top:2px; }
  .summary-row-price { font-size:14px; font-weight:800; color:var(--blue); white-space:nowrap; text-align:right; }
  .summary-row-price .per { font-size:10px; color:#888; font-weight:600; }
  .btn-add-to-day { background:#f0f6ff; color:var(--blue); border:1.5px dashed var(--blue); border-radius:8px; font-size:12px; font-weight:700; padding:8px 14px; cursor:pointer; display:inline-flex; align-items:center; gap:6px; }
  .btn-add-to-day:hover { background:#e0edff; }
  .day-summary-empty { padding:4px 0; }

  @media(max-width:768px) {
    .pkg-hero { height:300px; }
    .pkg-title-main { font-size:24px; }
    .gallery-strip { grid-template-columns:1fr 1fr; grid-template-rows:auto; height:200px; }
    .gallery-strip .g-main { grid-row:auto; grid-column:1/3; }
    .hotel-detail-card { flex-direction:column; }
    .hotel-detail-card img { width:100%; height:180px; }
  }
</style>
@endpush

@section('content')

<section class="pkg-hero">
  <div class="container pkg-hero-inner">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('packages.index') }}">Holiday Packages</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $package->title }}</li>
      </ol>
    </nav>
    <h1 class="pkg-title-main">{{ $package->title }}</h1>
    <div class="pkg-meta-row">
      <span class="badge-meta"><i class="bi bi-calendar3"></i> {{ $package->days }}D / {{ $package->nights }}N</span>
      <span class="badge-meta"><i class="bi bi-geo-alt-fill"></i> {{ $package->destination?->name }}</span>
      @if($package->averageRating)
        <span class="pkg-rating-hero"><span class="stars">@include('packages._stars', ['rating' => $package->averageRating])</span> {{ $package->averageRating }} ({{ $package->reviews->count() }} reviews)</span>
      @endif
      @if($package->is_best_seller)
        <span class="tag tag-deal">Best Seller</span>
      @endif
    </div>
  </div>
</section>

<nav class="pkg-sticky-nav">
  <div class="container">
    <ul>
      <li><a href="#overview">Overview</a></li>
      @if($package->itineraries->isNotEmpty())<li><a href="#itinerary">Day Plan</a></li>@endif
      @if($inclusions->isNotEmpty() || $exclusions->isNotEmpty())<li><a href="#inclusions">Inclusions</a></li>@endif
      @if($generalHotels->isNotEmpty() || $generalActivities->isNotEmpty())<li><a href="#general-addons">Add-ons</a></li>@endif
      @if($photos->isNotEmpty())<li><a href="#gallery">Photos</a></li>@endif
      @if($package->reviews->isNotEmpty())<li><a href="#reviews">Reviews</a></li>@endif
      @if($package->faqs->isNotEmpty())<li><a href="#faq">FAQ</a></li>@endif
    </ul>
  </div>
</nav>

<div class="container" style="margin-top:32px;">
  <div class="row g-4">
    <div class="col-lg-8">

      @if($photos->isNotEmpty())
        <div id="gallery" class="gallery-strip mb-4">
          <div class="g-main"><img src="{{ \App\Support\MediaUrl::resolve($photos->first()->path) }}" alt="{{ $package->focus_keyword ?: $package->title }}"></div>
          @foreach($photos->skip(1)->take(3) as $photo)
            <div><img src="{{ \App\Support\MediaUrl::resolve($photo->path) }}" alt="{{ $package->focus_keyword ?: $package->title }}"></div>
          @endforeach
          @if($photos->count() > 4)
            <div class="gallery-more">
              <img src="{{ \App\Support\MediaUrl::resolve($photos->get(4)->path) }}" alt="{{ $package->focus_keyword ?: $package->title }}">
              <div class="gallery-more-label">+{{ $photos->count() - 4 }} Photos</div>
            </div>
          @endif
        </div>
      @endif

      <div id="overview">
        <h2 class="sec-title">Overview</h2>
        <div class="rich-text-content ck-content" style="font-size:14px;color:#444;line-height:1.7;">{!! $package->overview !!}</div>
        @if($package->activities->isNotEmpty())
          <div class="d-flex flex-wrap gap-2 mt-3 mb-3">
            @foreach($package->activities as $activity)
              <span class="highlight-pill"><i class="bi bi-check2"></i> {{ $activity->name }}</span>
            @endforeach
          </div>
        @endif
        @if($package->places_to_visit)
          <div class="overview-box mt-1"><i class="bi bi-signpost-split d-block"></i><div class="ov-label">Places to Visit</div><div class="ov-val">{{ $package->places_to_visit }}</div></div>
        @endif
        <div class="row g-3 mt-1">
          <div class="col-6 col-md-3">
            <div class="overview-box"><i class="bi bi-calendar3 d-block"></i><div class="ov-label">Duration</div><div class="ov-val">{{ $package->days }}D/{{ $package->nights }}N</div></div>
          </div>
          <div class="col-6 col-md-3">
            <div class="overview-box"><i class="bi bi-building d-block"></i><div class="ov-label">Hotel Category</div><div class="ov-val">{{ $package->hotel_category ?: '—' }}</div></div>
          </div>
          <div class="col-6 col-md-3">
            <div class="overview-box"><i class="bi bi-cup-hot-fill d-block"></i><div class="ov-label">Meals</div><div class="ov-val">{{ $package->meals ?: '—' }}</div></div>
          </div>
          <div class="col-6 col-md-3">
            <div class="overview-box"><i class="bi bi-translate d-block"></i><div class="ov-label">Language</div><div class="ov-val">{{ $package->language ?: '—' }}</div></div>
          </div>
        </div>
      </div>

      @if($package->itineraries->isNotEmpty())
        <hr class="section-divider">
        <div id="itinerary">
          <h2 class="sec-title">Day-by-Day Itinerary <span style="font-size:13px;font-weight:500;color:#888;">— pick a Travel Date to see real dates, and add day-wise hotels/activities</span></h2>
          @foreach($package->itineraries as $day)
            @php
              $dayHotels = $hotelsByDay->get($day->day_number, collect());
              $dayActivities = $activitiesByDay->get($day->day_number, collect());
            @endphp
            <div class="itinerary-item">
              <div class="itin-header" onclick="toggleDay(this)">
                <span class="itin-day-badge">Day {{ $day->day_number }}</span>
                <div class="flex-grow-1">
                  <div class="itin-title">{{ $day->title }}</div>
                  <div class="itin-sub">
                    @if($day->route_summary){{ $day->route_summary }}@endif
                    <span class="itin-date-badge" id="day-date-{{ $day->day_number }}" data-day="{{ $day->day_number }}"></span>
                  </div>
                </div>
                <i class="bi bi-chevron-down ms-3" style="color:#888;transition:transform .3s;"></i>
              </div>
              <div class="itin-body {{ $loop->first ? 'open' : '' }}">
                @if($day->detail)<div class="rich-text-content ck-content">{!! $day->detail !!}</div>@endif
                @if(!empty($day->bullet_points))
                  <ul>
                    @foreach($day->bullet_points as $point)
                      <li>{{ $point }}</li>
                    @endforeach
                  </ul>
                @endif
                @if(!empty($day->meal_tags))
                  <div class="itin-meal-tags">
                    @foreach($day->meal_tags as $meal)
                      <span class="meal-tag"><i class="bi bi-cup-hot-fill me-1"></i>{{ $meal }}</span>
                    @endforeach
                  </div>
                @endif

                @if($dayHotels->isNotEmpty())
                  @include('packages._day_hotel_block', ['dayHotels' => $dayHotels, 'dayNumber' => $day->day_number])
                @endif

                @if($dayActivities->isNotEmpty())
                  @include('packages._day_activity_block', ['dayActivities' => $dayActivities, 'dayNumber' => $day->day_number])
                @endif
              </div>
            </div>
          @endforeach
        </div>
      @endif

      @if($inclusions->isNotEmpty() || $exclusions->isNotEmpty())
        <hr class="section-divider">
        <div id="inclusions">
          <h2 class="sec-title">Inclusions &amp; Exclusions</h2>
          <div class="row g-4">
            <div class="col-md-6">
              @foreach($inclusions as $line)
                <div class="incl-item"><i class="bi bi-check-circle-fill" style="color:#16a34a;"></i><span>{{ $line }}</span></div>
              @endforeach
            </div>
            <div class="col-md-6">
              @foreach($exclusions as $line)
                <div class="incl-item"><i class="bi bi-x-circle-fill" style="color:#dc2626;"></i><span>{{ $line }}</span></div>
              @endforeach
            </div>
          </div>
        </div>
      @endif

      @if($generalHotels->isNotEmpty() || $generalActivities->isNotEmpty())
        <hr class="section-divider">
        <div id="general-addons">
          <h2 class="sec-title">Add-ons <span style="font-size:13px;font-weight:500;color:#888;">— not tied to a specific day</span></h2>
          @if($generalActivities->isNotEmpty())
            @include('packages._day_activity_block', ['dayActivities' => $generalActivities, 'dayNumber' => 'general'])
          @endif
          @if($generalHotels->isNotEmpty())
            @include('packages._day_hotel_block', ['dayHotels' => $generalHotels, 'dayNumber' => 'general'])
          @endif
        </div>
      @endif

      @if($package->reviews->isNotEmpty())
        <hr class="section-divider">
        <div id="reviews">
          <h2 class="sec-title">Guest Reviews</h2>
          <div class="row g-3">
            @foreach($package->reviews as $review)
              <div class="col-12 col-md-6">
                <div class="review-card">
                  <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="review-avatar">{{ collect(explode(' ', $review->reviewer_name))->map(fn($w) => mb_substr($w, 0, 1))->join('') }}</div>
                    <div>
                      <div style="font-size:13px;font-weight:700;">{{ $review->reviewer_name }}</div>
                      <div class="review-stars">@include('packages._stars', ['rating' => $review->rating])</div>
                    </div>
                    <div class="review-date ms-auto">{{ $review->review_date?->format('F Y') }}</div>
                  </div>
                  <p class="review-text">"{{ $review->comment }}"</p>
                  @if($review->verified)
                    <div style="font-size:11px;color:#888;"><i class="bi bi-check-circle-fill" style="color:#16a34a;"></i> Verified Booking</div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      @if($package->faqs->isNotEmpty())
        <hr class="section-divider">
        <div id="faq">
          <h2 class="sec-title">Frequently Asked Questions</h2>
          <div class="accordion" id="faqAccordion">
            @foreach($package->faqs as $faq)
              <div class="accordion-item border mb-2 rounded" style="border-radius:10px!important;overflow:hidden;">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" style="font-size:14px;font-weight:600;" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $loop->index }}">
                    {{ $faq->question }}
                  </button>
                </h2>
                <div id="faq{{ $loop->index }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                  <div class="accordion-body rich-text-content ck-content" style="font-size:13px;color:#555;">{!! $faq->answer !!}</div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      @if($package->seo_content)
        <hr class="section-divider">
        <div id="seo-content">
          <h2 class="sec-title">{{ $package->title }} — Everything You Need to Know</h2>
          <div class="rich-text-content ck-content" style="font-size:14px;color:#444;line-height:1.7;">{!! $package->seo_content !!}</div>
        </div>
      @endif

    </div>

    <div class="col-lg-4">
      <div class="booking-sidebar">
        @php
          // The selected room type is the base price — defaults to the first room
          // type when one exists, falling back to the package's own price otherwise.
          $defaultRoom = $package->roomTypes->first();
          $displayOriginal = $defaultRoom ? (float) $defaultRoom->price : (float) $package->price;
          $displaySell = $defaultRoom ? (float) $defaultRoom->sell_price : (float) ($package->discounted_price ?: $package->price);
          $displaySavings = $displaySell < $displayOriginal ? (int) round((($displayOriginal - $displaySell) / $displayOriginal) * 100) : null;
        @endphp
        <div class="book-price-block mb-3" id="pkg-price-block">
          @if($displaySavings)
            <div class="orig">₹{{ number_format($displayOriginal) }}</div>
            <div class="curr">₹{{ number_format($displaySell) }} <span class="per">/ person</span></div>
            <div class="saving">Save {{ $displaySavings }}%</div>
          @else
            <div class="curr">₹{{ number_format($displaySell) }} <span class="per">/ person</span></div>
          @endif
        </div>
        @if($optionalAddOns->isNotEmpty())
          <div id="pkg-addons-line" class="mb-2" style="font-size:12px;color:#005fcc;font-weight:700;display:none;"></div>
        @endif
        @if($optionalHotels->isNotEmpty())
          <div id="pkg-hotel-line" class="mb-2" style="font-size:12px;color:#005fcc;font-weight:700;display:none;"></div>
        @endif
        <form id="pkg-selection-form">
          <div class="travel-date-block">
            <div class="form-label-sm">Travel Date <span style="color:#dc2626;">*</span></div>
            <input type="date" class="form-control-sm2" id="pkg-travel-date" name="travel_date" min="{{ now()->toDateString() }}" required>
            @if($package->itineraries->isNotEmpty())
              <div class="travel-date-hint"><i class="bi bi-info-circle-fill"></i> Pick a date to see your day-by-day plan with real dates</div>
            @endif
          </div>
          @if($package->departureCities->isNotEmpty())
            <div class="mb-2">
              <div class="form-label-sm">Departure City</div>
              <select class="form-control-sm2" id="pkg-departure-city" name="departure_city">
                @foreach($package->departureCities as $city)
                  <option value="{{ $city->city_name }}">{{ $city->city_name }}</option>
                @endforeach
              </select>
            </div>
          @endif
          @if($package->roomTypes->isNotEmpty())
            <div class="mb-2">
              <div class="form-label-sm">Room Type</div>
              <select class="form-control-sm2" id="pkg-room-type" name="room_type_id">
                @foreach($package->roomTypes as $room)
                  <option value="{{ $room->id }}"
                    data-original-price="{{ number_format((float) $room->price, 2, '.', '') }}"
                    data-discounted-price="{{ number_format((float) $room->sell_price, 2, '.', '') }}">
                    {{ $room->name }} — ₹{{ number_format($room->sell_price) }}
                  </option>
                @endforeach
              </select>
            </div>
          @endif
        </form>
        {{-- Booking Type (admin-controlled, see HolidayPackage::booking_type): "book_enquiry" shows both
             actions, "enquiry_only" hides Book Now entirely — no hardcoding, purely data-driven. --}}
        @if($package->booking_type === 'book_enquiry')
          <button type="button" class="btn-book-main mt-2" id="btn-book-now">Book Now</button>
        @endif
        <button type="button" class="btn-enquire" id="btn-enquire-now">Enquire Now</button>
        <div class="trust-row">
          <span class="trust-item"><i class="bi bi-shield-check"></i> Secure Booking</span>
          <span class="trust-item"><i class="bi bi-clock-history"></i> Free Cancellation*</span>
        </div>
      </div>
    </div>
  </div>

  @if($related->isNotEmpty())
    <hr class="section-divider">
    <h2 class="sec-title">You Might Also Like</h2>
    <div class="row g-4 mb-4">
      @foreach($related as $sim)
        <div class="col-12 col-md-6 col-lg-4">
          @include('packages._card', ['package' => $sim])
        </div>
      @endforeach
    </div>
  @endif
</div>

{{-- Enquire Now modal — reuses the sidebar's own form-control-sm2/form-label-sm styling --}}
<div class="modal fade" id="enquireModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;border:none;">
      <div class="modal-header" style="border-bottom:1px solid #eee;">
        <h5 class="modal-title" style="font-weight:800;">Enquire Now</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="enquire-form">
        <div class="modal-body">
          <p class="text-muted" style="font-size:13px;">{{ $package->title }}</p>
          <div id="enquire-error" class="alert alert-danger d-none" style="font-size:13px;"></div>

          <div class="mb-2">
            <div class="form-label-sm">Full Name</div>
            <input type="text" name="name" class="form-control-sm2" required value="{{ Auth::check() ? Auth::user()->name : '' }}">
          </div>
          <div class="mb-2">
            <div class="form-label-sm">Email</div>
            <input type="email" name="email" class="form-control-sm2" required value="{{ Auth::check() ? Auth::user()->email : '' }}">
          </div>
          <div class="mb-2">
            <div class="form-label-sm">Phone</div>
            <input type="text" name="phone" class="form-control-sm2" required>
          </div>
          <div class="mb-2">
            <div class="form-label-sm">Travel Date</div>
            <input type="date" name="travel_date" class="form-control-sm2" min="{{ now()->toDateString() }}">
          </div>
          <div class="mb-2">
            <div class="form-label-sm">Travellers</div>
            <input type="number" name="travellers" min="1" max="50" class="form-control-sm2" value="1">
          </div>
          <div class="mb-2">
            <div class="form-label-sm">Message</div>
            <textarea name="message" rows="3" class="form-control-sm2"></textarea>
          </div>
        </div>
        <div class="modal-footer" style="border-top:1px solid #eee;">
          <button type="submit" class="btn-book-main" style="width:auto;padding:10px 24px;" id="enquire-submit-btn">Submit Enquiry</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  function toggleDay(header) {
    var body = header.nextElementSibling;
    var icon = header.querySelector('.bi-chevron-down, .bi-chevron-up');
    body.classList.toggle('open');
    if (icon) { icon.classList.toggle('bi-chevron-down'); icon.classList.toggle('bi-chevron-up'); }
  }
  document.querySelectorAll('.pkg-sticky-nav a').forEach(function (link) {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      var target = document.querySelector(this.getAttribute('href'));
      if (target) target.scrollIntoView({ behavior: 'smooth' });
    });
  });

  // Day plan dates — Day 1 = the picked Travel Date, Day N = Travel Date + (N-1) days.
  // Purely a display computation (server recomputes real dates from travel_date at
  // booking time); blank until a date is chosen, so each day badge just reads "Day N".
  (function () {
    var travelDateInput = document.getElementById('pkg-travel-date');
    var dayBadges = document.querySelectorAll('.itin-date-badge');
    if (!travelDateInput || !dayBadges.length) return;

    function formatDate(date) {
      return date.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', weekday: 'short' }).replace(/,/g, '');
    }

    function renderDayDates() {
      var value = travelDateInput.value;
      if (!value) {
        dayBadges.forEach(function (el) { el.textContent = ''; });
        return;
      }
      var base = new Date(value + 'T00:00:00');
      dayBadges.forEach(function (el) {
        var dayNumber = parseInt(el.dataset.day, 10) || 1;
        var d = new Date(base.getTime());
        d.setDate(d.getDate() + (dayNumber - 1));
        el.textContent = formatDate(d);
      });
    }

    travelDateInput.addEventListener('change', function () {
      renderDayDates();
      if (travelDateInput.value) travelDateInput.classList.remove('is-invalid');
    });
    renderDayDates();
  })();

  // Live price box — the SELECTED ROOM TYPE is the source of truth for both the
  // original (strike-through) and discounted (sell) price. Falls back to the
  // package's own price/discounted_price only when no room type exists, exactly
  // the same rule BookingController::calculateBreakdown() applies server-side, so
  // what's shown here always matches what gets charged.
  (function () {
    var packageMrp = {{ number_format((float) $package->price, 2, '.', '') }};
    var packageSell = {{ number_format((float) ($package->discounted_price ?: $package->price), 2, '.', '') }};
    var roomSelect = document.getElementById('pkg-room-type');
    var priceBlock = document.getElementById('pkg-price-block');

    function formatMoney(n) {
      return '₹' + Math.round(n).toLocaleString('en-IN');
    }

    function currentPrices() {
      if (roomSelect && roomSelect.options.length) {
        var opt = roomSelect.options[roomSelect.selectedIndex];
        var original = parseFloat(opt.dataset.originalPrice);
        var discounted = parseFloat(opt.dataset.discountedPrice);
        if (!isNaN(original) && !isNaN(discounted)) return { mrp: original, sell: discounted };
      }
      return { mrp: packageMrp, sell: packageSell };
    }

    function renderPriceBlock() {
      var prices = currentPrices();
      var html;

      if (prices.sell < prices.mrp) {
        var savingsPercent = Math.round(((prices.mrp - prices.sell) / prices.mrp) * 100);
        html = '<div class="orig">' + formatMoney(prices.mrp) + '</div>'
          + '<div class="curr">' + formatMoney(prices.sell) + ' <span class="per">/ person</span></div>'
          + (savingsPercent > 0 ? '<div class="saving">Save ' + savingsPercent + '%</div>' : '');
      } else {
        html = '<div class="curr">' + formatMoney(prices.sell) + ' <span class="per">/ person</span></div>';
      }

      priceBlock.innerHTML = html;
    }

    if (roomSelect) {
      roomSelect.addEventListener('change', renderPriceBlock);
    }
  })();

  // Optional add-ons preview — purely a display estimate (per person, since travellers
  // aren't collected on this page); the booking form recomputes the real total × travellers,
  // and the server recomputes it again authoritatively on submit. Never trust this for charging.
  (function () {
    var addonsLine = document.getElementById('pkg-addons-line');
    if (!addonsLine) return;

    function renderAddonsLine() {
      var checked = document.querySelectorAll('.addon-checkbox:checked');
      var card, total = 0;
      checked.forEach(function (cb) { total += parseFloat(cb.dataset.price) || 0; });

      document.querySelectorAll('.addon-card').forEach(function (c) {
        var box = c.querySelector('.addon-checkbox');
        c.classList.toggle('is-selected', box && box.checked);
      });

      if (checked.length) {
        addonsLine.style.display = 'block';
        addonsLine.textContent = '+ ' + checked.length + ' add-on' + (checked.length > 1 ? 's' : '') + ' (₹' + Math.round(total).toLocaleString('en-IN') + ' / person)';
      } else {
        addonsLine.style.display = 'none';
      }
    }

    document.addEventListener('change', function (e) {
      if (e.target.classList && e.target.classList.contains('addon-checkbox')) renderAddonsLine();
    });

    renderAddonsLine();
  })();

  // Hotel "stay option" preview — one independent single-select (radio) group PER DAY
  // (name="hotel_day_N"), since each day's stay is its own choice; clicking an already-
  // selected card deselects it so booking with zero hotels for that day stays just as
  // easy. All checked radios across every day sum into the sidebar line. Same
  // display-only-estimate caveat as the activities preview above.
  (function () {
    var hotelLine = document.getElementById('pkg-hotel-line');
    if (!hotelLine) return;
    var lastCheckedByGroup = {};

    function renderHotelLine() {
      var checked = document.querySelectorAll('.hotel-addon-radio:checked');

      document.querySelectorAll('.addon-card').forEach(function (c) {
        var radio = c.querySelector('.hotel-addon-radio');
        c.classList.toggle('is-selected', !!(radio && radio.checked));
      });

      if (checked.length) {
        var total = 0;
        var names = [];
        checked.forEach(function (radio) {
          total += (parseFloat(radio.dataset.price) || 0) * (parseInt(radio.dataset.nights, 10) || 1);
          names.push(radio.dataset.name);
        });
        hotelLine.style.display = 'block';
        hotelLine.textContent = '+ ' + names.join(', ') + ' (₹' + Math.round(total).toLocaleString('en-IN') + ')';
      } else {
        hotelLine.style.display = 'none';
      }
    }

    document.addEventListener('click', function (e) {
      if (!e.target.classList || !e.target.classList.contains('hotel-addon-radio')) return;
      var group = e.target.name;
      if (lastCheckedByGroup[group] === e.target && e.target.checked) {
        e.target.checked = false;
        lastCheckedByGroup[group] = null;
      } else {
        lastCheckedByGroup[group] = e.target.checked ? e.target : null;
      }
      renderHotelLine();
    });

    renderHotelLine();
  })();

  // Selected-only summary rows (MakeMyTrip "Change" pattern) — the real .hotel-addon-radio
  // / .addon-checkbox inputs above are the single source of truth (they live inside each
  // day's "Change"/"Add" modal); these summary rows are pure display, toggled to match
  // whichever inputs are checked. Book Now still reads the same real inputs, so the
  // submitted payload is byte-identical to before this UI was added.
  (function () {
    var slots = document.querySelectorAll('.day-summary-slot');
    if (!slots.length) return;

    function closeParentModal(el) {
      var modalEl = el.closest('.modal');
      if (!modalEl || typeof bootstrap === 'undefined') return;
      var instance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
      instance.hide();
    }

    function syncHotelSlot(day) {
      var slot = document.querySelector('.day-summary-slot[data-slot-kind="hotel"][data-slot-day="' + day + '"]');
      if (!slot) return;
      var modal = document.getElementById('hotel-modal-day-' + day);
      var checked = modal ? modal.querySelector('.hotel-addon-radio:checked') : null;
      var selectedId = checked ? checked.value : null;

      slot.querySelectorAll('.day-summary-row[data-summary-kind="hotel"]').forEach(function (row) {
        row.style.display = (selectedId && row.dataset.hotelId === selectedId) ? '' : 'none';
      });
      var empty = slot.querySelector('.day-summary-empty[data-empty-kind="hotel"]');
      if (empty) empty.style.display = selectedId ? 'none' : '';
    }

    function syncActivitySlot(day) {
      var slot = document.querySelector('.day-summary-slot[data-slot-kind="activity"][data-slot-day="' + day + '"]');
      if (!slot) return;
      var modal = document.getElementById('activity-modal-day-' + day);
      if (!modal) return;
      var checkedIds = Array.prototype.map.call(modal.querySelectorAll('.addon-checkbox:checked'), function (cb) { return cb.value; });

      slot.querySelectorAll('.day-summary-row[data-summary-kind="activity"]').forEach(function (row) {
        row.style.display = checkedIds.indexOf(row.dataset.activityId) !== -1 ? '' : 'none';
      });
    }

    // Selecting a hotel in its "Change" modal — sync the summary row and close the modal.
    document.addEventListener('click', function (e) {
      if (!e.target.classList || !e.target.classList.contains('hotel-addon-radio')) return;
      var day = e.target.name.replace('hotel_day_', '');
      syncHotelSlot(day);
      closeParentModal(e.target);
    });

    // Adding/removing an activity in its "Add"/"Change" modal — sync the row and close.
    document.addEventListener('change', function (e) {
      if (!e.target.classList || !e.target.classList.contains('addon-checkbox')) return;
      var day = e.target.dataset.day;
      if (!day) return;
      syncActivitySlot(day);
      if (e.target.checked) closeParentModal(e.target);
    });

    // "Remove" on an activity summary row — unchecks the matching input and re-syncs.
    document.addEventListener('click', function (e) {
      var removeLink = e.target.closest('.summary-remove-link');
      if (!removeLink) return;
      e.preventDefault();
      var day = removeLink.dataset.day;
      var activityId = removeLink.dataset.removeActivity;
      var input = document.querySelector('#activity-modal-day-' + day + ' .addon-checkbox[value="' + activityId + '"]');
      if (input) {
        input.checked = false;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      }
    });

    // "Change" on an activity summary row — since activities are a multi-select (not a
    // radio), swapping means unchecking this one first so the modal opens with it
    // deselected; the next one the user picks becomes the new addition.
    document.addEventListener('click', function (e) {
      var changeLink = e.target.closest('.summary-change-link[data-change-activity]');
      if (!changeLink) return;
      var day = changeLink.closest('.day-summary-row').dataset.day;
      var activityId = changeLink.dataset.changeActivity;
      var input = document.querySelector('#activity-modal-day-' + day + ' .addon-checkbox[value="' + activityId + '"]');
      if (input && input.checked) {
        input.checked = false;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      }
    });

    // Default selection — the first hotel option in each day is pre-picked via a real
    // .click() (so the existing price-line listener above also fires and the sidebar
    // total already reflects it), like MakeMyTrip pre-fills the plan; every day already
    // shows a selection and the user only opens "Change" if they want a different one.
    // Optional paid activities are never auto-added — they're opt-in add-ons everywhere
    // else on this page, so there's no "included" default to pre-select here.
    var seenHotelGroups = {};
    document.querySelectorAll('.hotel-addon-radio').forEach(function (radio) {
      if (seenHotelGroups[radio.name]) return;
      seenHotelGroups[radio.name] = true;
      if (document.querySelector('.hotel-addon-radio[name="' + radio.name + '"]:checked')) {
        syncHotelSlot(radio.name.replace('hotel_day_', ''));
      } else {
        radio.click();
      }
    });

    var seenActivityDays = {};
    document.querySelectorAll('.addon-checkbox[data-day]').forEach(function (cb) {
      if (seenActivityDays[cb.dataset.day]) return;
      seenActivityDays[cb.dataset.day] = true;
      syncActivitySlot(cb.dataset.day);
    });
  })();

  // Enquire Now — opens the modal, submits via AJAX (matches the site's existing
  // fetch()-based form pattern), redirects to the thank-you page on success.
  // Book Now — carries the sidebar's current selections through as query params so
  // the booking form can pre-fill them (auth middleware handles the login redirect).
  var bookBtn = document.getElementById('btn-book-now');
  if (bookBtn) {
    bookBtn.addEventListener('click', function () {
      var travelDate = document.getElementById('pkg-travel-date');

      // The day plan (and each day-wise hotel/activity selection) only means anything
      // once a Travel Date is picked, so Book Now is gated on it just like a required
      // form field would be.
      if (travelDate && !travelDate.value) {
        travelDate.classList.add('is-invalid');
        travelDate.reportValidity();
        travelDate.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }

      var params = new URLSearchParams();
      var departureCity = document.getElementById('pkg-departure-city');
      var roomType = document.getElementById('pkg-room-type');
      if (departureCity && departureCity.value) params.set('departure_city', departureCity.value);
      if (travelDate && travelDate.value) params.set('travel_date', travelDate.value);
      if (roomType && roomType.value) params.set('room_type_id', roomType.value);
      document.querySelectorAll('.addon-checkbox:checked').forEach(function (cb) {
        params.append('activity_ids[]', cb.value);
      });
      document.querySelectorAll('.hotel-addon-radio:checked').forEach(function (radio) {
        params.append('hotel_ids[]', radio.value);
      });
      window.location.href = '{{ route("bookings.form", $package->slug) }}?' + params.toString();
    });
  }

  var enquireBtn = document.getElementById('btn-enquire-now');
  var enquireModalEl = document.getElementById('enquireModal');
  var enquireModal = enquireModalEl ? new bootstrap.Modal(enquireModalEl) : null;
  if (enquireBtn && enquireModal) {
    enquireBtn.addEventListener('click', function () {
      var travelDate = document.getElementById('pkg-travel-date');
      var form = document.getElementById('enquire-form');
      if (travelDate && travelDate.value) form.travel_date.value = travelDate.value;
      enquireModal.show();
    });
  }

  var enquireForm = document.getElementById('enquire-form');
  if (enquireForm) {
    enquireForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var errorBox = document.getElementById('enquire-error');
      var submitBtn = document.getElementById('enquire-submit-btn');
      errorBox.classList.add('d-none');
      submitBtn.disabled = true;
      submitBtn.textContent = 'Submitting...';

      var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      var formData = new FormData(enquireForm);

      fetch('{{ route("packages.enquire.store", $package->slug) }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: formData,
      })
        .then(function (response) {
          return response.json().then(function (data) { return { ok: response.ok, data: data }; });
        })
        .then(function (result) {
          if (!result.ok) {
            var messages = result.data.errors ? Object.values(result.data.errors).flat().join(' ') : (result.data.message || 'Please check the form and try again.');
            errorBox.textContent = messages;
            errorBox.classList.remove('d-none');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Enquiry';
            return;
          }
          window.location.href = result.data.redirect;
        })
        .catch(function () {
          errorBox.textContent = 'Something went wrong. Please try again.';
          errorBox.classList.remove('d-none');
          submitBtn.disabled = false;
          submitBtn.textContent = 'Submit Enquiry';
        });
    });
  }
</script>
@endpush
