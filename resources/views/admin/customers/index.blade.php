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
                <h3 class="card-title">Customers</h3>
              </div>
              <div class="card-body">
                <form method="GET" class="row g-2 mb-3">
                  <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="{{ request('search') }}">
                  </div>
                  <div class="col-md-3">
                    <select name="verified" class="form-control">
                      <option value="">All verification statuses</option>
                      <option value="verified" {{ request('verified') === 'verified' ? 'selected' : '' }}>Verified</option>
                      <option value="unverified" {{ request('verified') === 'unverified' ? 'selected' : '' }}>Pending verification</option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <select name="status" class="form-control">
                      <option value="">All account statuses</option>
                      <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                      <option value="pending_verification" {{ request('status') === 'pending_verification' ? 'selected' : '' }}>Pending Verification</option>
                      <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                  </div>
                  <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                  </div>
                </form>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>Customer ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Registered</th>
                      <th>Verification</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($customers as $customer)
                    <tr>
                      <td><code>{{ \Illuminate\Support\Str::limit($customer->public_id, 8, '') }}</code></td>
                      <td>{{ $customer->name }}</td>
                      <td>{{ $customer->email }}</td>
                      <td>{{ $customer->created_at->format('d M Y') }}</td>
                      <td>
                        @if($customer->email_verified_at)
                          <span class="badge badge-success">Verified</span>
                        @else
                          <span class="badge badge-warning">Pending</span>
                        @endif
                      </td>
                      <td>
                        @if($customer->status === 'suspended')
                          <span class="badge badge-danger">Suspended</span>
                        @elseif($customer->status === 'active')
                          <span class="badge badge-success">Active</span>
                        @else
                          <span class="badge badge-secondary">Pending Verification</span>
                        @endif
                      </td>
                      <td>
                        <a href="{{ route('crm.customers.show', $customer) }}" class="btn btn-primary btn-sm">View</a>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="7" class="text-center">No customers found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                {{ $customers->links() }}
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        </div>
    </div>
</div>
@endsection
