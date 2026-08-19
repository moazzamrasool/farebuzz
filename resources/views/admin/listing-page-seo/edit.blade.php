@extends('layouts.admin.app')

@php
  $pageRoutes = [
    'india-packages'         => route('packages.india'),
    'international-packages' => route('packages.international'),
    'hotels'                 => route('hotels.index'),
    'activities'              => route('activities.index'),
  ];
  $seoUrl = $pageRoutes[$page];
@endphp

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">

            @if(session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">{{ $pageLabel }} SEO</h3>
                <div class="card-tools">
                  <a href="{{ route('crm.listing-page-seo.index') }}" class="btn btn-secondary btn-sm">Back to Listing Pages</a>
                  <a href="{{ $seoUrl }}" target="_blank" class="btn btn-secondary btn-sm">Preview</a>
                </div>
              </div>
              <div class="card-body">
                <form action="{{ route('crm.listing-page-seo.update', $page) }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  @method('PUT')

                  @include('admin.partials._seo_fields', [
                    'seo' => $listingPageSeo,
                    'seoUrl' => $seoUrl,
                    'seoPreviewFallback' => $pageLabel.' – FareBuzzer',
                  ])

                  <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
              </div>
            </div>

          </div>
            </div>
        </div>
    </div>
</div>
@endsection
