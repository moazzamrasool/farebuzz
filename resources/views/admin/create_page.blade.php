
@extends('layouts.admin.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container">
               <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                {!! isset($pages) ? '<h3 class="card-title">Edit Page</h3>' : '<h3 class="card-title">Create Page</h3>' !!}

              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form id="page_form">

                <div class="card-body">
                  <div class="form-group">
                    <label for="PageName">Page Name</label>
                    <input type="text" name="page_name" class="form-control" id="PageName" placeholder="Enter page name" value="{{ isset($pages) ? $pages->page_name : '' }}">
                  </div>
                  <div class="form-group">
                    <label for="Slug">Slug</label>
                    <input type="text" name="page_url" class="form-control" id="Slug" placeholder="Enter slug" value="{{ isset($pages) ? $pages->page_url : '' }}">
                  </div>
                  <div class="form-group">
                    <label for="Title">Title</label>
                    <input type="text" name="page_title" class="form-control" id="Title" placeholder="Enter title" value="{{ isset($pages) ? $pages->page_title : '' }}">
                  </div>
                  <div class="form-group">
                    <label for="Title">Page Type</label>
                    <select name="status" id="Status" class="form-control">

                        <option value="0" {{ (isset($pages) && $pages->status == 0) ? 'selected' : '' }}>Draft</option>
                        <option value="1" {{ (isset($pages) && $pages->status == 1) ? 'selected' : '' }}>Service</option>
                        <option value="2" {{ (isset($pages) && $pages->status == 2) ? 'selected' : '' }}>Page</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="description">Description</label>
                     <section class="content">
                        <div class="row">
                            <div class="col-md-12">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                <h3 class="card-title">
                                    Page Description
                                    <small>Simple and fast</small>
                                </h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                <textarea id="summernote" name="page_link">
                                    {!! isset($pages) ? $pages->page_link : 'Place <em>some</em> <u>text</u> <strong>here</strong>' !!}
                                </textarea>
                                </div>
                            </div>
                            </div>
                            <!-- /.col-->
                        </div>
                        <!-- ./row -->

                    <!-- ./row -->
                    </section>
                  </div>

                   <div class="form-group">
                    <label for="keyword">Keyword <small>(seo)</small></label>
                    <input type="text" name="page_keyword" class="form-control" id="keyword" placeholder="Enter keyword" value="{{ isset($pages) ? $pages->page_keyword : '' }}">
                  </div>
                  <div class="form-group">
                    <label for="description">Description <small>(seo)</small></label>
                    <input type="text" name="page_description" class="form-control" id="description" placeholder="Enter description" value="{{ isset($pages) ? $pages->page_desc : '' }}">
                  </div>

                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
            </div>
            <!-- /.card -->



          </div>
          <!--/.col (left) -->
          <!-- right column -->

          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
        </div>
    </div>
</div>


<!--Script Start Here-->
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#page_form").on("submit", function(e) {
        e.preventDefault();  // stop normal form submit

        let formData = new FormData(this);

        $.ajax({
            url: "{{route('admin.page.create', ['id' => isset($pages) ? $pages->id : null])}}",     // your server script
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            cache: false,
            success: function(response) {
                if(response.status === 'success'){
                    toastr.success(response.message);

                    setTimeout(() => {
                        location.href = "{{ route('admin.page') }}";
                    }, 5000);
                } else if(response.status === 'error'){
                    $.each(response.errors, function(key, value){
                        toastr.error(value);
                    });
                }
            },
            error: function(err) {
                toastr.error("Error:", err);
                toastr.error("Something went wrong!");
            }
        });

    });

</script>
<!--Script End Here-->

@endsection
