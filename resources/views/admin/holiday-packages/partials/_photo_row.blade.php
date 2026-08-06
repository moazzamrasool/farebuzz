@php $item = $item ?? null; @endphp
<div class="photo-card" data-repeater-row>
  <input type="hidden" name="photos[{{ $index }}][id]" value="{{ $item->id ?? '' }}">

  @if($item && $item->is_cover)
    <span class="photo-cover-badge">Cover</span>
  @endif

  <div class="img-upload" data-multiple="false">
    <input type="file" name="photos[{{ $index }}][file]" class="img-upload-input d-none" accept="image/*">
    <div class="img-upload-box {{ $item && $item->path ? 'has-image' : '' }}" data-multiple="false">
      <div class="img-upload-placeholder">
        <i class="fas fa-cloud-upload-alt"></i>
        <span>Click or drag</span>
      </div>
      <div class="img-upload-preview-wrap">
        @if($item && $item->path)
          <div class="img-upload-thumb"><img src="{{ asset('storage/'.$item->path) }}" alt=""></div>
        @endif
      </div>
      @unless($item && $item->path)
        <button type="button" class="img-upload-remove" title="Clear selected file">&times;</button>
      @endunless
    </div>
  </div>
  @if($item && $item->path)
    <small class="text-muted d-block mt-1">Click to replace this photo.</small>
  @endif

  <div class="photo-card-footer">
    <label class="photo-cover-pill mb-0">
      <input type="checkbox" name="photos[{{ $index }}][is_cover]" value="1" {{ ($item->is_cover ?? false) ? 'checked' : '' }}>
      Set as cover
    </label>
    <button type="button" class="photo-remove-btn" title="Remove photo" data-repeater-remove><i class="fas fa-trash-alt"></i></button>
  </div>
</div>
