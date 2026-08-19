@extends('layouts.app')

@php
  $seo = \App\Support\Seo\SeoResolver::resolve(
    $blog,
    $blog->title.' – FareBuzzer',
    $blog->excerpt ?: $blog->content,
    $blog->featured_image,
    route('blog.show', $blog->slug),
  );
@endphp
@include('partials._seo_head', ['seo' => $seo])

@push('styles')
<style>
  :root { --blue: #005fcc; --orange: #f47b20; }
  body { font-family: 'Inter', sans-serif; }

  .blog-breadcrumb { font-size:13px; color:#888; margin-bottom:18px; }
  .blog-breadcrumb a { color:var(--blue); text-decoration:none; }
  .blog-breadcrumb a:hover { text-decoration:underline; }
  .blog-breadcrumb span { margin:0 6px; }

  .blog-title { font-size:32px; font-weight:800; color:#111; margin-bottom:14px; line-height:1.3; }
  .blog-meta { display:flex; flex-wrap:wrap; gap:18px; font-size:13px; color:#777; margin-bottom:22px; }
  .blog-meta span { display:inline-flex; align-items:center; gap:6px; }

  .blog-featured-img { width:100%; max-height:440px; object-fit:cover; border-radius:12px; margin-bottom:28px; }

  .blog-share { display:flex; align-items:center; gap:10px; margin-top:32px; padding-top:20px; border-top:1px solid #eee; }
  .blog-share span { font-size:13px; font-weight:600; color:#555; }
  .blog-share a { width:34px; height:34px; border-radius:50%; background:#f0f2f5; color:#444; display:flex; align-items:center; justify-content:center; text-decoration:none; transition:background .2s, color .2s; }
  .blog-share a:hover { background:var(--blue); color:#fff; }

  .blog-card { display:block; background:#fff; border-radius:12px; overflow:hidden; text-decoration:none; box-shadow:0 2px 8px rgba(0,0,0,.06); transition:box-shadow .2s, transform .2s; height:100%; }
  .blog-card:hover { box-shadow:0 6px 20px rgba(0,0,0,.1); transform:translateY(-2px); }
  .blog-card-img { height:160px; width:100%; object-fit:cover; display:block; }
  .blog-card-body { padding:14px; }
  .blog-card-category { display:inline-block; background:#dbeafe; color:var(--blue); font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; text-transform:uppercase; letter-spacing:.03em; margin-bottom:8px; }
  .blog-card-title { font-size:15px; font-weight:700; color:#111; margin-bottom:8px; line-height:1.4; }
  .blog-card-meta { font-size:12px; color:#888; display:flex; gap:14px; }

  .related-title { font-size:22px; font-weight:800; color:#111; margin-bottom:20px; }
</style>
@endpush

@section('content')
<section class="section-pad" style="padding-top:32px;">
  <div class="container" style="max-width:860px;">
    <div class="blog-breadcrumb">
      <a href="{{ route('blog.index') }}">Blog</a>
      <span>&raquo;</span>
      <a href="{{ route('blog.index', ['category' => $blog->category]) }}">{{ $blog->category_label }}</a>
      <span>&raquo;</span>
      {{ $blog->title }}
    </div>

    <h1 class="blog-title">{{ $blog->title }}</h1>
    <div class="blog-meta">
      @if($blog->author_name)
        <span><i class="bi bi-person"></i> {{ $blog->author_name }}</span>
      @endif
      <span><i class="bi bi-calendar3"></i> {{ $blog->published_at?->format('d M Y') }}</span>
      <span><i class="bi bi-clock"></i> {{ $blog->read_time }}</span>
    </div>

    @if($blog->featured_image)
      <img class="blog-featured-img" src="{{ asset('storage/'.$blog->featured_image) }}" alt="{{ $blog->title }}">
    @endif

    <div class="ck-content">
      {!! $blog->content !!}
    </div>

    <div class="blog-share">
      <span>Share:</span>
      <a href="https://wa.me/?text={{ urlencode($blog->title.' - '.url()->current()) }}" target="_blank" rel="noopener" aria-label="Share on WhatsApp"><i class="bi bi-whatsapp"></i></a>
      <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" aria-label="Share on Facebook"><i class="bi bi-facebook"></i></a>
      <a href="https://twitter.com/intent/tweet?text={{ urlencode($blog->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" aria-label="Share on X"><i class="bi bi-twitter-x"></i></a>
      <a href="mailto:?subject={{ urlencode($blog->title) }}&body={{ urlencode(url()->current()) }}" aria-label="Share via Email"><i class="bi bi-envelope"></i></a>
    </div>
  </div>
</section>

@if($related->isNotEmpty())
<section class="container" style="max-width:1140px; padding-bottom:56px;">
  <h2 class="related-title">Related Posts</h2>
  <div class="row g-4">
    @foreach($related as $relatedBlog)
      <div class="col-12 col-md-4">
        @include('blog._card', ['blog' => $relatedBlog])
      </div>
    @endforeach
  </div>
</section>
@endif

@endsection
