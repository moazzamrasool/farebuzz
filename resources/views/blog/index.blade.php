@extends('layouts.app')

@php
  $seo = \App\Support\Seo\SeoResolver::resolve(
    null,
    'Travel Blog – Tips, Guides & Destination Inspiration | FareBuzzer',
    'Travel tips, destination guides, offers and inspiration from the FareBuzzer travel blog.',
    null,
    \App\Support\Seo\CanonicalUrl::forListing(),
  );
@endphp
@include('partials._seo_head', ['seo' => $seo])

@push('styles')
<style>
  :root { --blue: #005fcc; --orange: #f47b20; --bg: #f5f5f5; }
  body { font-family: 'Inter', sans-serif; background: var(--bg); }

  .hp-hero {
    position: relative; height: 320px;
    background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.62)),
                url('https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=1400&q=80') center/cover no-repeat;
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

  .blog-card { display:block; background:#fff; border-radius:12px; overflow:hidden; text-decoration:none; box-shadow:0 2px 8px rgba(0,0,0,.06); transition:box-shadow .2s, transform .2s; height:100%; }
  .blog-card:hover { box-shadow:0 6px 20px rgba(0,0,0,.1); transform:translateY(-2px); }
  .blog-card-img { height:180px; width:100%; object-fit:cover; display:block; }
  .blog-card-body { padding:16px; }
  .blog-card-category { display:inline-block; background:#dbeafe; color:var(--blue); font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; text-transform:uppercase; letter-spacing:.03em; margin-bottom:8px; }
  .blog-card-title { font-size:16px; font-weight:700; color:#111; margin-bottom:8px; line-height:1.4; }
  .blog-card-meta { font-size:12px; color:#888; display:flex; gap:14px; }

  @media(max-width:768px) {
    .hp-hero { height:220px; }
    .hp-hero-text h1 { font-size:26px; }
  }
</style>
@endpush

@section('content')

<section class="hp-hero">
  <div class="hp-hero-text">
    <h1>Travel blog</h1>
    <p>Tips, guides and inspiration for your next journey</p>
  </div>
</section>

<div class="container" style="margin-top:28px; margin-bottom:48px;">
  <div class="dest-tabs mb-4">
    <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="tab-btn {{ !request('category') ? 'active' : '' }}">All</a>
    @foreach(\App\Models\Blog::CATEGORIES as $value => $label)
      <a href="{{ request()->fullUrlWithQuery(['category' => $value]) }}" class="tab-btn {{ request('category') === $value ? 'active' : '' }}">{{ $label }}</a>
    @endforeach
  </div>

  <div class="row g-4">
    @forelse($blogs as $blog)
      <div class="col-12 col-md-6 col-lg-4">
        @include('blog._card', ['blog' => $blog])
      </div>
    @empty
      <div class="col-12 text-center text-muted py-5">No blog posts published yet — check back soon.</div>
    @endforelse
  </div>

  <div class="mt-4">
    {{ $blogs->links() }}
  </div>
</div>

@endsection
