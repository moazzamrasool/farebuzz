@extends('layouts.admin.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">New Message Template</h3>
                <div class="card-tools">
                  <a href="{{ route('crm.message-templates.index') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>
              </div>
              <form action="{{ route('crm.message-templates.store') }}" method="POST">
                @csrf
                <div class="card-body">
                  @include('admin.message-templates._form')
                </div>
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Create Template</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        </div>
    </div>
</div>
@endsection
