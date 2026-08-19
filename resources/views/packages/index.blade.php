@extends('layouts.app')

@php
  $seo = \App\Support\Seo\SeoResolver::resolve(
    $pageSeo ?? null,
    $heading.' – FareBuzzer',
    $subheading,
    null,
    \App\Support\Seo\CanonicalUrl::forListing(),
  );
@endphp
@include('partials._seo_head', ['seo' => $seo])

@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/asset/css/listing-filters.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/asset/css/package-card.css') }}">
<style>
  .fb-breadcrumb { font-size:13px; color:#888; }
  .fb-breadcrumb a { color:var(--blue); text-decoration:none; }
  .fb-breadcrumb a:hover { text-decoration:underline; }
  .fb-breadcrumb span { margin:0 6px; }

  .dest-header { display:flex; gap:20px; align-items:center; background:#fff; border-radius:12px; padding:18px; margin-bottom:20px; }
  .dest-header-banner { width:220px; height:140px; border-radius:10px; overflow:hidden; flex-shrink:0; }
  .dest-header-banner img { width:100%; height:100%; object-fit:cover; display:block; }
  .dest-header-title { font-size:26px; font-weight:800; color:#111; margin-bottom:6px; }
  .dest-header-desc { font-size:14px; color:#555; }
  .dest-header-desc :first-child { margin-top:0; }
  .dest-header-desc :last-child { margin-bottom:0; }
  @media(max-width:576px) {
    .dest-header { flex-direction:column; align-items:flex-start; }
    .dest-header-banner { width:100%; height:160px; }
  }
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

  .dest-tabs { display:flex; gap:8px; flex-wrap:wrap; padding:4px 0; }
  .dest-tabs .tab-btn {
    border:1.5px solid #ddd; background:#fff; border-radius:20px; padding:7px 18px;
    font-size:13px; font-weight:500; color:#444; cursor:pointer; transition:all .2s;
    white-space:nowrap; text-decoration:none; display:inline-block;
  }
  .dest-tabs .tab-btn:hover { border-color:var(--blue); color:var(--blue); }
  .dest-tabs .tab-btn.active { background:var(--blue); border-color:var(--blue); color:#fff; font-weight:600; }

  .results-bar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:20px; }
  .results-count { font-size:15px; font-weight:600; color:#222; }
  .results-count span { color:#888; font-weight:400; font-size:13px; }

  .why-section { background:#fff; padding:60px 0; }
  .why-card { text-align:center; padding:32px 24px; border-radius:12px; border:1.5px solid #eee; transition:border-color .2s, box-shadow .2s; }
  .why-card:hover { border-color:var(--blue); box-shadow:0 4px 16px rgba(0,95,204,0.1); }
  .why-icon { width:64px; height:64px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:28px; }
  .why-card h5 { font-size:15px; font-weight:700; margin-bottom:8px; }
  .why-card p  { font-size:13px; color:#666; margin:0; }

  .section-title { font-size:24px; font-weight:800; color:#111; }

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

<div class="container" style="margin-top:28px;">
  @if($scopedDestination ?? null)
    <div class="fb-breadcrumb mb-3">
      <a href="{{ route($local === 'domestic' ? 'packages.india' : 'packages.international') }}">{{ $local === 'domestic' ? 'India Packages' : 'International Packages' }}</a>
      <span>&raquo;</span> {{ $scopedDestination->name }}
    </div>

    <div class="dest-header">
      @if($scopedDestination->cover_image)
        <div class="dest-header-banner">
          <img src="{{ \App\Support\MediaUrl::resolve($scopedDestination->cover_image) }}" alt="{{ $scopedDestination->name }}">
        </div>
      @endif
      <div>
        <h1 class="dest-header-title">{{ $scopedDestination->name }}</h1>
        @if($scopedDestination->description)
          <div class="dest-header-desc ck-content">{!! $scopedDestination->description !!}</div>
        @endif
      </div>
    </div>
  @endif

  <div class="dest-tabs mb-4">
    <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="tab-btn {{ !request('category') ? 'active' : '' }}">All</a>
    @foreach($categories as $category)
      <a href="{{ request()->fullUrlWithQuery(['category' => $category->slug]) }}" class="tab-btn {{ request('category') === $category->slug ? 'active' : '' }}">{{ $category->name }}</a>
    @endforeach
  </div>

  @php
    $chips = [];
    if ($destId = request('destination_id')) { $chips['destination_id'] = 'Destination: '.($destinations->firstWhere('id', (int) $destId)->name ?? $destId); }
    elseif ($destTerm = request()->query('destination')) { $chips['destination'] = 'Destination: '.$destTerm; }
    if (request('duration')) { $chips['duration'] = 'Duration: '.request('duration').' nights'; }
    if (request('price_min')) { $chips['price_min'] = 'Min ₹'.number_format((int) request('price_min')); }
    if (request('price_max')) { $chips['price_max'] = 'Max ₹'.number_format((int) request('price_max')); }
    if (request('hotel_category')) { $chips['hotel_category'] = request('hotel_category'); }
    if (request('budget')) { $chips['budget'] = 'Budget: '.str_replace('_', ' ', request('budget')); }
  @endphp
  @include('partials._filter_chips', ['chips' => $chips])

  <div class="row g-4">
    <div class="col-lg-3">
      <div class="fb-filter-sidebar mb-4">
        <form method="GET">
          @foreach(request()->except(['destination_id', 'destination', 'duration', 'price_min', 'price_max', 'hotel_category', 'budget', 'page']) as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
          @endforeach

          @unless($scopedDestination ?? null)
            <h6>Destination</h6>
            <select name="destination_id" class="form-select form-select-sm mb-2" onchange="this.form.submit()">
              <option value="">All Destinations</option>
              @foreach($destinations as $destination)
                <option value="{{ $destination->id }}" {{ (int) request('destination_id') === $destination->id ? 'selected' : '' }}>{{ $destination->name }}</option>
              @endforeach
            </select>
          @endunless

          <h6>Duration</h6>
          @foreach(['1-3' => '1-3 nights', '4-6' => '4-6 nights', '7+' => '7+ nights'] as $val => $label)
            <div class="form-check">
              <input class="form-check-input" type="radio" name="duration" value="{{ $val }}" id="dur-{{ $val }}" {{ request('duration') === $val ? 'checked' : '' }} onchange="this.form.submit()">
              <label class="form-check-label" for="dur-{{ $val }}">{{ $label }}</label>
            </div>
          @endforeach

          <h6>Price Range (₹)</h6>
          <div class="d-flex gap-2 mb-2">
            <input type="number" name="price_min" class="form-control form-control-sm" placeholder="Min" value="{{ request('price_min') }}">
            <input type="number" name="price_max" class="form-control form-control-sm" placeholder="Max" value="{{ request('price_max') }}">
          </div>

          @if($hotelCategories->isNotEmpty())
            <h6>Star Category</h6>
            <select name="hotel_category" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">Any</option>
              @foreach($hotelCategories as $hc)
                <option value="{{ $hc }}" {{ request('hotel_category') === $hc ? 'selected' : '' }}>{{ $hc }}</option>
              @endforeach
            </select>
          @endif

          <button type="submit" class="btn-apply-filters">Apply Filters</button>
        </form>
      </div>
    </div>

    <div class="col-lg-9">
      <div class="results-bar">
        <div class="results-count">{{ $packages->total() }} packages found <span>&middot; showing {{ $packages->firstItem() ?? 0 }}-{{ $packages->lastItem() ?? 0 }}</span></div>
        <form method="GET" class="d-flex align-items-center gap-2">
          @foreach(request()->except(['sort', 'page']) as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
          @endforeach
          <label class="form-label-sm mb-0" style="font-size:12px;color:#888;">Sort by</label>
          <select name="sort" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
            <option value="" {{ !request('sort') ? 'selected' : '' }}>Popularity</option>
            <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
            <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
            <option value="duration" {{ request('sort') === 'duration' ? 'selected' : '' }}>Duration</option>
          </select>
        </form>
      </div>

      <div class="row g-4" id="pkgGrid">
        @forelse($packages as $package)
          <div class="col-12 col-md-6">
            @include('packages._card', ['package' => $package])
          </div>
        @empty
          <div class="col-12 text-center text-muted py-5">No packages matched your search — showing tips: try clearing a filter or exploring all packages.</div>
        @endforelse
      </div>

      <div class="mt-4">
        {{ $packages->links() }}
      </div>
    </div>
  </div>
</div>

<section class="why-section mt-4">
  <div class="container">
    <h2 class="section-title text-center mb-4">Why Book With FareBuzzer</h2>
    <div class="row g-4">
      <div class="col-6 col-md-3">
        <div class="why-card">
          <div class="why-icon" style="background:#dbeafe;color:var(--blue);"><i class="bi bi-shield-check"></i></div>
          <h5>Best Price Guarantee</h5>
          <p>Handpicked packages at the most competitive prices.</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="why-card">
          <div class="why-icon" style="background:#fef3c7;color:#c97500;"><i class="bi bi-headset"></i></div>
          <h5>24/7 Support</h5>
          <p>Our travel experts are always here to help.</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="why-card">
          <div class="why-icon" style="background:#dcfce7;color:#16a34a;"><i class="bi bi-award"></i></div>
          <h5>Curated Experiences</h5>
          <p>Every itinerary is designed by destination experts.</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="why-card">
          <div class="why-icon" style="background:#f3e8ff;color:#7c3aed;"><i class="bi bi-people"></i></div>
          <h5>Trusted by Travellers</h5>
          <p>Thousands of happy travellers across India and abroad.</p>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
