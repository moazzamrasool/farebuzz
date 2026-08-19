@extends('layouts.app')

@php
  $seo = \App\Support\Seo\SeoResolver::resolve(
    null,
    $productLabel.' – Coming Soon | FareBuzzer',
    "FareBuzzer {$productLabel} booking is launching soon. In the meantime, explore our live holiday packages, hotels and activities.",
    null,
    url()->current(),
  );
  $seo['robots'] = 'noindex, nofollow';
@endphp
@include('partials._seo_head', ['seo' => $seo])

@section('content')
<div class="pad100 bg-white text-center">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-7">
        <span class="sub-title">Coming Soon</span>
        <h5 class="heading mb-3">{{ $productLabel }} <span class="txt-color">On FareBuzzer</span></h5>
        <p class="mb-4">We're putting the finishing touches on {{ $productLabel }} booking. It'll be live soon — until then, check out our holiday packages, hotels and activities.</p>
        <a href="{{ route('home') }}" class="btn theme-btn">Back To Home</a>
      </div>
    </div>
  </div>
</div>
@endsection
