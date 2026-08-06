
@extends('layouts.admin.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Add Activity</h3>
              </div>
              <div class="card-body">
                <form action="{{ route('crm.activities.store') }}" method="POST" enctype="multipart/form-data">
                  @include('admin.activities._form')
                </form>
              </div>
            </div>
          </div>
        </div>
        </div>
    </div>
</div>
@endsection
