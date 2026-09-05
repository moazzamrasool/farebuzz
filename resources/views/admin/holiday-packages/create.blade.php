
@extends('layouts.admin.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Add Holiday Package</h3>
              </div>
              <div class="card-body">
                <form id="packageForm" action="{{ route('crm.holiday-packages.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                  @include('admin.holiday-packages._form')
                </form>
              </div>
            </div>
          </div>
        </div>
        </div>
    </div>
</div>
@endsection
