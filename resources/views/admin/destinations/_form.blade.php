@csrf

<div class="form-group">
  <label for="name">Name</label>
  <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
    value="{{ old('name', $destination->name ?? '') }}" placeholder="e.g. Goa, Maldives, Manali" required>
  @error('name') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="slug">Slug</label>
    <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
      value="{{ old('slug', $destination->slug ?? '') }}" placeholder="e.g. goa (auto-generated from name if left blank)">
    @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-6">
    <label for="local">Local</label>
    <select name="local" id="local" class="form-control @error('local') is-invalid @enderror">
      <option value="domestic" {{ old('local', $destination->local ?? 'domestic') === 'domestic' ? 'selected' : '' }}>Domestic</option>
      <option value="international" {{ old('local', $destination->local ?? '') === 'international' ? 'selected' : '' }}>International</option>
    </select>
    @error('local') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="country">Country</label>
    <input type="text" name="country" id="country" class="form-control @error('country') is-invalid @enderror"
      value="{{ old('country', $destination->country ?? '') }}" placeholder="e.g. India" required>
    @error('country') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-6">
    <label for="city">City / Region</label>
    <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror"
      value="{{ old('city', $destination->city ?? '') }}" placeholder="e.g. Goa">
    @error('city') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<div class="form-group">
  <label for="description">Description</label>
  <textarea name="description" id="description" rows="4" class="form-control rich-text-editor @error('description') is-invalid @enderror"
    data-editor-height="250" placeholder="e.g. Sun-kissed beaches, vibrant nightlife and Portuguese heritage.">{{ old('description', $destination->description ?? '') }}</textarea>
  @error('description') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="meta">Meta <small class="text-muted">(legacy free-text note, not rendered on the public page)</small></label>
  <textarea name="meta" id="meta" rows="2" class="form-control @error('meta') is-invalid @enderror"
    placeholder="e.g. internal notes about this destination">{{ old('meta', $destination->meta ?? '') }}</textarea>
  @error('meta') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<hr>
<h5 class="mb-3">SEO</h5>

<div class="form-group">
  <label for="meta_title">Meta Title</label>
  <input type="text" name="meta_title" id="meta_title" class="form-control @error('meta_title') is-invalid @enderror"
    value="{{ old('meta_title', $destination->meta_title ?? '') }}" maxlength="255" placeholder="e.g. Kashmir Tour Packages | Best Deals 2026 | FareBuzzer Travel">
  @error('meta_title') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="meta_description">Meta Description</label>
  <textarea name="meta_description" id="meta_description" rows="3" class="form-control @error('meta_description') is-invalid @enderror"
    maxlength="500" placeholder="e.g. Explore Kashmir with FareBuzzer Travel...">{{ old('meta_description', $destination->meta_description ?? '') }}</textarea>
  @error('meta_description') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="meta_keywords">Meta Keywords <small class="text-muted">(comma-separated)</small></label>
  <input type="text" name="meta_keywords" id="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror"
    value="{{ old('meta_keywords', $destination->meta_keywords ?? '') }}" maxlength="255" placeholder="e.g. kashmir tour package, kashmir honeymoon package">
  @error('meta_keywords') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="focus_keyword">Focus Keyword</label>
  <input type="text" name="focus_keyword" id="focus_keyword" class="form-control @error('focus_keyword') is-invalid @enderror"
    value="{{ old('focus_keyword', $destination->focus_keyword ?? '') }}" maxlength="255" placeholder="e.g. Kashmir tour package from Delhi">
  @error('focus_keyword') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="seo_content">Long-form SEO Content</label>
  <textarea name="seo_content" id="seo_content" rows="8" class="form-control rich-text-editor @error('seo_content') is-invalid @enderror"
    placeholder="e.g. Kashmir, often called Paradise on Earth...">{{ old('seo_content', $destination->seo_content ?? '') }}</textarea>
  @error('seo_content') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="cover_image">Cover Image <small class="text-muted">size: 1200x600</small></label>
  <input type="file" name="cover_image" id="cover_image" class="form-control-file @error('cover_image') is-invalid @enderror" accept="image/*">
  @error('cover_image') <span class="text-danger d-block">{{ $message }}</span> @enderror
  @isset($destination)
    @if($destination->cover_image)
      <div class="mt-2">
        <img src="{{ asset('storage/'.$destination->cover_image) }}" alt="{{ $destination->name }}" style="width:80px;height:80px;object-fit:cover;border-radius:6px;">
      </div>
    @endif
  @endisset
</div>

<div class="form-group">
  <label for="gallery_images">Gallery Images <small class="text-muted">size: 800x600 each</small></label>
  <input type="file" name="gallery_images[]" id="gallery_images" class="form-control-file" accept="image/*" multiple>
  <small class="form-text text-muted">Uploading new gallery images will replace the existing gallery.</small>
  @isset($destination)
    @if(!empty($destination->gallery_images))
      <div class="mt-2 d-flex flex-wrap" style="gap:8px;">
        @foreach($destination->gallery_images as $image)
          <img src="{{ asset('storage/'.$image) }}" style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
        @endforeach
      </div>
    @endif
  @endisset
</div>

<div class="form-row">
  <div class="form-group col-md-4">
    <label for="status">Status</label>
    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
      <option value="active" {{ old('status', $destination->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
      <option value="inactive" {{ old('status', $destination->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-4">
    <label for="sort_order">Sort Order</label>
    <input type="number" name="sort_order" id="sort_order" min="0" class="form-control @error('sort_order') is-invalid @enderror"
      value="{{ old('sort_order', $destination->sort_order ?? 0) }}">
    @error('sort_order') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-4">
    <label class="d-block">Featured</label>
    <div class="custom-control custom-checkbox">
      <input type="checkbox" name="featured" value="1" class="custom-control-input" id="featured"
        {{ old('featured', $destination->featured ?? false) ? 'checked' : '' }}>
      <label class="custom-control-label" for="featured">Show as featured destination</label>
    </div>
  </div>
</div>

<button type="submit" class="btn btn-primary">{{ isset($destination) ? 'Update' : 'Create' }} Destination</button>
<a href="{{ route('crm.destinations.index') }}" class="btn btn-secondary">Cancel</a>
