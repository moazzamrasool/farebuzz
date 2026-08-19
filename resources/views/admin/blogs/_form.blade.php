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

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="reading_time">Reading Time <small class="text-muted">(minutes — leave blank to auto-estimate from content)</small></label>
    <input type="number" name="reading_time" id="reading_time" min="1" class="form-control @error('reading_time') is-invalid @enderror"
      value="{{ old('reading_time', $blog->reading_time ?? '') }}">
    @error('reading_time') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

@include('admin.partials._seo_fields', [
  'seo' => $blog ?? null,
  'seoUrl' => isset($blog) && $blog->slug ? route('blog.show', $blog->slug) : null,
  'seoPreviewFallback' => ($blog->title ?? 'Blog Post').' – FareBuzzer',
])

<button type="submit" class="btn btn-primary">{{ isset($blog) ? 'Update' : 'Create' }} Blog Post</button>
<a href="{{ route('crm.blogs.index') }}" class="btn btn-secondary">Cancel</a>
