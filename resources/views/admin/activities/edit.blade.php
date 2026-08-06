
@extends('layouts.admin.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Edit Activity</h3>
              </div>
              <div class="card-body">
                <form action="{{ route('crm.activities.update', $activity->id) }}" method="POST" enctype="multipart/form-data">
                  @method('PUT')
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
