
@extends('layouts.admin.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Edit Company</h3>
              </div>
              <div class="card-body">
                <form action="{{ route('crm.admins.update', $admin->id) }}" method="POST">
                  @method('PUT')
                  @include('admin.admins._form')
                </form>
              </div>
            </div>
          </div>
        </div>
        </div>
    </div>
</div>
@endsection
