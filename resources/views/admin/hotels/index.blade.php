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
                <h3 class="card-title">Hotels</h3>
                <div class="card-tools">
                  @if($currentAdmin?->isAdmin() || $currentAdmin?->can('hotels.create'))
                    <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#hotelsBulkUploadModal"><i class="fa fa-file-excel"></i> Bulk Upload</button>
                    <a href="{{ route('crm.hotels.create') }}" class="btn btn-primary">+ Add New</a>
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
                      <th>Name</th>
                      <th>Stars</th>
                      <th>Address</th>
                      <th>Rating</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($hotels as $key=>$hotel)
                    <tr>
                      <td>{{ $hotels->firstItem() + $key }}</td>
                      <td>
                        @if($hotel->cover_image)
                          <img src="{{ asset('storage/'.$hotel->cover_image) }}" alt="{{ $hotel->name }}" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">
                        @else
                          —
                        @endif
                      </td>
                      <td>{{ $hotel->name }}</td>
                      <td>{{ $hotel->star_rating }}★</td>
                      <td>{{ $hotel->address ?? 'N/A' }}</td>
                      <td>{{ $hotel->rating_score ?? 'N/A' }}</td>
                      <td>
                        <form action="{{ route('crm.hotels.toggle-status', $hotel->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm {{ $hotel->status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                            {{ ucfirst($hotel->status) }}
                          </button>
                        </form>
                      </td>
                      <td>
                        @if($currentAdmin?->isAdmin() || $currentAdmin?->can('hotels.edit'))
                          <a href="{{ route('crm.hotels.edit', $hotel->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        @endif
                        @if($currentAdmin?->isAdmin() || $currentAdmin?->can('hotels.delete'))
                          <form action="{{ route('crm.hotels.destroy', $hotel->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this hotel?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                          </form>
                        @endif
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="8" class="text-center">No hotels found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                {{ $hotels->links() }}
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        </div>
    </div>
</div>

@if($currentAdmin?->isAdmin() || $currentAdmin?->can('hotels.create'))
  @include('admin.partials.bulk-upload-modal', ['module' => 'hotels', 'moduleLabel' => 'Hotels', 'modalId' => 'hotelsBulkUploadModal'])
@endif
@endsection
