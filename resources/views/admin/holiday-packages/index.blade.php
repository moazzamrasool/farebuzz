
@extends('layouts.admin.app')

@section('content')
@php $currentAdmin = Auth::guard('admin')->user(); @endphp
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
                <h3 class="card-title">Holiday Packages</h3>
                <div class="card-tools">
                  @if($currentAdmin?->isAdmin() || $currentAdmin?->can('holiday-packages.create'))
                    <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#holidayPackagesBulkUploadModal"><i class="fa fa-file-excel"></i> Bulk Upload</button>
                    <a href="{{ route('crm.holiday-packages.create') }}" class="btn btn-primary">+ Add New</a>
                  @endif
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>SN.</th>
                      <th>Cover</th>
                      <th>Title</th>
                      <th>Categories</th>
                      <th>Destination</th>
                      <th>Duration</th>
                      <th>Price</th>
                      <th>Best Seller</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($holidayPackages as $key=>$package)
                    @php $coverPhoto = $package->photos->firstWhere('is_cover', true) ?? $package->photos->first(); @endphp
                    <tr>
                      <td>{{ $holidayPackages->firstItem() + $key }}</td>
                      <td>
                        @if($coverPhoto)
                          <img src="{{ asset('storage/'.$coverPhoto->path) }}" alt="{{ $package->title }}" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">
                        @else
                          —
                        @endif
                      </td>
                      <td>{{ $package->title }}</td>
                      <td>
                        @forelse($package->categories as $category)
                          <span class="badge" style="background:{{ $category->badge_color ?? '#6c757d' }};color:#fff;">{{ $category->name }}</span>
                        @empty
                          <span class="text-muted">N/A</span>
                        @endforelse
                      </td>
                      <td>{{ $package->destination->name ?? 'N/A' }}</td>
                      <td>{{ $package->nights }}N / {{ $package->days }}D</td>
                      <td>
                        @if($package->discounted_price)
                          <span class="text-muted" style="text-decoration:line-through;">{{ number_format($package->price, 2) }}</span>
                          <strong>{{ number_format($package->discounted_price, 2) }}</strong>
                        @else
                          {{ number_format($package->price, 2) }}
                        @endif
                      </td>
                      <td>{!! $package->is_best_seller ? '<span class="badge badge-info">Yes</span>' : '<span class="badge badge-secondary">No</span>' !!}</td>
                      <td>
                        <form action="{{ route('crm.holiday-packages.toggle-status', $package->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm {{ $package->status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                            {{ ucfirst($package->status) }}
                          </button>
                        </form>
                      </td>
                      <td>
                        @if($currentAdmin?->isAdmin() || $currentAdmin?->can('holiday-packages.edit'))
                          <a href="{{ route('crm.holiday-packages.edit', $package->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        @endif
                        @if($currentAdmin?->isAdmin() || $currentAdmin?->can('holiday-packages.delete'))
                          <form action="{{ route('crm.holiday-packages.destroy', $package->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this holiday package?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                          </form>
                        @endif
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="10" class="text-center">No holiday packages found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                {{ $holidayPackages->links() }}
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        </div>
    </div>
</div>

@if($currentAdmin?->isAdmin() || $currentAdmin?->can('holiday-packages.create'))
  @include('admin.partials.bulk-upload-modal', ['module' => 'holiday-packages', 'moduleLabel' => 'Holiday Packages', 'modalId' => 'holidayPackagesBulkUploadModal', 'accept' => '.xlsx,.xls'])
@endif
@endsection
