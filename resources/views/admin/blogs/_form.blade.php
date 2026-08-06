@csrf

<div class="form-row">
  <div class="form-group col-md-8">
    <label for="title">Title</label>
    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
      value="{{ old('title', $blog->title ?? '') }}" placeholder="e.g. 10 Hidden Beaches in Goa You Must Visit" required>
    @error('title') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-4">
    <label for="slug">Slug</label>
    <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
      value="{{ old('slug', $blog->slug ?? '') }}" placeholder="auto-generated if left blank">
    @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="category">Category</label>
    <select name="category" id="category" class="form-control @error('category') is-invalid @enderror">
      @foreach(\App\Models\Blog::CATEGORIES as $value => $label)
        <option value="{{ $value }}" {{ old('category', $blog->category ?? 'general') === $value ? 'selected' : '' }}>{{ $label }}</option>
      @endforeach
    </select>
    @error('category') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-6">
    <label for="author_name">Author Name</label>
    <input type="text" name="author_name" id="author_name" class="form-control @error('author_name') is-invalid @enderror"
      value="{{ old('author_name', $blog->author_name ?? '') }}" placeholder="e.g. FareBuzzer Team">
    @error('author_name') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<div class="form-group">
  <label for="excerpt">Excerpt <small class="text-muted">short summary used for card previews, ~160 characters</small></label>
  <textarea name="excerpt" id="excerpt" rows="2" maxlength="500" class="form-control @error('excerpt') is-invalid @enderror"
    placeholder="A short teaser shown on the blog listing page.">{{ old('excerpt', $blog->excerpt ?? '') }}</textarea>
  @error('excerpt') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="featured_image">Featured Image <small class="text-muted">size: 1200x630</small></label>
  <input type="file" name="featured_image" id="featured_image" class="form-control-file @error('featured_image') is-invalid @enderror" accept="image/*">
  @error('featured_image') <span class="text-danger d-block">{{ $message }}</span> @enderror
  @isset($blog)
    @if($blog->featured_image)
      <div class="mt-2">
        <img src="{{ asset('storage/'.$blog->featured_image) }}" alt="{{ $blog->title }}" style="width:120px;height:70px;object-fit:cover;border-radius:6px;">
      </div>
    @endif
  @endisset
</div>

<div class="form-group">
  <label for="content">Content</label>
  <textarea name="content" id="content" rows="10" class="form-control rich-text-editor @error('content') is-invalid @enderror"
    data-editor-height="500">{{ old('content', $blog->content ?? '') }}</textarea>
  @error('content') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-row">
  <div class="form-group col-md-4">
    <label for="status">Status</label>
    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
      <option value="draft" {{ old('status', $blog->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
      <option value="published" {{ old('status', $blog->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
    </select>
    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-4">
    <label for="published_at">Published Date</label>
    <input type="datetime-local" name="published_at" id="published_at" class="form-control @error('published_at') is-invalid @enderror"
      value="{{ old('published_at', isset($blog) && $blog->published_at ? $blog->published_at->format('Y-m-d\TH:i') : '') }}">
    <small class="form-text text-muted">Leave blank to publish immediately when status is set to Published.</small>
    @error('published_at') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-4">
    <label class="d-block">Featured</label>
    <div class="custom-control custom-checkbox">
      <input type="checkbox" name="is_featured" value="1" class="custom-control-input" id="is_featured"
        {{ old('is_featured', $blog->is_featured ?? false) ? 'checked' : '' }}>
      <label class="custom-control-label" for="is_featured">Show as featured post</label>
    </div>
  </div>
</div>

<hr>
<a href="#seoPanel" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="seoPanel" class="d-inline-flex align-items-center mb-2" style="text-decoration:none;">
  <h6 class="mb-0">SEO Settings</h6>
  <i class="fa fa-chevron-down ml-2" style="font-size:12px;"></i>
</a>
<div class="collapse" id="seoPanel">
  <div class="form-group">
    <label for="meta_title">Meta Title</label>
    <input type="text" name="meta_title" id="meta_title" class="form-control @error('meta_title') is-invalid @enderror"
      value="{{ old('meta_title', $blog->meta_title ?? '') }}">
    @error('meta_title') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group">
    <label for="meta_description">Meta Description</label>
    <textarea name="meta_description" id="meta_description" rows="2" maxlength="255" class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description', $blog->meta_description ?? '') }}</textarea>
    <small class="form-text text-muted"><span id="metaDescriptionCount">0</span>/160 characters (recommended)</small>
    @error('meta_description') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group">
    <label for="meta_keywords">Meta Keywords</label>
    <input type="text" name="meta_keywords" id="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror"
      value="{{ old('meta_keywords', $blog->meta_keywords ?? '') }}" placeholder="comma, separated, keywords">
    @error('meta_keywords') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group">
    <label for="og_image">OG Image <small class="text-muted">shown when the post is shared on social media, size: 1200x630</small></label>
    <input type="file" name="og_image" id="og_image" class="form-control-file @error('og_image') is-invalid @enderror" accept="image/*">
    @error('og_image') <span class="text-danger d-block">{{ $message }}</span> @enderror
    @isset($blog)
      @if($blog->og_image)
        <div class="mt-2">
          <img src="{{ asset('storage/'.$blog->og_image) }}" style="width:120px;height:70px;object-fit:cover;border-radius:6px;">
        </div>
      @endif
    @endisset
  </div>
  <div class="form-group">
    <label for="canonical_url">Canonical URL</label>
    <input type="url" name="canonical_url" id="canonical_url" class="form-control @error('canonical_url') is-invalid @enderror"
      value="{{ old('canonical_url', $blog->canonical_url ?? '') }}" placeholder="https://farebuzzer.com/blog/... (leave blank to use this post's own URL)">
    @error('canonical_url') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<button type="submit" class="btn btn-primary">{{ isset($blog) ? 'Update' : 'Create' }} Blog Post</button>
<a href="{{ route('crm.blogs.index') }}" class="btn btn-secondary">Cancel</a>

<script>
  (function () {
    var $textarea = document.getElementById('meta_description');
    var $count = document.getElementById('metaDescriptionCount');
    if (!$textarea || !$count) return;

    function update() {
      $count.textContent = $textarea.value.length;
    }
    update();
    $textarea.addEventListener('input', update);
  })();
</script>
