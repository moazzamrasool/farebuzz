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

            <div class="alert alert-warning">
              <strong><i class="fa fa-exclamation-triangle"></i> Heads up:</strong>
              Anything pasted below is output <strong>exactly as typed, unescaped</strong>, and runs on every
              frontend page (homepage, listings, detail pages, CMS pages). It is never shown or run inside
              <code>/crm</code>. Only paste code you trust — a broken or malicious script here breaks the
              entire public site.
            </div>

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Tracking &amp; Scripts</h3>
              </div>
              <div class="card-body">
                @if($tracking->updated_at)
                  <p class="text-muted">
                    Last updated {{ $tracking->updated_at->diffForHumans() }}
                    @if($tracking->updatedBy) by {{ $tracking->updatedBy->name }} @endif
                  </p>
                @endif

                <form action="{{ route('crm.tracking-scripts.update') }}" method="POST">
                  @csrf
                  @method('PUT')

                  <h5>Header Scripts</h5>
                  <p class="text-muted">Injected just before <code>&lt;/head&gt;</code> — Google Analytics / GA4, GTM head snippet, Facebook Pixel, Search Console verification meta tag, etc.</p>
                  <div class="form-group form-check">
                    <input type="checkbox" name="header_enabled" id="header_enabled" class="form-check-input" value="1"
                      {{ old('header_enabled', $tracking->header_enabled) ? 'checked' : '' }}>
                    <label class="form-check-label" for="header_enabled">Enabled</label>
                  </div>
                  <div class="form-group">
                    <textarea name="header_script" rows="8" class="form-control @error('header_script') is-invalid @enderror"
                      style="font-family:monospace;font-size:13px;"
                      placeholder="<!-- Google tag (gtag.js) -->&#10;<script>...</script>">{{ old('header_script', $tracking->header_script) }}</textarea>
                    @error('header_script') <span class="text-danger">{{ $message }}</span> @enderror
                  </div>

                  <hr>

                  <h5>Body-open Scripts</h5>
                  <p class="text-muted">Injected right after <code>&lt;body&gt;</code> — e.g. the GTM <code>&lt;noscript&gt;</code> snippet.</p>
                  <div class="form-group form-check">
                    <input type="checkbox" name="body_enabled" id="body_enabled" class="form-check-input" value="1"
                      {{ old('body_enabled', $tracking->body_enabled) ? 'checked' : '' }}>
                    <label class="form-check-label" for="body_enabled">Enabled</label>
                  </div>
                  <div class="form-group">
                    <textarea name="body_script" rows="6" class="form-control @error('body_script') is-invalid @enderror"
                      style="font-family:monospace;font-size:13px;"
                      placeholder="<noscript><iframe src=&quot;https://www.googletagmanager.com/ns.html?id=GTM-XXXX&quot;></iframe></noscript>">{{ old('body_script', $tracking->body_script) }}</textarea>
                    @error('body_script') <span class="text-danger">{{ $message }}</span> @enderror
                  </div>

                  <hr>

                  <h5>Footer Scripts</h5>
                  <p class="text-muted">Injected just before <code>&lt;/body&gt;</code> — chat widgets, remarketing tags, etc.</p>
                  <div class="form-group form-check">
                    <input type="checkbox" name="footer_enabled" id="footer_enabled" class="form-check-input" value="1"
                      {{ old('footer_enabled', $tracking->footer_enabled) ? 'checked' : '' }}>
                    <label class="form-check-label" for="footer_enabled">Enabled</label>
                  </div>
                  <div class="form-group">
                    <textarea name="footer_script" rows="6" class="form-control @error('footer_script') is-invalid @enderror"
                      style="font-family:monospace;font-size:13px;"
                      placeholder="<script>...</script>">{{ old('footer_script', $tracking->footer_script) }}</textarea>
                    @error('footer_script') <span class="text-danger">{{ $message }}</span> @enderror
                  </div>

                  <button type="submit" class="btn btn-primary">Save</button>
                </form>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
</div>
@endsection
