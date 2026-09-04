@extends('layouts.admin.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">

            @if($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Edit Blog Post</h3>
              </div>
              <div class="card-body">
                <form id="blogForm" action="{{ route('crm.blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
                  @method('PUT')
                  @include('admin.blogs._form')
                </form>
              </div>
            </div>
          </div>
        </div>
        </div>
    </div>
</div>
@endsection
