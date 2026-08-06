@extends('layouts.app')

@section('title', 'Thank You – FareBuzzer')

@push('styles')
<style>
  :root { --blue:#005fcc; --orange:#f47b20; }
  .ty-wrap { max-width:560px; margin:60px auto; padding:0 16px; text-align:center; }
  .ty-icon { width:80px; height:80px; border-radius:50%; background:#dcfce7; color:#16a34a; display:flex; align-items:center; justify-content:center; font-size:36px; margin:0 auto 20px; }
  .ty-title { font-size:26px; font-weight:800; margin-bottom:10px; }
  .ty-sub { font-size:15px; color:#666; line-height:1.7; margin-bottom:28px; }
  .ty-actions .btn { border-radius:10px; font-weight:700; padding:10px 22px; }
</style>
@endpush

@section('content')
<div class="ty-wrap">
  <div class="ty-icon"><i class="bi bi-check-lg"></i></div>
  <div class="ty-title">Thank You!</div>
  <p class="ty-sub">
    Your enquiry for <strong>{{ $package->title }}</strong> has been received.
    Our travel experts will reach out to you shortly with the best options.
  </p>
  <div class="ty-actions">
    <a href="{{ route('packages.show', $package->slug) }}" class="btn btn-outline-primary">Back to Package</a>
    <a href="{{ route('home') }}" class="btn" style="background:var(--blue);color:#fff;">Continue Exploring</a>
  </div>
</div>
@endsection
