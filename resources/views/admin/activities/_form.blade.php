@csrf

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="destination_id">Destination (optional)</label>
    <select name="destination_id" id="destination_id" class="form-control @error('destination_id') is-invalid @enderror">
      <option value="">None</option>
      @foreach($destinations as $dest)
        <option value="{{ $dest->id }}" {{ (int) old('destination_id', $activity->destination_id ?? '') === $dest->id ? 'selected' : '' }}>
          {{ $dest->name }}
        </option>
      @endforeach
    </select>
    @error('destination_id') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-6">
    <label for="travel_category_id">Package Category (optional)</label>
    <select name="travel_category_id" id="travel_category_id" class="form-control @error('travel_category_id') is-invalid @enderror">
      <option value="">None</option>
      @foreach($travelCategories as $category)
        <option value="{{ $category->id }}" {{ (int) old('travel_category_id', $activity->travel_category_id ?? '') === $category->id ? 'selected' : '' }}>
          {{ $category->name }}
        </option>
      @endforeach
    </select>
    @error('travel_category_id') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<div class="form-group">
  <label for="name">Name</label>
  <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
    value="{{ old('name', $activity->name ?? '') }}" placeholder="e.g. Sunset Cruise, Scuba Diving" required>
  @error('name') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="category">Category</label>
    <input type="text" name="category" id="category" placeholder="e.g. Adventure, Water Sports, Sightseeing"
      class="form-control @error('category') is-invalid @enderror" value="{{ old('category', $activity->category ?? '') }}">
    @error('category') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-6">
    <label for="price">Price</label>
    <input type="number" step="0.01" min="0" name="price" id="price" class="form-control @error('price') is-invalid @enderror"
      value="{{ old('price', $activity->price ?? '') }}" placeholder="e.g. 2500">
    @error('price') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<div class="form-group">
  <label for="duration">Duration</label>
  <input type="text" name="duration" id="duration" class="form-control @error('duration') is-invalid @enderror"
    value="{{ old('duration', $activity->duration ?? '') }}" placeholder="e.g. 3 hours, Half day">
  @error('duration') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="description">Description</label>
  <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror"
    placeholder="e.g. Enjoy a thrilling water sports session with expert guides.">{{ old('description', $activity->description ?? '') }}</textarea>
  @error('description') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="image">Image <small class="text-muted">size: 400x300</small></label>
  <input type="file" name="image" id="image" class="form-control-file @error('image') is-invalid @enderror" accept="image/*">
  @error('image') <span class="text-danger d-block">{{ $message }}</span> @enderror
  @isset($activity)
    @if($activity->image)
      <div class="mt-2">
        <img src="{{ asset('storage/'.$activity->image) }}" alt="{{ $activity->name }}" style="width:80px;height:80px;object-fit:cover;border-radius:6px;">
      </div>
    @endif
  @endisset
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="status">Status</label>
    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
      <option value="active" {{ old('status', $activity->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
      <option value="inactive" {{ old('status', $activity->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-6">
    <label for="sort_order">Sort Order</label>
    <input type="number" name="sort_order" id="sort_order" min="0" class="form-control @error('sort_order') is-invalid @enderror"
      value="{{ old('sort_order', $activity->sort_order ?? 0) }}">
    @error('sort_order') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

@include('admin.partials._seo_fields', [
  'seo' => $activity ?? null,
  'seoUrl' => isset($activity) && $activity->slug ? route('activities.show', $activity->slug) : null,
  'seoPreviewFallback' => ($activity->name ?? 'Activity').' – FareBuzzer',
])

<button type="submit" class="btn btn-primary">{{ isset($activity) ? 'Update' : 'Create' }} Activity</button>
<a href="{{ route('crm.activities.index') }}" class="btn btn-secondary">Cancel</a>
