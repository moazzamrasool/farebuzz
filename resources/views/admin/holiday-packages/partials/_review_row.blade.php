@php $item = $item ?? null; @endphp
<div class="card mb-2" data-repeater-row>
  <div class="card-body">
    <input type="hidden" name="reviews[{{ $index }}][id]" value="{{ $item->id ?? '' }}">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <strong>Review <span data-repeater-index></span></strong>
      <button type="button" class="btn btn-danger btn-sm" data-repeater-remove>&times; Remove</button>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label class="form-label-sm">Reviewer Name</label>
        <input type="text" name="reviews[{{ $index }}][reviewer_name]" class="form-control" value="{{ $item->reviewer_name ?? '' }}" placeholder="e.g. Rahul Sharma">
      </div>
      <div class="form-group col-md-3">
        <label class="form-label-sm">Overall Rating (0-5)</label>
        <input type="number" step="0.1" min="0" max="5" name="reviews[{{ $index }}][rating]" class="form-control" value="{{ $item->rating ?? '' }}" placeholder="e.g. 4.8">
      </div>
      <div class="form-group col-md-3">
        <label class="form-label-sm">Review Date</label>
        <input type="date" name="reviews[{{ $index }}][review_date]" class="form-control" value="{{ $item?->review_date?->format('Y-m-d') ?? '' }}">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-3">
        <label class="form-label-sm">Hotels</label>
        <input type="number" step="0.1" min="0" max="5" name="reviews[{{ $index }}][hotels_rating]" class="form-control" value="{{ $item->hotels_rating ?? '' }}" placeholder="e.g. 4.6">
      </div>
      <div class="form-group col-md-3">
        <label class="form-label-sm">Sightseeing</label>
        <input type="number" step="0.1" min="0" max="5" name="reviews[{{ $index }}][sightseeing_rating]" class="form-control" value="{{ $item->sightseeing_rating ?? '' }}" placeholder="e.g. 4.8">
      </div>
      <div class="form-group col-md-3">
        <label class="form-label-sm">Food</label>
        <input type="number" step="0.1" min="0" max="5" name="reviews[{{ $index }}][food_rating]" class="form-control" value="{{ $item->food_rating ?? '' }}" placeholder="e.g. 4.5">
      </div>
      <div class="form-group col-md-3">
        <label class="form-label-sm">Value</label>
        <input type="number" step="0.1" min="0" max="5" name="reviews[{{ $index }}][value_rating]" class="form-control" value="{{ $item->value_rating ?? '' }}" placeholder="e.g. 4.9">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label-sm">Comment</label>
      <textarea name="reviews[{{ $index }}][comment]" rows="2" class="form-control" placeholder="e.g. Absolutely loved the package! The hotel was stunning and transfers were always on time.">{{ $item->comment ?? '' }}</textarea>
    </div>
    <div class="custom-control custom-checkbox mb-0">
      <input type="checkbox" name="reviews[{{ $index }}][verified]" value="1" class="custom-control-input" id="verified_{{ $index }}" {{ ($item->verified ?? true) ? 'checked' : '' }}>
      <label class="custom-control-label" for="verified_{{ $index }}">Verified Booking</label>
    </div>
  </div>
</div>
