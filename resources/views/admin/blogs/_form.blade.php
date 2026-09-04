@csrf

<div class="form-row">
  <div class="form-group col-md-8">
    <label for="title">Title</label>
    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
      value="{{ old('title', $blog->title ?? '') }}" placeholder="e.g. 10 Hidden Beaches in Goa You Must Visit" required>
    <span class="text-danger d-block" data-error-for="title">@error('title'){{ $message }}@enderror</span>
  </div>
  <div class="form-group col-md-4">
    <label for="slug">Slug</label>
    <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
      value="{{ old('slug', $blog->slug ?? '') }}" placeholder="auto-generated if left blank">
    <span class="text-danger d-block" data-error-for="slug">@error('slug'){{ $message }}@enderror</span>
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
    <span class="text-danger d-block" data-error-for="category">@error('category'){{ $message }}@enderror</span>
  </div>
  <div class="form-group col-md-6">
    <label for="author_name">Author Name</label>
    <input type="text" name="author_name" id="author_name" class="form-control @error('author_name') is-invalid @enderror"
      value="{{ old('author_name', $blog->author_name ?? '') }}" placeholder="e.g. FareBuzzer Team">
    <span class="text-danger d-block" data-error-for="author_name">@error('author_name'){{ $message }}@enderror</span>
  </div>
</div>

<div class="form-group">
  <label for="excerpt">Excerpt <small class="text-muted">short summary used for card previews, ~160 characters</small></label>
  <textarea name="excerpt" id="excerpt" rows="2" maxlength="500" class="form-control @error('excerpt') is-invalid @enderror"
    placeholder="A short teaser shown on the blog listing page.">{{ old('excerpt', $blog->excerpt ?? '') }}</textarea>
  <span class="text-danger d-block" data-error-for="excerpt">@error('excerpt'){{ $message }}@enderror</span>
</div>

<div class="form-group">
  <label for="featured_image">Featured Image <small class="text-muted">size: 1200x630</small></label>
  <input type="file" name="featured_image" id="featured_image" class="form-control-file @error('featured_image') is-invalid @enderror" accept="image/*">
  <span class="text-danger d-block" data-error-for="featured_image">@error('featured_image'){{ $message }}@enderror</span>
  <div class="mt-2" id="featured_image_preview_wrap" style="{{ isset($blog) && $blog->featured_image ? '' : 'display:none' }}">
    <img id="featured_image_preview" src="{{ isset($blog) && $blog->featured_image ? asset('storage/'.$blog->featured_image) : '' }}" alt="Featured image preview" style="width:120px;height:70px;object-fit:cover;border-radius:6px;">
  </div>
</div>

<div class="form-group">
  <label for="content">Content</label>
  <textarea name="content" id="content" rows="10" class="form-control rich-text-editor @error('content') is-invalid @enderror"
    data-editor-height="500">{{ old('content', $blog->content ?? '') }}</textarea>
  <span class="text-danger d-block" data-error-for="content">@error('content'){{ $message }}@enderror</span>
</div>

<div class="form-row">
  <div class="form-group col-md-4">
    <label for="status">Status</label>
    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
      <option value="draft" {{ old('status', $blog->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
      <option value="published" {{ old('status', $blog->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
    </select>
    <span class="text-danger d-block" data-error-for="status">@error('status'){{ $message }}@enderror</span>
  </div>
  <div class="form-group col-md-4">
    <label for="published_at">Published Date</label>
    <input type="datetime-local" name="published_at" id="published_at" class="form-control @error('published_at') is-invalid @enderror"
      value="{{ old('published_at', isset($blog) && $blog->published_at ? $blog->published_at->format('Y-m-d\TH:i') : '') }}">
    <small class="form-text text-muted">Leave blank to publish immediately when status is set to Published.</small>
    <span class="text-danger d-block" data-error-for="published_at">@error('published_at'){{ $message }}@enderror</span>
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
    <span class="text-danger d-block" data-error-for="reading_time">@error('reading_time'){{ $message }}@enderror</span>
  </div>
</div>

@include('admin.partials._seo_fields', [
  'seo' => $blog ?? null,
  'seoUrl' => isset($blog) && $blog->slug ? route('blog.show', $blog->slug) : null,
  'seoPreviewFallback' => ($blog->title ?? 'Blog Post').' – FareBuzzer',
])

<button type="submit" class="btn btn-primary">
  <span class="btn-label">{{ isset($blog) ? 'Update' : 'Create' }} Blog Post</span>
  <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
</button>
<a href="{{ route('crm.blogs.index') }}" class="btn btn-secondary">Cancel</a>

<script>
(function ($) {
  function bindImagePreview(inputId, previewImgId, previewWrapId) {
    var input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('change', function () {
      var file = this.files && this.files[0];
      var $wrap = document.getElementById(previewWrapId);
      var $img = document.getElementById(previewImgId);
      if (!file) return;
      var reader = new FileReader();
      reader.onload = function (e) {
        if ($img) $img.src = e.target.result;
        if ($wrap) $wrap.style.display = '';
      };
      reader.readAsDataURL(file);
    });
  }

  bindImagePreview('featured_image', 'featured_image_preview', 'featured_image_preview_wrap');
  bindImagePreview('og_image', 'og_image_preview', 'og_image_preview_wrap');

  var $form = $('#blogForm');
  if (!$form.length) return;

  function clearErrors() {
    $form.find('.is-invalid').removeClass('is-invalid');
    $form.find('[data-error-for]').text('');
  }

  function setLoading(isLoading) {
    var $btn = $form.find('button[type="submit"]');
    $btn.prop('disabled', isLoading);
    $btn.find('.btn-label').toggleClass('d-none', isLoading);
    $btn.find('.spinner-border').toggleClass('d-none', !isLoading);
  }

  $form.off('submit.blogForm').on('submit.blogForm', function (e) {
    e.preventDefault();

    // Push CKEditor's live content back into its <textarea> before FormData reads it.
    $form.find('.rich-text-editor').each(function () {
      var editor = $(this).data('ckeditor-instance');
      if (editor) {
        this.value = editor.getData();
      }
    });

    clearErrors();
    setLoading(true);

    var formData = new FormData(this);

    $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          if (typeof toastr !== 'undefined') {
            toastr.success(response.message || 'Saved successfully.');
          }
          setTimeout(function () {
            window.location.href = response.redirect;
          }, 800);
        }
      },
      error: function (xhr) {
        setLoading(false);
        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
          var allMessages = [];
          $.each(xhr.responseJSON.errors, function (field, messages) {
            $form.find('[name="' + field + '"]').addClass('is-invalid');
            $form.find('[data-error-for="' + field + '"]').text(messages[0]);
            allMessages.push(messages[0]);
          });
          var $seoPanel = $('#seoPanel');
          if ($seoPanel.find('.is-invalid').length) {
            $seoPanel.collapse('show');
          }
          if (typeof toastr !== 'undefined') {
            toastr.error(allMessages.join('<br>'));
          }
          var $firstError = $form.find('.is-invalid').first();
          if ($firstError.length) {
            $('html, body').animate({ scrollTop: $firstError.offset().top - 120 }, 300);
          }
        } else if (typeof toastr !== 'undefined') {
          toastr.error('Something went wrong. Please try again.');
        }
      }
    });
  });
})(jQuery);
</script>
