@php
  $rows = $items[$sectionKey] ?? collect();
@endphp
<div data-repeater id="repeater-{{ $sectionKey }}">
  <div data-repeater-rows>
    @foreach($rows as $i => $item)
      @include('admin.about-page._item_row', [
        'sectionKey' => $sectionKey, 'index' => $i, 'item' => $item,
        'titleLabel' => $titleLabel ?? 'Title', 'subtitleLabel' => $subtitleLabel ?? null,
        'showDescription' => $showDescription ?? false, 'descriptionLabel' => $descriptionLabel ?? null,
        'showLink' => $showLink ?? false, 'showImage' => $showImage ?? true,
      ])
    @endforeach
  </div>
  <template data-repeater-template>
    @include('admin.about-page._item_row', [
      'sectionKey' => $sectionKey, 'index' => '__INDEX__', 'item' => null,
      'titleLabel' => $titleLabel ?? 'Title', 'subtitleLabel' => $subtitleLabel ?? null,
      'showDescription' => $showDescription ?? false, 'descriptionLabel' => $descriptionLabel ?? null,
      'showLink' => $showLink ?? false, 'showImage' => $showImage ?? true,
    ])
  </template>
  <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add>+ {{ $addLabel ?? 'Add Item' }}</button>
</div>
