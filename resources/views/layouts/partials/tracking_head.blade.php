@php $__tracking = \App\Models\TrackingScript::forSite()->first(); @endphp
@if($__tracking?->header_enabled && $__tracking->header_script)
{!! $__tracking->header_script !!}
@endif
