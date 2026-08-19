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
                <h3 class="card-title">Contact Us Page</h3>
                <div class="card-tools">
                  <a href="{{ route('contact_us') }}" target="_blank" class="btn btn-secondary btn-sm">Preview</a>
                </div>
              </div>
              <div class="card-body">
                <form action="{{ route('crm.contact-page.update') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  @method('PUT')

                  <h5 class="mb-3">Contact Details</h5>
                  <div class="form-group">
                    <label for="address">Address</label>
                    <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror"
                      placeholder="e.g. 123, Business Park, New Delhi, India">{{ old('address', $contactPage->address) }}</textarea>
                    @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="phone">Phone</label>
                      <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
                        value="{{ old('phone', $contactPage->phone) }}" placeholder="e.g. +91 98765 43210">
                      @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group col-md-6">
                      <label for="email">Email</label>
                      <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $contactPage->email) }}" placeholder="e.g. info@farebuzzertravel.com">
                      @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="support_hours">Support Hours</label>
                    <input type="text" name="support_hours" id="support_hours" class="form-control @error('support_hours') is-invalid @enderror"
                      value="{{ old('support_hours', $contactPage->support_hours) }}" placeholder="e.g. Mon–Sat, 10:00 AM – 7:00 PM IST">
                    @error('support_hours') <span class="text-danger">{{ $message }}</span> @enderror
                  </div>
                  <div class="form-group">
                    <label for="map_embed_url">Map Embed URL <small class="text-muted">(optional — from Google Maps' Share &gt; Embed a map; leave blank to auto-generate from the Address above)</small></label>
                    <input type="url" name="map_embed_url" id="map_embed_url" class="form-control @error('map_embed_url') is-invalid @enderror"
                      value="{{ old('map_embed_url', $contactPage->map_embed_url) }}" placeholder="https://www.google.com/maps/embed?pb=...">
                    @error('map_embed_url') <span class="text-danger">{{ $message }}</span> @enderror
                    @if($contactPage->resolvedMapEmbedUrl())
                      <small class="form-text text-muted">Map will show on the Contact Us page {{ $contactPage->map_embed_url ? '(using this URL).' : '(auto-generated from the address).' }}</small>
                    @else
                      <small class="form-text text-muted">No map will show until either this field or the Address above is filled in.</small>
                    @endif
                  </div>

                  @include('admin.partials._seo_fields', [
                    'seo' => $contactPage,
                    'seoUrl' => route('contact_us'),
                    'seoPreviewFallback' => 'Contact Us – FareBuzzer',
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
