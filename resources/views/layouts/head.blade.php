<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'FareBuzzer – Your Journey Starts Here')</title>
  <meta property="og:site_name" content="FareBuzzer">
  @hasSection('meta_description')
    <meta name="description" content="@yield('meta_description')">
  @endif
  @hasSection('meta_keywords')
    <meta name="keywords" content="@yield('meta_keywords')">
  @endif
  @hasSection('og_title')
    <meta property="og:title" content="@yield('og_title')">
  @endif
  @hasSection('og_description')
    <meta property="og:description" content="@yield('og_description')">
  @endif
  @hasSection('og_image')
    <meta property="og:image" content="@yield('og_image')">
  @endif
  @hasSection('og_url')
    <meta property="og:url" content="@yield('og_url')">
  @endif
  @hasSection('canonical')
    <link rel="canonical" href="@yield('canonical')">
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
