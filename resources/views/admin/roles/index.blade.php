
@extends('layouts.admin.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">

            @if(session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
              <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Roles</h3>
                <div class="card-tools">
                  <a href="{{ route('crm.roles.create') }}" class="btn btn-primary">+ Add New</a>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>SN.</th>
                      <th>Name</th>
                      <th>Permissions</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($roles as $key=>$role)
                    <tr>
                      <td>{{ $roles->firstItem() + $key }}</td>
                      <td>{{ $role->name }}</td>
                      <td>{{ $role->permissions->count() }}</td>
                      <td>
                        <a href="{{ route('crm.roles.edit', $role->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('crm.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this role?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="4" class="text-center">No roles found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                {{ $roles->links() }}
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        </div>
    </div>
</div>
@endsection
