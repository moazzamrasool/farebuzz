<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

@include('layouts.admin.head')
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
    @include('layouts.admin.topbar')
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
    @include('layouts.admin.sidebar')

  <!-- Content Wrapper. Contains page content -->
    @yield('content')
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  {{-- <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>Title</h5>
      <p>Sidebar content</p>
    </div>
  </aside> --}}
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
    @include('layouts.admin.footer')
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- Bootstrap 4 -->

<script src="{{asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('admin/dist/js/adminlte.min.js')}}"></script>
<!-- FareBuzzer CRM shared UI helpers (image upload widget + ajax modal forms) -->
<script src="{{asset('admin/custom.js')}}"></script>

  <script src="{{asset('admin/plugins/summernote/summernote-bs4.min.js')}}"></script>
  <!-- CKEditor 5 (self-hosted GPL build, no API key/account) -->
  <script src="{{asset('admin/plugins/ckeditor/ckeditor5.umd.js')}}"></script>
  <script>
  $(function () {
    // Summernote
    $('#summernote').summernote()

    // CodeMirror
    if (document.getElementById("codeMirrorDemo")) {
      CodeMirror.fromTextArea(document.getElementById("codeMirrorDemo"), {
        mode: "htmlmixed",
        theme: "monokai"
      });
    }
  })
</script>


</body>
</html>
