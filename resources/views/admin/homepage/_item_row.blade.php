@php
  $item = $item ?? null;
  $fields = $config['fields'];
  $metaFields = $config['meta_fields'];
  $group = $config['group'];

  $groupOptions = match ($group['type'] ?? null) {
      'fixed' => $group['options'],
      'tabs' => collect($section->extra['tabs'] ?? [])->pluck('label', 'key')->all(),
      default => null,
  };
@endphp
<div class="card mb-2" data-repeater-row>
  <div class="card-body">
    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id ?? '' }}">
    <input type="hidden" name="items[{{ $index }}][image_existing]" value="{{ $item && $item->image ? 1 : '' }}">

    <div class="d-flex justify-content-between align-items-center mb-2">
      <strong>Card <span data-repeater-index></span></strong>
      <div>
        <label class="mr-3 mb-0">
          <input type="checkbox" name="items[{{ $index }}][status]" value="1" {{ !$item || $item->status === 'active' ? 'checked' : '' }}>
          Active
        </label>
        <button type="button" class="btn btn-danger btn-sm" data-repeater-remove>&times; Remove</button>
      </div>
    </div>

    <div class="form-row">
      @if($groupOptions !== null)
        <div class="form-group col-md-3">
          <label class="form-label-sm">{{ ($group['type'] ?? null) === 'tabs' ? 'Tab' : 'Group' }}</label>
          <select name="items[{{ $index }}][group_key]" class="form-control">
            @foreach($groupOptions as $optKey => $optLabel)
              <option value="{{ $optKey }}" {{ ($item->group_key ?? '') === $optKey ? 'selected' : '' }}>{{ $optLabel }}</option>
            @endforeach
          </select>
        </div>
      @endif

      @if(in_array('title', $fields))
        <div class="form-group col-md-{{ $groupOptions !== null ? 5 : 6 }}">
          <label class="form-label-sm">Title</label>
          <input type="text" name="items[{{ $index }}][title]" class="form-control" value="{{ $item->title ?? '' }}">
        </div>
      @endif

      @if(in_array('subtitle', $fields))
        <div class="form-group col-md-{{ $groupOptions !== null ? 4 : 6 }}">
          <label class="form-label-sm">Subtitle</label>
          <input type="text" name="items[{{ $index }}][subtitle]" class="form-control" value="{{ $item->subtitle ?? '' }}">
        </div>
      @endif
    </div>

    @if(in_array('description', $fields))
      <div class="form-group">
        <label class="form-label-sm">Description</label>
        <textarea name="items[{{ $index }}][description]" rows="2" class="form-control">{{ $item->description ?? '' }}</textarea>
      </div>
    @endif

    @if($section->key === 'offers')
      <div class="form-group">
        <label class="form-label-sm">Linked Coupon <small class="text-muted">(optional — shows the code as a badge on this card; leave blank for a plain marketing card)</small></label>
        <select name="items[{{ $index }}][coupon_id]" class="form-control">
          <option value="">— None —</option>
          @foreach(($coupons ?? []) as $couponOption)
            <option value="{{ $couponOption->id }}" {{ ($item->coupon_id ?? '') == $couponOption->id ? 'selected' : '' }}>{{ $couponOption->code }} — {{ $couponOption->title }}</option>
          @endforeach
        </select>
      </div>
    @endif

    <div class="form-row">
      @if(in_array('label', $fields))
        <div class="form-group col-md-3">
          <label class="form-label-sm">Tag / Label</label>
          <input type="text" name="items[{{ $index }}][label]" class="form-control" value="{{ $item->label ?? '' }}">
        </div>
      @endif

      @if(in_array('link', $fields))
        <div class="form-group col-md-{{ in_array('button_label', $fields) ? 5 : 9 }}">
          <label class="form-label-sm">Link</label>
          <input type="text" name="items[{{ $index }}][link]" class="form-control" value="{{ $item->link ?? '' }}" placeholder="https:// or a route path">
        </div>
      @endif

      @if(in_array('button_label', $fields))
        <div class="form-group col-md-4">
          <label class="form-label-sm">Button Label</label>
          <input type="text" name="items[{{ $index }}][button_label]" class="form-control" value="{{ $item->button_label ?? '' }}">
        </div>
      @endif
    </div>

    @if(array_intersect(['price', 'old_price', 'rating', 'review_count'], $fields) || !empty($metaFields))
      <div class="form-row">
        @if(in_array('old_price', $fields))
          <div class="form-group col-md-2">
            <label class="form-label-sm">Old Price</label>
            <input type="number" step="0.01" name="items[{{ $index }}][old_price]" class="form-control" value="{{ $item->old_price ?? '' }}">
          </div>
        @endif
        @if(in_array('price', $fields))
          <div class="form-group col-md-2">
            <label class="form-label-sm">Price</label>
            <input type="number" step="0.01" name="items[{{ $index }}][price]" class="form-control" value="{{ $item->price ?? '' }}">
          </div>
        @endif
        @if(in_array('rating', $fields))
          <div class="form-group col-md-2">
            <label class="form-label-sm">Rating</label>
            <input type="number" step="0.1" min="0" max="10" name="items[{{ $index }}][rating]" class="form-control" value="{{ $item->rating ?? '' }}">
          </div>
        @endif
        @if(in_array('review_count', $fields))
          <div class="form-group col-md-2">
            <label class="form-label-sm">Review Count</label>
            <input type="number" name="items[{{ $index }}][review_count]" class="form-control" value="{{ $item->review_count ?? '' }}">
          </div>
        @endif
        @foreach($metaFields as $metaField)
          <div class="form-group col-md-2">
            <label class="form-label-sm">{{ ucwords(str_replace('_', ' ', $metaField)) }}</label>
            <input type="text" name="items[{{ $index }}][meta][{{ $metaField }}]" class="form-control" value="{{ $item->meta[$metaField] ?? '' }}">
          </div>
        @endforeach
      </div>
    @endif

    @if(in_array('image', $fields))
      <div class="form-group">
        <label class="form-label-sm">Image</label>
        <div class="img-upload" data-multiple="false">
          <input type="file" name="items[{{ $index }}][image]" class="img-upload-input d-none" accept="image/*">
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

    @foreach($config['meta_image_fields'] ?? [] as $metaImageField)
      @php $metaImageValue = $item->meta[$metaImageField] ?? null; @endphp
      <div class="form-group">
        <label class="form-label-sm">{{ ucwords(str_replace('_', ' ', $metaImageField)) }}</label>
        <div class="img-upload" data-multiple="false">
          <input type="file" name="items[{{ $index }}][meta][{{ $metaImageField }}]" class="img-upload-input d-none" accept="image/*">
          <div class="img-upload-box {{ $metaImageValue ? 'has-image' : '' }}" data-multiple="false">
            <div class="img-upload-placeholder">
              <i class="fa fa-cloud-upload-alt"></i>
              <span>Click or drag an image here</span>
            </div>
            <div class="img-upload-preview-wrap">
              @if($metaImageValue)
                <div class="img-upload-thumb"><img src="{{ \App\Support\MediaUrl::resolve($metaImageValue) }}" alt=""></div>
              @endif
            </div>
            @unless($metaImageValue)
              <button type="button" class="img-upload-remove" title="Clear selected file">&times;</button>
            @endunless
          </div>
        </div>
        @if($metaImageValue)
          <small class="text-muted d-block mt-1">Click to replace this image.</small>
        @endif
      </div>
    @endforeach
  </div>
</div>
