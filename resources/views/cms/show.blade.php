@extends('layouts.app')

@php
  $seo = \App\Support\Seo\SeoResolver::resolve(
    $page,
    $page->title.' – FareBuzzer',
    $page->body,
    null,
    url($page->slug),
  );
@endphp
@include('partials._seo_head', ['seo' => $seo])

@section('content')
<section class="section-pad">
  <div class="container">
    <h1 class="section-title mb-4 cms-page-title" style="font-size:28px;">{{ $page->title }}</h1>
    <div class="cms-page-body ck-content">
      {!! $page->body !!}
    </div>
  </div>
</section>
@endsection
