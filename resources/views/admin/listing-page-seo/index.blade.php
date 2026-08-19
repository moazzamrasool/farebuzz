@extends('layouts.admin.app')

@php
  $pageRoutes = [
    'india-packages'         => route('packages.india'),
    'international-packages' => route('packages.international'),
    'hotels'                 => route('hotels.index'),
    'activities'              => route('activities.index'),
  ];
@endphp

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Listing Pages SEO</h3>
              </div>
              <div class="card-body">
                <p class="text-muted">Meta title/description and other &lt;head&gt; tags for the general listing pages — per-destination and per-item SEO is edited on those records directly.</p>

                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Page</th>
                      <th>URL</th>
                      <th class="text-right">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($pages as $key => $label)
                      <tr>
                        <td>{{ $label }}</td>
                        <td><a href="{{ $pageRoutes[$key] }}" target="_blank">{{ $pageRoutes[$key] }}</a></td>
                        <td class="text-right">
                          <a href="{{ route('crm.listing-page-seo.edit', $key) }}" class="btn btn-sm btn-primary">Edit SEO</a>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>

          </div>
            </div>
        </div>
    </div>
</div>
@endsection
