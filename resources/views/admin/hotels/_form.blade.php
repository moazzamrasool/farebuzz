@csrf
@php $htl = $hotel ?? null; @endphp

<div class="form-row">
  <div class="form-group col-md-8">
    <label for="name">Name</label>
    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
      value="{{ old('name', $htl->name ?? '') }}" placeholder="e.g. Taj Vivanta Goa" required>
    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-4">
    <label for="slug">Slug <small class="text-muted">(auto if blank)</small></label>
    <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
      value="{{ old('slug', $htl->slug ?? '') }}" placeholder="e.g. taj-vivanta-goa">
    @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-3">
    <label for="star_rating">Star Rating</label>
    <select name="star_rating" id="star_rating" class="form-control @error('star_rating') is-invalid @enderror">
      @foreach([0 => 'Unrated', 1 => '1 Star', 2 => '2 Star', 3 => '3 Star', 4 => '4 Star', 5 => '5 Star'] as $value => $label)
        <option value="{{ $value }}" {{ (int) old('star_rating', $htl->star_rating ?? 0) === $value ? 'selected' : '' }}>{{ $label }}</option>
      @endforeach
    </select>
    @error('star_rating') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-3">
    <label for="destination_id">Destination / City</label>
    <select name="destination_id" id="destination_id" class="form-control @error('destination_id') is-invalid @enderror">
      <option value="">— None —</option>
      @foreach($destinations as $destination)
        <option value="{{ $destination->id }}" {{ (int) old('destination_id', $htl->destination_id ?? '') === $destination->id ? 'selected' : '' }}>{{ $destination->name }}</option>
      @endforeach
    </select>
    @error('destination_id') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-3">
    <label for="rating_score">Rating Score</label>
    <input type="number" step="0.1" min="0" max="10" name="rating_score" id="rating_score" class="form-control @error('rating_score') is-invalid @enderror"
      value="{{ old('rating_score', $htl->rating_score ?? '') }}" placeholder="e.g. 8.6">
    @error('rating_score') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-3">
    <label for="review_count">Reviews</label>
    <input type="number" min="0" name="review_count" id="review_count" class="form-control @error('review_count') is-invalid @enderror"
      value="{{ old('review_count', $htl->review_count ?? 0) }}" placeholder="e.g. 1240">
    @error('review_count') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="address">Address / Location</label>
    <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror"
      value="{{ old('address', $htl->address ?? '') }}" placeholder="e.g. Candolim Beach Road, North Goa">
    @error('address') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-3">
    <label for="latitude">Latitude</label>
    <input type="text" name="latitude" id="latitude" class="form-control @error('latitude') is-invalid @enderror"
      value="{{ old('latitude', $htl->latitude ?? '') }}" placeholder="e.g. 15.5185">
    @error('latitude') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-3">
    <label for="longitude">Longitude</label>
    <input type="text" name="longitude" id="longitude" class="form-control @error('longitude') is-invalid @enderror"
      value="{{ old('longitude', $htl->longitude ?? '') }}" placeholder="e.g. 73.7684">
    @error('longitude') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-3">
    <label for="check_in_time">Check-in Time</label>
    <input type="text" name="check_in_time" id="check_in_time" class="form-control @error('check_in_time') is-invalid @enderror"
      value="{{ old('check_in_time', $htl->check_in_time ?? '') }}" placeholder="e.g. 14:00">
    @error('check_in_time') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-3">
    <label for="check_out_time">Check-out Time</label>
    <input type="text" name="check_out_time" id="check_out_time" class="form-control @error('check_out_time') is-invalid @enderror"
      value="{{ old('check_out_time', $htl->check_out_time ?? '') }}" placeholder="e.g. 11:00">
    @error('check_out_time') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-6">
    <label for="property_rules">Property Rules <small class="text-muted">(one per line)</small></label>
    <textarea name="property_rules" id="property_rules" rows="2" class="form-control @error('property_rules') is-invalid @enderror"
      placeholder="Valid photo ID required at check-in.">{{ old('property_rules', $htl->property_rules ?? '') }}</textarea>
    @error('property_rules') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

