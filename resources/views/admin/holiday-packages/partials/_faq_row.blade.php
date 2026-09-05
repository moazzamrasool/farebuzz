@php $item = $item ?? null; @endphp
<div class="card mb-2" data-repeater-row>
  <div class="card-body">
    <input type="hidden" name="faqs[{{ $index }}][id]" class="pkg-row-id" value="{{ $item->id ?? '' }}">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <strong>Question <span data-repeater-index></span></strong>
      <button type="button" class="btn btn-danger btn-sm" data-repeater-remove>&times; Remove</button>
    </div>
    <div class="form-group">
      <label class="form-label-sm">Question</label>
      <input type="text" name="faqs[{{ $index }}][question]" class="form-control" value="{{ $item->question ?? '' }}" placeholder="e.g. Can I customise this package?">
    </div>
    <div class="form-group mb-0">
      <label class="form-label-sm">Answer</label>
      <textarea name="faqs[{{ $index }}][answer]" rows="3" class="form-control rich-text-editor" placeholder="e.g. Yes! All FareBuzzer packages are fully customizable...">{{ $item->answer ?? '' }}</textarea>
    </div>
  </div>
</div>
