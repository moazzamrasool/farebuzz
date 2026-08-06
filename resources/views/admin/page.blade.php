
@extends('layouts.admin.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Responsive Hover Table</h3>

                <div class="card-tools">
                  <div class="input-group input-group-sm" style="width: 150px;">
                    {{-- <input type="text" name="table_search" class="form-control float-right" placeholder="Search"> --}}

                    <div class="input-group-append">
                      {{-- <button type="submit" class="btn btn-default">
                        <i class="fas fa-search"></i>
                      </button> --}}
                      <a href="{{ route('admin.page.create') }}" class="btn btn-primary">+Add New</a>
                    </div>
                </div>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>SN.</th>
                      <th>Name</th>
                      <th>URL</th>
                      <th>Title</th>
                      <th>Page Type</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($pages as $key=>$page)
                    <tr>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ $page->page_name }}</td>
                      <td>{{ $page->page_url }}</td>
                      <td>{{ $page->page_title }}</td>
                      <td>
                        @if($page->status == 0)
                            <span class="tag tag-warning">Draft</span>
                        @elseif($page->status == 1)
                            <span class="tag tag-info">Service</span>
                        @elseif($page->status == 2)
                            <span class="tag tag-primary">Page</span>
                        @endif

                      </td>
                      <td>
                        <a href="{{ route('admin.page.edit', $page->id) }}">Edit</a>
                    </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="6" class="text-center">No pages found.</td>
                    </tr>
                    @endforelse


                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
        </div>
    </div>
</div>


<!--Script Start Here-->
<script>
    $("#survey-form").on("submit", function(e) {
        e.preventDefault();  // stop normal form submit

        var formData = new FormData(this);

        $.ajax({
            url: "/",     // your server script
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            cache: false,
            success: function(response) {

                if(response.status === 'success'){
                    toastr.success(response.message);
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
