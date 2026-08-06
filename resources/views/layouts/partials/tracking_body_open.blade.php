@php $__tracking = \App\Models\TrackingScript::forSite()->first(); @endphp
@if($__tracking?->body_enabled && $__tracking->body_script)
{!! $__tracking->body_script !!}
@endif
