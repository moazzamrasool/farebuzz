<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title')</title>
  @hasSection('meta_description')
    <meta name="description" content="@yield('meta_description')">
  @endif
  <meta property="og:site_name" content="Farebuzzertravel">
  <meta property="og:type" content="website">
  @hasSection('og_title')
    <meta property="og:title" content="@yield('og_title')">
  @endif
  @hasSection('og_description')
    <meta property="og:description" content="@yield('og_description')">
  @endif
  @hasSection('og_image')
    <meta property="og:image" content="@yield('og_image')">
  @endif
  @hasSection('canonical')
    <link rel="canonical" href="@yield('canonical')">
    <meta property="og:url" content="@yield('canonical')">
  @endif
  <meta name="robots" content="@yield('robots', 'index, follow')">
  @stack('styles')
  {{-- GTM / Meta Pixel: managed per-site in the CRM (Tracking Scripts) and injected here,
       same as the main site layout — nothing further to wire up per landing page. --}}
  @include('layouts.partials.tracking_head')
</head>
<body>
@include('layouts.partials.tracking_body_open')
@yield('content')
@include('layouts.partials.tracking_footer')
@stack('scripts')
</body>
</html>
