@csrf

<div class="form-group">
  <label for="name">Name</label>
  <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
    value="{{ old('name', $travelCategory->name ?? '') }}" placeholder="e.g. Beaches, Honeymoon, Best Seller" required>
  @error('name') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="description">Description</label>
  <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror"
    placeholder="e.g. Sun, sand and sea &mdash; our best beach getaways.">{{ old('description', $travelCategory->description ?? '') }}</textarea>
  @error('description') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="image">Image <small class="text-muted">size: 300x300</small></label>
  <input type="file" name="image" id="image" class="form-control-file @error('image') is-invalid @enderror" accept="image/*">
  @error('image') <span class="text-danger d-block">{{ $message }}</span> @enderror
  @isset($travelCategory)
    @if($travelCategory->image)
      <div class="mt-2">
        <img src="{{ asset('storage/'.$travelCategory->image) }}" alt="{{ $travelCategory->name }}" style="width:80px;height:80px;object-fit:cover;border-radius:6px;">
      </div>
    @endif
  @endisset
</div>

<div class="form-group">
  <label for="badge_color">Badge Color <small class="text-muted">(shown on package/destination badge tags)</small></label>
  <input type="color" name="badge_color" id="badge_color" class="form-control @error('badge_color') is-invalid @enderror"
    value="{{ old('badge_color', $travelCategory->badge_color ?? '#0d6efd') }}" style="max-width:100px;height:42px;">
  @error('badge_color') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="status">Status</label>
    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
      <option value="active" {{ old('status', $travelCategory->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
      <option value="inactive" {{ old('status', $travelCategory->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-6">
    <label for="sort_order">Sort Order</label>
    <input type="number" name="sort_order" id="sort_order" min="0" class="form-control @error('sort_order') is-invalid @enderror"
      value="{{ old('sort_order', $travelCategory->sort_order ?? 0) }}">
    @error('sort_order') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<button type="submit" class="btn btn-primary">{{ isset($travelCategory) ? 'Update' : 'Create' }} Category</button>
<a href="{{ route('crm.travel-categories.index') }}" class="btn btn-secondary">Cancel</a>
