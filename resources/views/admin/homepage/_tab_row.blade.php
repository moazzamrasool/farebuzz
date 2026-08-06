@php $tab = $tab ?? null; @endphp
<div class="form-row align-items-center mb-2 repeater-row-plain" data-repeater-row>
  <input type="hidden" name="tabs[{{ $index }}][key]" value="{{ $tab['key'] ?? '' }}">
  <div class="form-group col-md-10 mb-2">
    <input type="text" name="tabs[{{ $index }}][label]" class="form-control" value="{{ $tab['label'] ?? '' }}" placeholder="e.g. Beach">
  </div>
  <div class="form-group col-md-2 mb-2">
    <button type="button" class="btn btn-danger btn-sm btn-block" data-repeater-remove><i class="fa fa-times"></i></button>
  </div>
</div>
