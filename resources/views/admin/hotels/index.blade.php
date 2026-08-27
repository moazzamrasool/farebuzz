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
              <div class="card-body border-bottom">
                <form method="GET" action="{{ route('crm.hotels.index') }}" class="form-row align-items-end">
                  <div class="col-md-3 form-group mb-2">
                    <label class="small text-muted mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or address" class="form-control form-control-sm">
                  </div>
                  <div class="col-md-2 form-group mb-2">
                    <label class="small text-muted mb-1">Destination</label>
                    <select name="destination_id" class="form-control form-control-sm">
                      <option value="">All Destinations</option>
                      @foreach($destinations as $destination)
                        <option value="{{ $destination->id }}" @selected((string) request('destination_id') === (string) $destination->id)>{{ $destination->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-1 form-group mb-2">
                    <label class="small text-muted mb-1">Stars</label>
                    <select name="star_rating" class="form-control form-control-sm">
                      <option value="">Any</option>
                      @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" @selected((string) request('star_rating') === (string) $i)>{{ $i }}★</option>
                      @endfor
                    </select>
                  </div>
                  <div class="col-md-2 form-group mb-2">
                    <label class="small text-muted mb-1">Status</label>
                    <select name="status" class="form-control form-control-sm">
                      <option value="">All Statuses</option>
                      <option value="active" @selected(request('status') === 'active')>Active</option>
                      <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                  </div>
                  <div class="col-md-1 form-group mb-2">
                    <label class="small text-muted mb-1">Min Price</label>
                    <input type="number" name="price_min" value="{{ request('price_min') }}" min="0" class="form-control form-control-sm">
                  </div>
                  <div class="col-md-1 form-group mb-2">
                    <label class="small text-muted mb-1">Max Price</label>
                    <input type="number" name="price_max" value="{{ request('price_max') }}" min="0" class="form-control form-control-sm">
                  </div>
                  <div class="col-md-2 form-group mb-2">
                    <label class="small text-muted mb-1">Sort By</label>
                    <select name="sort" class="form-control form-control-sm">
                      <option value="" @selected(!request('sort'))>Recently Added</option>
                      <option value="name_asc" @selected(request('sort') === 'name_asc')>Name A-Z</option>
                      <option value="star_rating" @selected(request('sort') === 'star_rating')>Star Rating</option>
                      <option value="price_low" @selected(request('sort') === 'price_low')>Price: Low to High</option>
                    </select>
                  </div>
                  <div class="col-12 mt-1">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route('crm.hotels.index') }}" class="btn btn-secondary btn-sm">Clear</a>
                  </div>
                </form>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>SN.</th>
                      <th>Cover</th>
                      <th>Name</th>
                      <th>Destination</th>
                      <th>Stars</th>
                      <th>Address</th>
                      <th>Rooms</th>
                      <th>From Price</th>
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
                      <td>{{ $hotel->destination?->name ?? 'N/A' }}</td>
                      <td>{{ $hotel->star_rating }}★</td>
                      <td>{{ $hotel->address ?? 'N/A' }}</td>
                      <td>{{ $hotel->room_types_count }}</td>
                      <td>{{ $hotel->min_price !== null ? '₹'.number_format($hotel->min_price) : 'N/A' }}</td>
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
                      <td colspan="11" class="text-center">No hotels found matching your filters.</td>
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
