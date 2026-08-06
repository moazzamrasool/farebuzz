<!DOCTYPE html>
<html lang="en">
@include('layouts.head')
<body>
@include('layouts.partials.tracking_body_open')

<!-- ══════════ NAVBAR ══════════ -->
@include('layouts.admin.header')

@yield('content')
<!-- ══════════ FOOTER ══════════ -->

@include('layouts.footer')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{asset('frontend/asset/js/custom.js')}}"></script>
<script src="{{asset('frontend/asset/js/autocomplete.js')}}"></script>
@stack('scripts')
@include('layouts.partials.tracking_footer')
</body>
</html>
