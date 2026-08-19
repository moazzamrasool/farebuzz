@extends('layouts.app')

@php
  $seo = \App\Support\Seo\SeoResolver::resolve(
    $pageSeo ?? null,
    'Activities – FareBuzzer',
    'Book tours, adventure sports and curated experiences at destinations across India and abroad.',
    null,
    \App\Support\Seo\CanonicalUrl::forListing(),
  );
@endphp
@include('partials._seo_head', ['seo' => $seo])

@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/asset/css/listing-filters.css') }}">
<style>
  :root { --blue: #005fcc; }
  body { font-family: 'Inter', sans-serif; background: #f5f5f5; }
  .hp-hero {
    position: relative; height: 320px;
    background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.62)),
                url('https://images.unsplash.com/photo-1526772662000-3f88f10405ff?w=1400&q=80') center/cover no-repeat;
    display: flex; align-items: center; justify-content: center; text-align: center;
  }
  .hp-hero-text h1 { color:#fff; font-size:36px; font-weight:800; margin-bottom:8px; }
  .hp-hero-text p  { color:rgba(255,255,255,0.85); font-size:16px; }
  .results-bar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:20px; }
  .results-count { font-size:15px; font-weight:600; color:#222; }

  .activity-card-link { display:block; height:100%; }
  .activity-card { background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,0.07); overflow:hidden; height:100%; display:flex; flex-direction:column; transition:transform .25s, box-shadow .25s; }
  .activity-card:hover { transform:translateY(-4px); box-shadow:0 10px 26px rgba(0,0,0,0.13); }
  .activity-card-img { position:relative; height:190px; overflow:hidden; }
  .activity-card-img img { width:100%; height:100%; object-fit:cover; transition:transform .35s; }
  .activity-card:hover .activity-card-img img { transform:scale(1.06); }
  .activity-badge-category {
    position:absolute; top:12px; left:12px; background:rgba(255,255,255,0.92); color:#111;
    font-size:11px; font-weight:700; padding:5px 10px; border-radius:20px; display:flex; align-items:center; gap:5px;
  }
  .activity-badge-category i { color:var(--blue); font-size:11px; }
  .activity-badge-price {
    position:absolute; top:12px; right:12px; background:rgba(17,17,17,0.72); color:#fff; backdrop-filter:blur(2px);
    font-size:11px; font-weight:700; padding:5px 10px; border-radius:20px;
  }
  .activity-card-body { padding:16px; flex:1; display:flex; flex-direction:column; }
  .activity-card-title { font-size:16px; font-weight:700; color:#111; margin-bottom:4px; }
  .activity-card-dest { font-size:12px; color:#888; margin-bottom:8px; }
  .activity-card-dest i { color:var(--blue); }
  .activity-card-desc { font-size:13px; color:#666; line-height:1.5; flex:1; margin-bottom:14px; }
  .activity-card-footer { margin-top:auto; display:flex; align-items:center; justify-content:space-between; }
  .activity-price-label { font-size:11px; color:#888; }
  .activity-price-value { font-size:17px; font-weight:800; color:var(--blue); }
  .btn-view-activity { background:var(--blue); color:#fff; border:none; border-radius:8px; font-size:12px; font-weight:700; padding:8px 14px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-left:auto; }
</style>
@endpush

@section('content')

<section class="hp-hero">
  <div class="hp-hero-text">
    <h1>Activities</h1>
    <p>Hand-picked experiences and things to do at your destination</p>
  </div>
</section>

<div class="container" style="margin-top:28px;">
  @php
    $chips = [];
    if ($destId = request('destination_id')) { $chips['destination_id'] = 'Destination: '.($destinations->firstWhere('id', (int) $destId)->name ?? $destId); }
    elseif (request('destination')) { $chips['destination'] = 'Destination: '.request('destination'); }
    if (request('category')) { $chips['category'] = 'Category: '.request('category'); }
    if (request('price_min')) { $chips['price_min'] = 'Min ₹'.number_format((int) request('price_min')); }
    if (request('price_max')) { $chips['price_max'] = 'Max ₹'.number_format((int) request('price_max')); }
  @endphp
  @include('partials._filter_chips', ['chips' => $chips])

  <div class="row g-4">
    <div class="col-lg-3">
      <div class="fb-filter-sidebar mb-4">
        <form method="GET">
          <h6>Destination</h6>
          <select name="destination_id" class="form-select form-select-sm mb-2" onchange="this.form.submit()">
            <option value="">All Destinations</option>
            @foreach($destinations as $destination)
              <option value="{{ $destination->id }}" {{ (int) request('destination_id') === $destination->id ? 'selected' : '' }}>{{ $destination->name }}</option>
            @endforeach
          </select>

          <h6>Category</h6>
          <select name="category" class="form-select form-select-sm mb-2" onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
              <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
          </select>

          <h6>Price Range (₹)</h6>
          <div class="d-flex gap-2 mb-2">
            <input type="number" name="price_min" class="form-control form-control-sm" placeholder="Min" value="{{ request('price_min') }}">
            <input type="number" name="price_max" class="form-control form-control-sm" placeholder="Max" value="{{ request('price_max') }}">
          </div>

          <button type="submit" class="btn-apply-filters">Apply Filters</button>
        </form>
      </div>
    </div>

    <div class="col-lg-9">
      <div class="results-bar">
        <div class="results-count">{{ $activities->total() }} activities found</div>
        <form method="GET" class="d-flex align-items-center gap-2">
          @foreach(request()->except(['sort', 'page']) as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
          @endforeach
          <label class="mb-0" style="font-size:12px;color:#888;">Sort by</label>
          <select name="sort" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
            <option value="" {{ !request('sort') ? 'selected' : '' }}>Featured</option>
            <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
            <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name (A-Z)</option>
          </select>
        </form>
      </div>

      <div class="row g-4">
        @forelse($activities as $activity)
          <div class="col-12 col-md-6 col-lg-4">
            @include('activities._card', ['activity' => $activity])
          </div>
        @empty
          <div class="col-12 text-center text-muted py-5">No activities matched your search. Try clearing a filter.</div>
        @endforelse
      </div>

      <div class="mt-4">
        {{ $activities->links() }}
      </div>
    </div>
  </div>
</div>

@endsection
