<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{asset('admin/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('admin/dist/css/adminlte.min.css')}}">
  {{-- toastr --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

  <link rel="stylesheet" href="{{asset('admin/custom.css')}}">
  <!-- CKEditor 5 (self-hosted GPL build) — ckeditor5.css is the editor "chrome" (toolbar,
       balloons); ckeditor5-content.css is the separate stylesheet CKEditor 5 requires for
       the *editable area itself* (image floats, paragraph spacing, tables). Without it,
       the editing area has no rules for .image-style-align-left etc. and layout breaks
       inside the editor, even though the same HTML looks fine once saved and rendered
       through the frontend (which already had this file linked). -->
  <link rel="stylesheet" href="{{asset('admin/plugins/ckeditor/ckeditor5.css')}}">
  <link rel="stylesheet" href="{{asset('admin/plugins/ckeditor/ckeditor5-content.css')}}">
  <script>
    window.FB_IMAGE_UPLOAD_URL = "{{ route('crm.editor.upload-image') }}";
  </script>
  <!-- jQuery -->
<script src="{{asset('admin/plugins/jquery/jquery.min.js')}}"></script>
<!-- toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <!-- summernote -->
  <link rel="stylesheet" href="{{asset('admin/plugins/summernote/summernote-bs4.min.css')}}">

</head>
