@csrf

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="travel_category_id">Package Category</label>
    <select name="travel_category_id" id="travel_category_id" class="form-control @error('travel_category_id') is-invalid @enderror">
      <option value="">Select a category</option>
      @foreach($travelCategories as $category)
        <option value="{{ $category->id }}" {{ (int) old('travel_category_id', $package->travel_category_id ?? '') === $category->id ? 'selected' : '' }}>
          {{ $category->name }}
        </option>
      @endforeach
    </select>
    @error('travel_category_id') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-6">
    <label for="type">Package Type</label>
    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror">
      <option value="domestic" {{ old('type', $package->type ?? 'domestic') === 'domestic' ? 'selected' : '' }}>Domestic</option>
      <option value="international" {{ old('type', $package->type ?? '') === 'international' ? 'selected' : '' }}>International</option>
    </select>
    @error('type') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<div class="form-group">
  <label for="name">Name</label>
  <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
    value="{{ old('name', $package->name ?? '') }}" placeholder="e.g. India Beach Packages, Honeymoon Specials" required>
  @error('name') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="slug">Slug</label>
  <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
    value="{{ old('slug', $package->slug ?? '') }}" placeholder="e.g. india-beach-packages (auto-generated from name if left blank)">
  @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="description">Description</label>
  <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror"
    placeholder="e.g. Curated beach packages across India for every traveller.">{{ old('description', $package->description ?? '') }}</textarea>
  @error('description') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="meta">Meta</label>
  <textarea name="meta" id="meta" rows="2" class="form-control @error('meta') is-invalid @enderror"
    placeholder="e.g. SEO meta description for this package listing">{{ old('meta', $package->meta ?? '') }}</textarea>
  @error('meta') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="image">Image <small class="text-muted">size: 216x270</small></label>
    <input type="file" name="image" id="image" class="form-control-file @error('image') is-invalid @enderror" accept="image/*">
    @error('image') <span class="text-danger d-block">{{ $message }}</span> @enderror
    @isset($package)
      @if($package->image)
        <div class="mt-2">
          <img src="{{ asset('storage/'.$package->image) }}" alt="{{ $package->name }}" style="width:80px;height:80px;object-fit:cover;border-radius:6px;">
        </div>
      @endif
    @endisset
  </div>
  <div class="form-group col-md-6">
    <label for="banner_image">Banner Image <small class="text-muted">size: 1366x396</small></label>
    <input type="file" name="banner_image" id="banner_image" class="form-control-file @error('banner_image') is-invalid @enderror" accept="image/*">
    @error('banner_image') <span class="text-danger d-block">{{ $message }}</span> @enderror
    @isset($package)
      @if($package->banner_image)
        <div class="mt-2">
          <img src="{{ asset('storage/'.$package->banner_image) }}" alt="{{ $package->name }}" style="width:120px;height:40px;object-fit:cover;border-radius:6px;">
        </div>
      @endif
    @endisset
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="status">Status</label>
    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
      <option value="active" {{ old('status', $package->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
      <option value="inactive" {{ old('status', $package->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-6">
    <label for="sort_order">Sort Order</label>
    <input type="number" name="sort_order" id="sort_order" min="0" class="form-control @error('sort_order') is-invalid @enderror"
      value="{{ old('sort_order', $package->sort_order ?? 0) }}">
    @error('sort_order') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<button type="submit" class="btn btn-primary">{{ isset($package) ? 'Update' : 'Create' }} Package</button>
<a href="{{ route('crm.packages.index') }}" class="btn btn-secondary">Cancel</a>
