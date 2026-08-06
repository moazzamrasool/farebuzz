@extends('layouts.app')

@section('title', $page->meta_title ?: $page->title.' – FareBuzzer')
@section('meta_description', $page->meta_description)

@section('content')
<section class="section-pad">
  <div class="container" style="max-width:860px;">
    <h1 class="section-title mb-4" style="font-size:28px;">{{ $page->title }}</h1>
    <div class="cms-page-body ck-content">
      {!! $page->body !!}
    </div>
  </div>
</section>
@endsection
