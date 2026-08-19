@extends('layouts.app')

@php
  $seo = \App\Support\Seo\SeoResolver::resolve(
    $homepageSeo ?? null,
    'FareBuzzer – Your Journey Starts Here',
    'Flights, hotels, holiday packages, buses, cabs and more — all in one place. Plan your next trip with FareBuzzer Travel.',
    null,
    route('home'),
  );
@endphp
@include('partials._seo_head', ['seo' => $seo])
@section('content')
  @foreach($sections as $section)
    @continue($section->key === 'footer')
    @include("homepage._{$section->key}", ['section' => $section, 'searchTabs' => $searchTabs])
  @endforeach
@endsection
