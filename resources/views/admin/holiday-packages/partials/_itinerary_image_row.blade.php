@php $img = $img ?? null; @endphp
<div class="itin-img-card" data-repeater-row style="width:150px;">
  <input type="hidden" name="itineraries[{{ $dayIndex }}][images][{{ $imgIndex }}][id]" value="{{ $img->id ?? '' }}">

  <div class="img-upload" data-multiple="false">
    <input type="file" name="itineraries[{{ $dayIndex }}][images][{{ $imgIndex }}][file]" class="img-upload-input d-none" accept="image/*">
    <div class="img-upload-box {{ $img && $img->image ? 'has-image' : '' }}" data-multiple="false" style="height:100px;">
      <div class="img-upload-placeholder">
        <i class="fas fa-cloud-upload-alt"></i>
        <span>Click or drag</span>
      </div>
      <div class="img-upload-preview-wrap">
        @if($img && $img->image)
          <div class="img-upload-thumb"><img src="{{ asset('storage/'.$img->image) }}" alt=""></div>
        @endif
      </div>
      @unless($img && $img->image)
        <button type="button" class="img-upload-remove" title="Clear selected file">&times;</button>
      @endunless
    </div>
  </div>

  <input type="text" name="itineraries[{{ $dayIndex }}][images][{{ $imgIndex }}][caption]" class="form-control form-control-sm mt-1" placeholder="Caption" value="{{ $img->caption ?? '' }}">
  <input type="text" name="itineraries[{{ $dayIndex }}][images][{{ $imgIndex }}][alt_text]" class="form-control form-control-sm mt-1" placeholder="Alt text" value="{{ $img->alt_text ?? '' }}">

  <button type="button" class="photo-remove-btn mt-1" title="Remove image" data-repeater-remove><i class="fas fa-trash-alt"></i></button>
</div>
