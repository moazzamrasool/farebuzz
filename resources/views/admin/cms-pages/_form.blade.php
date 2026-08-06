@csrf

<div class="form-row">
  <div class="form-group col-md-8">
    <label for="title">Title</label>
    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
      value="{{ old('title', $cmsPage->title ?? '') }}" placeholder="e.g. About Us" required>
    @error('title') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-4">
    <label for="slug">Slug</label>
    <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
      value="{{ old('slug', $cmsPage->slug ?? '') }}" placeholder="auto-generated if left blank">
    @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<div class="form-group">
  <label for="body">Body</label>
  <textarea name="body" id="body" rows="10" class="form-control rich-text-editor @error('body') is-invalid @enderror" data-editor-height="500">{{ old('body', $cmsPage->body ?? '') }}</textarea>
  @error('body') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<hr>
<h6>SEO Meta</h6>
<div class="form-group">
  <label for="meta_title">Meta Title</label>
  <input type="text" name="meta_title" id="meta_title" class="form-control @error('meta_title') is-invalid @enderror"
    value="{{ old('meta_title', $cmsPage->meta_title ?? '') }}">
  @error('meta_title') <span class="text-danger">{{ $message }}</span> @enderror
</div>
<div class="form-group">
  <label for="meta_description">Meta Description</label>
  <textarea name="meta_description" id="meta_description" rows="2" class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description', $cmsPage->meta_description ?? '') }}</textarea>
  @error('meta_description') <span class="text-danger">{{ $message }}</span> @enderror
</div>
<div class="form-group">
  <label for="meta_keywords">Meta Keywords</label>
  <input type="text" name="meta_keywords" id="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror"
    value="{{ old('meta_keywords', $cmsPage->meta_keywords ?? '') }}" placeholder="comma, separated, keywords">
  @error('meta_keywords') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="status">Status</label>
  <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
    <option value="active" {{ old('status', $cmsPage->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
    <option value="inactive" {{ old('status', $cmsPage->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
  </select>
  @error('status') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<button type="submit" class="btn btn-primary">{{ isset($cmsPage) ? 'Update' : 'Create' }} Page</button>
<a href="{{ route('crm.cms-pages.index') }}" class="btn btn-secondary">Cancel</a>
