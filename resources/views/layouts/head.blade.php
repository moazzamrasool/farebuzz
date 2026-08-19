<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @php
    // Blade sections are stored HTML-escaped, so pulling one out as a fallback for
    // another tag needs an entity-decode first — otherwise the second {{ }} pass
    // (or @yield's own default-escaping) double-encodes it (e.g. "&amp;" -> "&amp;amp;").
    $__siteTitle = 'FareBuzzer – Your Journey Starts Here';
    $__siteDescription = 'Explore affordable holiday packages, hotel bookings and flight deals for your next trip. Book domestic and international tour package with FareBuzzerTravel.';
    $__siteImage = asset('frontend/img/logo.png');

    $__rawTitle = $__env->hasSection('title')
        ? html_entity_decode($__env->yieldContent('title'), ENT_QUOTES)
        : $__siteTitle;

    $__rawOgTitle = $__env->hasSection('og_title')
        ? html_entity_decode($__env->yieldContent('og_title'), ENT_QUOTES)
        : $__rawTitle;

    $__rawOgDescription = $__env->hasSection('og_description')
        ? html_entity_decode($__env->yieldContent('og_description'), ENT_QUOTES)
        : $__siteDescription;

    $__rawOgImage = $__env->hasSection('og_image')
        ? html_entity_decode($__env->yieldContent('og_image'), ENT_QUOTES)
        : $__siteImage;

    $__rawCanonical = $__env->hasSection('canonical')
        ? html_entity_decode($__env->yieldContent('canonical'), ENT_QUOTES)
        : \App\Support\Seo\CanonicalUrl::current();

    $__rawOgUrl = $__env->hasSection('og_url')
        ? html_entity_decode($__env->yieldContent('og_url'), ENT_QUOTES)
        : $__rawCanonical;
  @endphp
  <title>{{ $__rawTitle }}</title>
  <meta property="og:site_name" content="Farebuzzertravel">
  <meta property="og:type" content="website">
  @hasSection('meta_description')
    <meta name="description" content="@yield('meta_description')">
  @endif
  @hasSection('meta_keywords')
    <meta name="keywords" content="@yield('meta_keywords')">
  @endif
  <meta property="og:title" content="{{ $__rawOgTitle }}">
  <meta property="og:description" content="{{ $__rawOgDescription }}">
  <meta property="og:image" content="{{ $__rawOgImage }}">
  <meta property="og:url" content="{{ $__rawOgUrl }}">
  <link rel="canonical" href="{{ $__rawCanonical }}">

  <meta name="robots" content="@yield('robots', 'index, follow')">
  <meta name="twitter:card" content="summary_large_image">
  @hasSection('twitter_title')
    <meta name="twitter:title" content="@yield('twitter_title')">
  @endif
  @hasSection('twitter_description')
    <meta name="twitter:description" content="@yield('twitter_description')">
  @endif
  @hasSection('twitter_image')
    <meta name="twitter:image" content="@yield('twitter_image')">
  @endif
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="{{asset('frontend/asset/css/style.css')}}"/>
  {{-- CKEditor 5's official content stylesheet — renders saved rich-text HTML (image
       float/wrap, tables, lists) identically to how it looked in the admin editor. --}}
  <link rel="stylesheet" href="{{asset('frontend/asset/css/ckeditor5-content.css')}}"/>
  @stack('styles')
  @stack('jsonld')
  @include('layouts.partials.tracking_head')
</head>
