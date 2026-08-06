{{-- Removable active-filter chips. $chips: assoc array of [query_key => display label]. --}}
@if(!empty($chips))
<div class="fb-filter-chips mb-3">
  @foreach($chips as $key => $label)
    <a href="{{ request()->fullUrlWithQuery([$key => null, 'page' => null]) }}" class="fb-chip">{{ $label }} <i class="bi bi-x"></i></a>
  @endforeach
  <a href="{{ request()->url() }}" class="fb-chip fb-chip-clear">Clear all</a>
</div>
@endif
