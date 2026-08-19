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

            {{-- ── Sitemap status ─────────────────────────────────────────── --}}
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Sitemap</h3>
                <div class="card-tools">
                  <a href="{{ $sitemapUrl }}" target="_blank" class="btn btn-secondary btn-sm">View sitemap.xml</a>
                </div>
              </div>
              <div class="card-body">
                <div class="row mb-3">
                  <div class="col-md-4">
                    <div class="info-box">
                      <span class="info-box-icon bg-info"><i class="fa fa-link"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Total URLs</span>
                        <span class="info-box-number">{{ $setting->sitemap_url_count ?? '—' }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="info-box">
                      <span class="info-box-icon bg-success"><i class="fa fa-clock"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Last Generated</span>
                        <span class="info-box-number" style="font-size:14px;">
                          {{ $setting->sitemap_generated_at?->diffForHumans() ?? 'Never — generates on first visit' }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 d-flex align-items-center">
                    <form action="{{ route('crm.seo-settings.sitemap.regenerate') }}" method="POST">
                      @csrf
                      <button type="submit" class="btn btn-primary"><i class="fa fa-sync"></i> Regenerate Now</button>
                    </form>
                  </div>
                </div>

                <p class="text-muted">Choose which content types appear in the sitemap and their default priority / change frequency.</p>

                <form action="{{ route('crm.seo-settings.sitemap-config.update') }}" method="POST">
                  @csrf
                  @method('PUT')
                  <table class="table table-bordered table-sm">
                    <thead>
                      <tr>
                        <th>Content type</th>
                        <th style="width:110px;">Included</th>
                        <th style="width:160px;">Priority</th>
                        <th style="width:200px;">Change frequency</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php
                        $labels = [
                          'holiday_packages' => 'Holiday Packages',
                          'hotels' => 'Hotels',
                          'activities' => 'Activities',
                          'destinations' => 'Destinations',
                          'destination_packages' => 'Destination Package Listings',
                          'cms_pages' => 'CMS Pages',
                          'blog' => 'Blog Posts',
                        ];
                        $freqs = ['always','hourly','daily','weekly','monthly','yearly','never'];
                      @endphp
                      @foreach($labels as $type => $label)
                        <tr>
                          <td>{{ $label }}</td>
                          <td class="text-center">
                            <input type="checkbox" name="included[{{ $type }}]" value="1" {{ $sitemapConfig[$type]['included'] ? 'checked' : '' }}>
                          </td>
                          <td>
                            <input type="number" step="0.1" min="0" max="1" class="form-control form-control-sm"
                              name="priority[{{ $type }}]" value="{{ $sitemapConfig[$type]['priority'] }}">
                          </td>
                          <td>
                            <select name="changefreq[{{ $type }}]" class="form-control form-control-sm">
                              @foreach($freqs as $freq)
                                <option value="{{ $freq }}" {{ $sitemapConfig[$type]['changefreq'] === $freq ? 'selected' : '' }}>{{ ucfirst($freq) }}</option>
                              @endforeach
                            </select>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                  <button type="submit" class="btn btn-primary">Save Sitemap Settings</button>
                </form>
              </div>
            </div>

            {{-- ── Site-wide SEO defaults ─────────────────────────────────── --}}
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Site-wide SEO Defaults</h3>
              </div>
              <div class="card-body">
                <p class="text-muted">Used as the last-resort fallback when a page has no OG image of its own, and to fill in the Organization details search engines show for the whole site.</p>

                <form action="{{ route('crm.seo-settings.defaults.update') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  @method('PUT')

                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="organization_name">Organization Name</label>
                      <input type="text" name="organization_name" id="organization_name" class="form-control @error('organization_name') is-invalid @enderror"
                        value="{{ old('organization_name', $setting->organization_name) }}" placeholder="e.g. FareBuzzer Travel">
                      @error('organization_name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group col-md-6">
                      <label for="organization_logo">Organization Logo</label>
                      <input type="file" name="organization_logo" id="organization_logo" class="form-control-file @error('organization_logo') is-invalid @enderror" accept="image/*">
                      @error('organization_logo') <span class="text-danger d-block">{{ $message }}</span> @enderror
                      @if($setting->organization_logo)
                        <div class="mt-2"><img src="{{ asset('storage/'.$setting->organization_logo) }}" style="height:50px;object-fit:contain;"></div>
                      @endif
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="default_og_image">Default OG Image <small class="text-muted">(1200×630 — used when a page and its content type both have none)</small></label>
                    <input type="file" name="default_og_image" id="default_og_image" class="form-control-file @error('default_og_image') is-invalid @enderror" accept="image/*">
                    @error('default_og_image') <span class="text-danger d-block">{{ $message }}</span> @enderror
                    @if($setting->default_og_image)
                      <div class="mt-2"><img src="{{ asset('storage/'.$setting->default_og_image) }}" style="width:120px;height:70px;object-fit:cover;border-radius:6px;"></div>
                    @endif
                  </div>

                  <label class="d-block">Social Links <small class="text-muted">(used in Organization structured data)</small></label>
                  <div class="form-row">
                    @foreach(['facebook' => 'Facebook', 'instagram' => 'Instagram', 'twitter' => 'Twitter / X', 'youtube' => 'YouTube', 'linkedin' => 'LinkedIn'] as $key => $label)
                      <div class="form-group col-md-4">
                        <label for="social_{{ $key }}">{{ $label }}</label>
                        <input type="url" name="social_links[{{ $key }}]" id="social_{{ $key }}" class="form-control @error('social_links.'.$key) is-invalid @enderror"
                          value="{{ old('social_links.'.$key, $setting->social_links[$key] ?? '') }}" placeholder="https://...">
                        @error('social_links.'.$key) <span class="text-danger">{{ $message }}</span> @enderror
                      </div>
                    @endforeach
                  </div>

                  <button type="submit" class="btn btn-primary">Save Defaults</button>
                </form>
              </div>
            </div>

            {{-- ── robots.txt ─────────────────────────────────────────────── --}}
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">robots.txt</h3>
                <div class="card-tools">
                  <a href="{{ $robotsUrl }}" target="_blank" class="btn btn-secondary btn-sm">View robots.txt</a>
                </div>
              </div>
              <div class="card-body">
                <p class="text-muted">
                  Leave blank to use the default below. The <code>Sitemap:</code> line is added automatically
                  and always points at <code>{{ $sitemapUrl }}</code> — you don't need to type it yourself.
                </p>

                @error('robots_txt')
                  <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <form id="robots-form" action="{{ route('crm.seo-settings.robots.update') }}" method="POST">
                  @csrf
                  @method('PUT')
                  <div class="form-group">
                    <textarea name="robots_txt" id="robots_txt" rows="12" class="form-control"
                      style="font-family:monospace;font-size:13px;"
                      placeholder="{{ $defaultRobotsTxt }}">{{ old('robots_txt', $setting->robots_txt) }}</textarea>
                  </div>
                  <div class="form-group form-check" id="confirm-block-wrap" style="display:none;">
                    <input type="checkbox" name="confirm_full_block" id="confirm_full_block" class="form-check-input" value="1">
                    <label class="form-check-label text-danger" for="confirm_full_block">
                      I understand this blocks all search engines from the entire site.
                    </label>
                  </div>
                  <button type="submit" class="btn btn-primary">Save robots.txt</button>
                </form>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
</div>

<script>
  (function () {
    var textarea = document.getElementById('robots_txt');
    var wrap = document.getElementById('confirm-block-wrap');
    var form = document.getElementById('robots-form');

    function blocksEverything(text) {
      var isWildcard = false;
      var lines = text.split(/\r\n|\r|\n/);
      for (var i = 0; i < lines.length; i++) {
        var line = lines[i].trim();
        var uaMatch = line.match(/^user-agent\s*:\s*(.+)$/i);
        if (uaMatch) { isWildcard = uaMatch[1].trim() === '*'; continue; }
        if (isWildcard && /^disallow\s*:\s*\/\s*$/i.test(line)) { return true; }
      }
      return false;
    }

    function refresh() {
      wrap.style.display = blocksEverything(textarea.value) ? 'block' : 'none';
    }

    textarea.addEventListener('input', refresh);
    refresh();

    form.addEventListener('submit', function (e) {
      if (blocksEverything(textarea.value) && !document.getElementById('confirm_full_block').checked) {
        e.preventDefault();
        wrap.style.display = 'block';
        alert('This robots.txt blocks the entire site from search engines. Tick the confirmation box to save it anyway.');
      }
    });
  })();
</script>
@endsection
