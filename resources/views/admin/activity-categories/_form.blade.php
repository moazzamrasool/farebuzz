@csrf

<div class="form-group">
  <label for="name">Name</label>
  <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
    value="{{ old('name', $activityCategory->name ?? '') }}" placeholder="e.g. Adventure, Water Sports, Sightseeing" required>
  @error('name') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="description">Description</label>
  <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror"
    placeholder="e.g. Adrenaline-fuelled experiences like scuba diving and trekking.">{{ old('description', $activityCategory->description ?? '') }}</textarea>
  @error('description') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="icon">Icon <small class="text-muted">(<a href="https://icons.getbootstrap.com/" target="_blank" rel="noopener">Bootstrap Icons</a> class, e.g. bi-water)</small></label>
  <input type="text" name="icon" id="icon" class="form-control @error('icon') is-invalid @enderror"
    value="{{ old('icon', $activityCategory->icon ?? '') }}" placeholder="e.g. bi-water">
  @error('icon') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="status">Status</label>
    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
      <option value="active" {{ old('status', $activityCategory->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
      <option value="inactive" {{ old('status', $activityCategory->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-6">
    <label for="sort_order">Sort Order</label>
    <input type="number" name="sort_order" id="sort_order" min="0" class="form-control @error('sort_order') is-invalid @enderror"
      value="{{ old('sort_order', $activityCategory->sort_order ?? 0) }}">
    @error('sort_order') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<button type="submit" class="btn btn-primary">{{ isset($activityCategory) ? 'Update' : 'Create' }} Category</button>
<a href="{{ route('crm.activity-categories.index') }}" class="btn btn-secondary">Cancel</a>
