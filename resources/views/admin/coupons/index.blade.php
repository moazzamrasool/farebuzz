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
                <h3 class="card-title">Coupons / Offers</h3>
                <div class="card-tools">
                  @if($currentAdmin?->isAdmin() || $currentAdmin?->can('coupons.create'))
                    <a href="{{ route('crm.coupons.create') }}" class="btn btn-primary">+ Add New</a>
                  @endif
                </div>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>SN.</th>
                      <th>Code</th>
                      <th>Title</th>
                      <th>Discount</th>
                      <th>Valid</th>
                      <th>Usage</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($coupons as $key=>$coupon)
                    <tr>
                      <td>{{ $coupons->firstItem() + $key }}</td>
                      <td><code>{{ $coupon->code }}</code></td>
                      <td>{{ $coupon->title }}</td>
                      <td>
                        @if($coupon->discount_type === 'percentage')
                          {{ rtrim(rtrim(number_format($coupon->discount_value, 2), '0'), '.') }}%
                          @if($coupon->max_discount_amount) <small class="text-muted">(cap ₹{{ number_format($coupon->max_discount_amount, 0) }})</small> @endif
                        @else
                          ₹{{ number_format($coupon->discount_value, 2) }}
                        @endif
                      </td>
                      <td>{{ $coupon->valid_from->format('d M Y') }} – {{ $coupon->valid_to->format('d M Y') }}</td>
                      <td>{{ $coupon->usages_count }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}</td>
                      <td>
                        <form action="{{ route('crm.coupons.toggle-status', $coupon->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm {{ $coupon->status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                            {{ ucfirst($coupon->status) }}
                          </button>
                        </form>
                      </td>
                      <td>
                        @if($currentAdmin?->isAdmin() || $currentAdmin?->can('coupons.view'))
                          <a href="{{ route('crm.coupons.show', $coupon->id) }}" class="btn btn-info btn-sm">View</a>
                        @endif
                        @if($currentAdmin?->isAdmin() || $currentAdmin?->can('coupons.edit'))
                          <a href="{{ route('crm.coupons.edit', $coupon->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        @endif
                        @if($currentAdmin?->isAdmin() || $currentAdmin?->can('coupons.delete'))
                          <form action="{{ route('crm.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this coupon?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                          </form>
                        @endif
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="8" class="text-center">No coupons found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <div class="card-footer">
                {{ $coupons->links() }}
              </div>
            </div>
          </div>
        </div>
        </div>
    </div>
</div>
@endsection
