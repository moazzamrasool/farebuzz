@php $__tracking = \App\Models\TrackingScript::forSite()->first(); @endphp
@if($__tracking?->footer_enabled && $__tracking->footer_script)
{!! $__tracking->footer_script !!}
@endif
