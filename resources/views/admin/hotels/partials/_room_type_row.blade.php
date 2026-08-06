@php $item = $item ?? null; @endphp
<div class="form-row align-items-end mb-2 repeater-row-plain border-bottom pb-2" data-repeater-row>
  <input type="hidden" name="room_types[{{ $index }}][id]" value="{{ $item->id ?? '' }}">

  <div class="form-group col-md-4">
    <label class="form-label-sm">Room Type Name</label>
    <input type="text" name="room_types[{{ $index }}][name]" class="form-control" value="{{ $item->name ?? '' }}" placeholder="e.g. Deluxe Room (Sea View)">
  </div>
  <div class="form-group col-md-2">
    <label class="form-label-sm">Original Price / Night</label>
    <input type="number" step="0.01" min="0" name="room_types[{{ $index }}][price]" class="form-control" value="{{ $item->price ?? '' }}" placeholder="e.g. 6999">
  </div>
  <div class="form-group col-md-2">
    <label class="form-label-sm">Discounted Price / Night</label>
    <input type="number" step="0.01" min="0" name="room_types[{{ $index }}][discounted_price]" class="form-control" value="{{ $item->discounted_price ?? '' }}" placeholder="e.g. 5999">
  </div>
  <div class="form-group col-md-2">
    <label class="form-label-sm">Bed Type</label>
    <input type="text" name="room_types[{{ $index }}][bed_type]" class="form-control" value="{{ $item->bed_type ?? '' }}" placeholder="e.g. King Bed">
  </div>
  <div class="form-group col-md-1">
    <label class="form-label-sm">Size (sqft)</label>
    <input type="number" min="0" name="room_types[{{ $index }}][size_sqft]" class="form-control" value="{{ $item->size_sqft ?? '' }}">
  </div>
  <div class="form-group col-md-1">
    <button type="button" class="btn btn-danger btn-sm btn-block" data-repeater-remove><i class="fas fa-times"></i></button>
  </div>

  <div class="form-group col-md-2">
    <label class="form-label-sm">Adults</label>
    <input type="number" min="1" name="room_types[{{ $index }}][occupancy_adults]" class="form-control" value="{{ $item->occupancy_adults ?? 2 }}">
  </div>
  <div class="form-group col-md-2">
    <label class="form-label-sm">Children</label>
    <input type="number" min="0" name="room_types[{{ $index }}][occupancy_children]" class="form-control" value="{{ $item->occupancy_children ?? 0 }}">
  </div>
  <div class="form-group col-md-3">
    <label class="form-label-sm">Meal Plan</label>
    <select name="room_types[{{ $index }}][meal_plan]" class="form-control">
      @php $mealPlan = $item->meal_plan ?? 'room_only'; @endphp
      <option value="room_only" {{ $mealPlan === 'room_only' ? 'selected' : '' }}>Room Only</option>
      <option value="breakfast" {{ $mealPlan === 'breakfast' ? 'selected' : '' }}>Breakfast Included</option>
      <option value="breakfast_dinner" {{ $mealPlan === 'breakfast_dinner' ? 'selected' : '' }}>Breakfast &amp; Dinner Included</option>
    </select>
  </div>
  <div class="form-group col-md-2 d-flex align-items-center">
    <div class="custom-control custom-checkbox mt-4">
      <input type="checkbox" name="room_types[{{ $index }}][refundable]" value="1" class="custom-control-input" id="room_refundable_{{ $index }}" {{ ($item->refundable ?? true) ? 'checked' : '' }}>
      <label class="custom-control-label" for="room_refundable_{{ $index }}">Refundable</label>
    </div>
  </div>
  <div class="form-group col-md-3">
    <label class="form-label-sm">Room Images</label>
    <input type="file" name="room_types[{{ $index }}][images][]" class="form-control-file" accept="image/*" multiple>
    @if($item && $item->images)
      <div class="d-flex flex-wrap mt-1" style="gap:4px;">
        @foreach($item->images as $image)
          <img src="{{ asset('storage/'.$image) }}" alt="" style="width:44px;height:44px;object-fit:cover;border-radius:4px;">
        @endforeach
      </div>
      <small class="text-muted d-block">Choosing new files replaces these images.</small>
    @endif
  </div>
</div>
