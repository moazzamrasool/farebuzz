@php $item = $item ?? null; @endphp
<div class="form-row align-items-end mb-2 repeater-row-plain" data-repeater-row>
  <div class="form-group col-md-4">
    <label class="form-label-sm">Room Type Name</label>
    <input type="text" name="room_types[{{ $index }}][name]" class="form-control" value="{{ $item->name ?? '' }}" placeholder="e.g. Deluxe Room (Sea View)">
  </div>
  <div class="form-group col-md-3">
    <label class="form-label-sm">Original Price</label>
    <input type="number" step="0.01" min="0" name="room_types[{{ $index }}][price]" class="form-control" value="{{ $item->price ?? '' }}" placeholder="e.g. 21999">
  </div>
  <div class="form-group col-md-3">
    <label class="form-label-sm">Discounted Price</label>
    <input type="number" step="0.01" min="0" name="room_types[{{ $index }}][discounted_price]" class="form-control" value="{{ $item->discounted_price ?? '' }}" placeholder="e.g. 18999">
  </div>
  <div class="form-group col-md-2">
    <button type="button" class="btn btn-danger btn-sm btn-block" data-repeater-remove><i class="fas fa-times"></i> Remove</button>
  </div>
</div>
