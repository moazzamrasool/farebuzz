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

@include('admin.partials._seo_fields', [
  'seo' => $cmsPage ?? null,
  'seoUrl' => isset($cmsPage) && $cmsPage->slug ? url($cmsPage->slug) : null,
  'seoPreviewFallback' => ($cmsPage->title ?? 'Page').' – FareBuzzer',
])

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
