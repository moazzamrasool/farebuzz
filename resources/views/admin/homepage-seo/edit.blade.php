@extends('layouts.admin.app')

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
                <h3 class="card-title">Homepage SEO</h3>
                <div class="card-tools">
                  <a href="{{ url('/') }}" target="_blank" class="btn btn-secondary btn-sm">Preview</a>
                </div>
              </div>
              <div class="card-body">
                <p class="text-muted">Homepage content (sections, banners, offers) is managed under Homepage Sections. This screen only covers what search engines and social shares see for the homepage.</p>

                <form action="{{ route('crm.homepage-seo.update') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  @method('PUT')

                  @include('admin.partials._seo_fields', [
                    'seo' => $homepageSeo,
                    'seoUrl' => route('home'),
                    'seoPreviewFallback' => 'FareBuzzer – Your Journey Starts Here',
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
