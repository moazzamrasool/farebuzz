@extends('layouts.app')
@section('content')
  @foreach($sections as $section)
    @continue($section->key === 'footer')
    @include("homepage._{$section->key}", ['section' => $section, 'searchTabs' => $searchTabs])
  @endforeach
@endsection
