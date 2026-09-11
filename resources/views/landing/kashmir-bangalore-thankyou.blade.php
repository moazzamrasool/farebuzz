@extends('layouts.landing')

@php
  $seo = \App\Support\Seo\SeoResolver::resolve(
    null,
    'Thank You | Kashmir Tour Package from Bangalore',
    'Your Kashmir tour enquiry has been received. A travel expert will call you during business hours.',
    null,
    route('landing.kashmir-bangalore.thankyou'),
  );
$seo['robots'] = 'noindex, nofollow';
@endphp
@include('partials._seo_head', ['seo' => $seo])

@push('styles')
  <link rel="stylesheet" href="{{ asset('frontend/asset/css/landing-kashmir-bangalore.css') }}">
  <style>
    /* Page-scoped only (selectors all require .ty-page / .ty-hero / .ty-card, which
       don't exist on the landing page) — the shared stylesheet is untouched.

       .hero:before/:after are position:absolute; per CSS stacking rules, positioned
       descendants always paint AFTER non-positioned in-flow content in the same
       stacking context, regardless of z-index:auto vs none. The landing page avoids
       this by wrapping its hero content in .hero-grid (position:relative;z-index:2);
       .ty-hero below does the same job for this page's single centered card instead
       of the two-column grid layout .hero-grid is built for. */
    .ty-page{min-height:100vh;display:flex;flex-direction:column}
    .ty-page .hero{flex:1;display:flex}
    .ty-hero{position:relative;z-index:2;flex:1;display:flex;align-items:center;justify-content:center;padding:60px 20px}
    .ty-card{max-width:460px;width:100%}
    /* .success is display:none by default, toggled visible by JS on the landing
       page's inline flow — this page has no such JS, so force it on here instead
       of touching that shared rule (the landing page may still rely on it). */
    .ty-card .success{display:flex}
  </style>
@endpush

@section('content')
<div class="ty-page">
  <header class="head">
    <a class="brand" href="{{ route('landing.kashmir-bangalore') }}"><img src="{{ asset('frontend/img/logo.png') }}" height="60px"/></a>
  </header>

  <section class="hero">
    <div class="ty-hero shell">
      <aside class="card ty-card">
        <div class="success">
          <div class="tick">✓</div>
          <span class="kicker">Request received</span>
          <h2>@if($name)Thank you, {{ $name }}!@else Thank you!@endif</h2>
          <p class="section-copy">A travel expert will call you during business hours to help plan your Kashmir trip.</p>
          <div class="actions" style="justify-content:center;margin-top:22px;">
            <a class="btn gold" href="tel:+918447843676">☎ Call Now</a>
            <a class="btn wa" href="https://wa.me/918447843676" target="_blank">WhatsApp</a>
          </div>
          <p style="margin-top:20px;"><a href="{{ route('landing.kashmir-bangalore') }}" style="color:var(--green);font-weight:800;font-size:11px;">← Back to package details</a></p>
        </div>
      </aside>
    </div>
  </section>

  <!-- GOOGLE ADS CONVERSION TAG -->
  {{-- Paste the gtag('event', 'conversion', {...}) snippet here — this page loads only
       once per genuine submission (see LandingController::kashmirBangaloreThankYou),
       so a conversion fired unconditionally on page load here is safe from refresh/replay. --}}

  <!-- META PIXEL Lead EVENT -->
  {{-- Paste fbq('track', 'Lead') here. --}}

  <footer class="foot">
    <div class="foot-grid shell">
      <a class="brand" href="{{ route('landing.kashmir-bangalore') }}"><img src="{{ asset('frontend/img/logo.png') }}" height="60px"/></a>
      <p>© 2026 FareBuzzer Travel Pvt Ltd. All rights reserved.</p>
    </div>
  </footer>
</div>
@endsection