<div class="form-group">
  <label for="description">Description</label>
  <textarea name="description" id="description" rows="4" class="form-control rich-text-editor @error('description') is-invalid @enderror"
    data-editor-height="200" placeholder="e.g. Nestled beside the Arabian Sea with sea-view rooms and an infinity pool.">{{ old('description', $htl->description ?? '') }}</textarea>
  @error('description') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label>Amenities</label>
  <div class="d-flex flex-wrap" style="gap:16px;">
    @php $selectedAmenityIds = old('amenity_ids', $htl ? $htl->amenities->pluck('id')->all() : []); @endphp
    @forelse($amenities as $amenity)
      <div class="custom-control custom-checkbox">
        <input type="checkbox" name="amenity_ids[]" value="{{ $amenity->id }}" class="custom-control-input" id="amenity_{{ $amenity->id }}"
          {{ in_array($amenity->id, $selectedAmenityIds) ? 'checked' : '' }}>
        <label class="custom-control-label" for="amenity_{{ $amenity->id }}"><i class="{{ $amenity->icon }}"></i> {{ $amenity->name }}</label>
      </div>
    @empty
      <span class="text-muted">No amenities available yet.</span>
    @endforelse
  </div>
</div>

<hr>
<h6><i class="fas fa-bed text-muted mr-1"></i> Hotel Room Types <small class="text-muted">(prices are per night; leave empty if this hotel has no bookable room types yet)</small></h6>
<div data-repeater id="hotelRoomTypesRepeater">
  <div data-repeater-rows>
    @foreach(($htl->roomTypes ?? collect()) as $i => $item)
      @include('admin.hotels.partials._room_type_row', ['index' => $i, 'item' => $item])
    @endforeach
  </div>
  <template data-repeater-template>
    @include('admin.hotels.partials._room_type_row', ['index' => '__INDEX__', 'item' => null])
  </template>
  <button type="button" class="btn btn-outline-primary btn-sm mt-1" data-repeater-add><i class="fas fa-plus mr-1"></i>Add Room Type</button>
</div>

<hr>
<div class="form-row">
  <div class="form-group col-md-6">
    <label for="cover_image">Cover Image <small class="text-muted">size: 800x500</small></label>
    <input type="file" name="cover_image" id="cover_image" class="form-control-file @error('cover_image') is-invalid @enderror" accept="image/*">
    @error('cover_image') <span class="text-danger d-block">{{ $message }}</span> @enderror
    @if($htl && $htl->cover_image)
      <div class="mt-2">
        <img src="{{ asset('storage/'.$htl->cover_image) }}" alt="{{ $htl->name }}" style="width:80px;height:80px;object-fit:cover;border-radius:6px;">
      </div>
    @endif
  </div>
  <div class="form-group col-md-6">
    <label for="gallery_images">Gallery Images <small class="text-muted">size: 800x500 each</small></label>
    <input type="file" name="gallery_images[]" id="gallery_images" class="form-control-file" accept="image/*" multiple>
    <small class="form-text text-muted">Uploading new gallery images replaces the existing gallery.</small>
    @if($htl && !empty($htl->gallery_images))
      <div class="mt-2 d-flex flex-wrap" style="gap:8px;">
        @foreach($htl->gallery_images as $image)
          <img src="{{ asset('storage/'.$image) }}" style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
        @endforeach
      </div>
    @endif
  </div>
</div>

<div class="form-row">
  <div class="form-group col-md-6">
    <label for="status">Status</label>
    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
      <option value="active" {{ old('status', $htl->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
      <option value="inactive" {{ old('status', $htl->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
  <div class="form-group col-md-6">
    <label for="sort_order">Sort Order</label>
    <input type="number" name="sort_order" id="sort_order" min="0" class="form-control @error('sort_order') is-invalid @enderror"
      value="{{ old('sort_order', $htl->sort_order ?? 0) }}">
    @error('sort_order') <span class="text-danger">{{ $message }}</span> @enderror
  </div>
</div>

@include('admin.partials._seo_fields', [
  'seo' => $htl,
  'seoUrl' => $htl && $htl->slug ? route('hotels.show', $htl->slug) : null,
  'seoPreviewFallback' => ($htl->name ?? 'Hotel').' – FareBuzzer',
])

<button type="submit" class="btn btn-primary">{{ $htl ? 'Update' : 'Create' }} Hotel</button>
<a href="{{ route('crm.hotels.index') }}" class="btn btn-secondary">Cancel</a>
