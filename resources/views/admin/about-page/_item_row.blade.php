@php
  // $sectionKey, $index, $item, $titleLabel, $subtitleLabel, $showDescription, $showLink, $showImage passed in
  $item = $item ?? null;
@endphp
<div class="card mb-2" data-repeater-row>
  <div class="card-body">
    <input type="hidden" name="items[{{ $sectionKey }}][{{ $index }}][id]" value="{{ $item->id ?? '' }}">
    <input type="hidden" name="items[{{ $sectionKey }}][{{ $index }}][image_existing]" value="{{ $item && $item->image ? 1 : '' }}">

    <div class="d-flex justify-content-between align-items-center mb-2">
      <strong>{{ $titleLabel ?? 'Item' }} <span data-repeater-index></span></strong>
      <div>
        <label class="mr-3 mb-0">
          <input type="checkbox" name="items[{{ $sectionKey }}][{{ $index }}][status]" value="1" {{ !$item || $item->status === 'active' ? 'checked' : '' }}>
          Active
        </label>
        <button type="button" class="btn btn-danger btn-sm" data-repeater-remove>&times; Remove</button>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group col-md-6">
        <label class="form-label-sm">{{ $titleLabel ?? 'Title' }}</label>
        <input type="text" name="items[{{ $sectionKey }}][{{ $index }}][title]" class="form-control" value="{{ $item->title ?? '' }}">
      </div>
      @if($subtitleLabel ?? null)
        <div class="form-group col-md-6">
          <label class="form-label-sm">{{ $subtitleLabel }}</label>
          <input type="text" name="items[{{ $sectionKey }}][{{ $index }}][subtitle]" class="form-control" value="{{ $item->subtitle ?? '' }}">
        </div>
      @endif
    </div>

    @if($showDescription ?? false)
      <div class="form-group">
        <label class="form-label-sm">{{ $descriptionLabel ?? 'Description' }}</label>
        <textarea name="items[{{ $sectionKey }}][{{ $index }}][description]" rows="2" class="form-control">{{ $item->description ?? '' }}</textarea>
      </div>
    @endif

    @if($showLink ?? false)
      <div class="form-group">
        <label class="form-label-sm">Link</label>
        <input type="text" name="items[{{ $sectionKey }}][{{ $index }}][link]" class="form-control" value="{{ $item->link ?? '' }}" placeholder="https://">
      </div>
    @endif

    @if($showImage ?? true)
      <div class="form-group">
        <label class="form-label-sm">Image</label>
        <div class="img-upload" data-multiple="false">
          <input type="file" name="items[{{ $sectionKey }}][{{ $index }}][image]" class="img-upload-input d-none" accept="image/*">
          <div class="img-upload-box {{ $item && $item->image ? 'has-image' : '' }}" data-multiple="false">
            <div class="img-upload-placeholder">
              <i class="fa fa-cloud-upload-alt"></i>
              <span>Click or drag an image here</span>
            </div>
            <div class="img-upload-preview-wrap">
              @if($item && $item->image)
                <div class="img-upload-thumb"><img src="{{ \App\Support\MediaUrl::resolve($item->image) }}" alt=""></div>
              @endif
            </div>
            @unless($item && $item->image)
              <button type="button" class="img-upload-remove" title="Clear selected file">&times;</button>
            @endunless
          </div>
        </div>
        @if($item && $item->image)
          <small class="text-muted d-block mt-1">Click to replace this image.</small>
        @endif
      </div>
    @endif
  </div>
</div>
