{{--
  Shared SEO block, included by every content editor (Holiday Packages, Destinations,
  Hotels, Activities, CMS Pages, Blog, About Page, Homepage, Contact Us).

  Expects:
    $seo               – the model instance being edited (or null on a create form)
    $seoUrl             – (optional) absolute frontend URL of this page, used for the
                           canonical placeholder + preview breadcrumb; null on create
                           forms where the slug doesn't exist yet
    $seoPreviewFallback – (optional) fallback title shown in the Google preview and
                           counter before the admin types a Meta Title
--}}
@php
  $seo = $seo ?? null;
  $seoUrl = $seoUrl ?? null;
  $seoPreviewFallback = $seoPreviewFallback ?? 'Page title';
  $seoHost = $seoUrl ? parse_url($seoUrl, PHP_URL_HOST) : request()->getHost();
  $seoPath = $seoUrl ? trim(parse_url($seoUrl, PHP_URL_PATH) ?? '', '/') : '';
@endphp

<hr>
<a href="#seoPanel" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="seoPanel" class="d-inline-flex align-items-center mb-2" style="text-decoration:none;">
  <h6 class="mb-0">SEO Settings</h6>
  <i class="fa fa-chevron-down ml-2" style="font-size:12px;"></i>
</a>
<div class="collapse" id="seoPanel">

  <div class="mb-3 p-3" style="background:#fff;border:1px solid #dadce0;border-radius:8px;max-width:600px;">
    <div style="font-size:13px;color:#202124;">{{ $seoHost }}{{ $seoPath ? ' › '.$seoPath : '' }}</div>
    <div id="seoPreviewTitle" data-fallback="{{ $seoPreviewFallback }}" style="color:#1a0dab;font-size:18px;line-height:1.3;margin-top:2px;">{{ old('meta_title', $seo?->meta_title) ?: $seoPreviewFallback }}</div>
    <div id="seoPreviewDescription" data-fallback="No meta description yet — search engines will show an excerpt of the page content instead." style="color:#4d5156;font-size:13px;line-height:1.4;margin-top:2px;">{{ old('meta_description', $seo?->meta_description) ?: 'No meta description yet — search engines will show an excerpt of the page content instead.' }}</div>
  </div>

  <div class="form-group">
    <label for="meta_title">Meta Title <small class="text-muted">(ideal 50–60 characters)</small></label>
    <input type="text" name="meta_title" id="meta_title" class="form-control @error('meta_title') is-invalid @enderror"
      value="{{ old('meta_title', $seo?->meta_title) }}" maxlength="255">
    <small class="form-text text-muted"><span id="metaTitleCount">0</span>/60 characters</small>
    <span class="text-danger d-block" data-error-for="meta_title">@error('meta_title'){{ $message }}@enderror</span>
  </div>

  <div class="form-group">
    <label for="meta_description">Meta Description <small class="text-muted">(ideal 150–160 characters)</small></label>
    <textarea name="meta_description" id="meta_description" rows="3" maxlength="500" class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description', $seo?->meta_description) }}</textarea>
    <small class="form-text text-muted"><span id="metaDescriptionCount">0</span>/160 characters</small>
    <span class="text-danger d-block" data-error-for="meta_description">@error('meta_description'){{ $message }}@enderror</span>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="meta_keywords">Meta Keywords <small class="text-muted">(comma-separated)</small></label>
      <input type="text" name="meta_keywords" id="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror"
        value="{{ old('meta_keywords', $seo?->meta_keywords) }}" placeholder="e.g. goa packages, beach holiday">
      <span class="text-danger d-block" data-error-for="meta_keywords">@error('meta_keywords'){{ $message }}@enderror</span>
    </div>
    <div class="form-group col-md-6">
      <label for="focus_keyword">Focus Keyword</label>
      <input type="text" name="focus_keyword" id="focus_keyword" class="form-control @error('focus_keyword') is-invalid @enderror"
        value="{{ old('focus_keyword', $seo?->focus_keyword) }}" placeholder="e.g. goa holiday package">
      <span class="text-danger d-block" data-error-for="focus_keyword">@error('focus_keyword'){{ $message }}@enderror</span>
    </div>
  </div>

  <div class="form-group">
    <label for="tags">Tags <small class="text-muted">(comma-separated)</small></label>
    <input type="text" name="tags" id="tags" class="form-control @error('tags') is-invalid @enderror"
      value="{{ old('tags', $seo?->tags) }}" placeholder="e.g. goa, beach, honeymoon">
    <span class="text-danger d-block" data-error-for="tags">@error('tags'){{ $message }}@enderror</span>
  </div>

  <div class="form-group">
    <label for="canonical_url">Canonical URL</label>
    <input type="url" name="canonical_url" id="canonical_url" class="form-control @error('canonical_url') is-invalid @enderror"
      value="{{ old('canonical_url', $seo?->canonical_url) }}" placeholder="{{ $seoUrl ?: 'https://www.farebuzzertravel.com/...' }}">
    <small class="form-text text-muted">
      @if($seoUrl)
        Leave blank to use this page's own URL: {{ $seoUrl }}
      @else
        Leave blank to use this page's own URL (available once saved).
      @endif
    </small>
    <span class="text-danger d-block" data-error-for="canonical_url">@error('canonical_url'){{ $message }}@enderror</span>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="og_title">OG Title <small class="text-muted">(falls back to Meta Title)</small></label>
      <input type="text" name="og_title" id="og_title" class="form-control @error('og_title') is-invalid @enderror"
        value="{{ old('og_title', $seo?->og_title) }}">
      <span class="text-danger d-block" data-error-for="og_title">@error('og_title'){{ $message }}@enderror</span>
    </div>
    <div class="form-group col-md-6">
      <label for="og_image">OG Image <small class="text-muted">(1200×630 — falls back to the cover image)</small></label>
      <input type="file" name="og_image" id="og_image" class="form-control-file @error('og_image') is-invalid @enderror" accept="image/*">
      <span class="text-danger d-block" data-error-for="og_image">@error('og_image'){{ $message }}@enderror</span>
      <div class="mt-2" id="og_image_preview_wrap" style="{{ $seo?->og_image ? '' : 'display:none' }}">
        <img id="og_image_preview" src="{{ $seo?->og_image ? asset('storage/'.$seo->og_image) : '' }}" alt="OG image preview" style="width:120px;height:70px;object-fit:cover;border-radius:6px;">
      </div>
    </div>
  </div>

  <div class="form-group">
    <label for="og_description">OG Description <small class="text-muted">(falls back to Meta Description)</small></label>
    <textarea name="og_description" id="og_description" rows="2" maxlength="500" class="form-control @error('og_description') is-invalid @enderror">{{ old('og_description', $seo?->og_description) }}</textarea>
    <span class="text-danger d-block" data-error-for="og_description">@error('og_description'){{ $message }}@enderror</span>
  </div>

  <div class="form-group">
    <label class="d-block">Robots</label>
    <div class="custom-control custom-checkbox custom-control-inline">
      <input type="hidden" name="robots_index" value="0">
      <input type="checkbox" name="robots_index" value="1" class="custom-control-input" id="robots_index"
        {{ old('robots_index', $seo?->robots_index ?? true) ? 'checked' : '' }}>
      <label class="custom-control-label" for="robots_index">Index (let search engines list this page)</label>
    </div>
    <div class="custom-control custom-checkbox custom-control-inline">
      <input type="hidden" name="robots_follow" value="0">
      <input type="checkbox" name="robots_follow" value="1" class="custom-control-input" id="robots_follow"
        {{ old('robots_follow', $seo?->robots_follow ?? true) ? 'checked' : '' }}>
      <label class="custom-control-label" for="robots_follow">Follow (let search engines follow links on this page)</label>
    </div>
  </div>
</div>

<script>
(function () {
  function bindCounter(inputId, countId, max, previewId) {
    var $input = document.getElementById(inputId);
    var $count = document.getElementById(countId);
    var $preview = previewId ? document.getElementById(previewId) : null;
    if (!$input) return;

    function update() {
      var len = $input.value.length;
      if ($count) {
        $count.textContent = len;
        $count.parentElement.classList.toggle('text-danger', len > max);
      }
      if ($preview) {
        $preview.textContent = $input.value || $preview.dataset.fallback;
      }
    }

    $input.addEventListener('input', update);
    update();
  }

  bindCounter('meta_title', 'metaTitleCount', 60, 'seoPreviewTitle');
  bindCounter('meta_description', 'metaDescriptionCount', 160, 'seoPreviewDescription');
})();
</script>
