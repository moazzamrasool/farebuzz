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

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Bookings</h3>
                <div class="card-tools">
                  <div class="btn-group btn-group-sm">
                    <a href="{{ route('crm.bookings.index') }}" class="btn {{ !$bookingType ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
                    <a href="{{ route('crm.bookings.index', ['type' => 'package']) }}" class="btn {{ $bookingType === 'package' ? 'btn-primary' : 'btn-outline-primary' }}">Packages</a>
                    <a href="{{ route('crm.bookings.index', ['type' => 'hotel']) }}" class="btn {{ $bookingType === 'hotel' ? 'btn-primary' : 'btn-outline-primary' }}">Hotels</a>
                  </div>
                </div>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>SN.</th>
                      <th>Reference</th>
                      <th>Type</th>
                      <th>Package / Hotel</th>
                      <th>Traveller</th>
                      <th>Date</th>
                      <th>Total</th>
                      <th>Status</th>
                      <th>Payment</th>
                      <th>Booked At</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($bookings as $key=>$booking)
                    <tr>
                      <td>{{ $bookings->firstItem() + $key }}</td>
                      <td><code>{{ $booking->booking_reference }}</code></td>
                      <td>
                        @if($booking->booking_type === 'hotel')
                          <span class="badge badge-info">Hotel</span>
                        @else
                          <span class="badge badge-primary">Package</span>
                        @endif
                      </td>
                      <td>{{ $booking->package_title }}</td>
                      <td>{{ $booking->traveller_name }}</td>
                      <td>
                        @if($booking->booking_type === 'hotel')
                          {{ $booking->check_in_date?->format('d M Y') }} &ndash; {{ $booking->check_out_date?->format('d M Y') }}
                        @else
                          {{ $booking->travel_date->format('d M Y') }}
                        @endif
                      </td>
                      <td>₹{{ number_format($booking->total_amount, 2) }}</td>
                      <td>
                        @if($booking->status === 'confirmed')
                          <span class="badge badge-success">Confirmed</span>
                        @elseif($booking->status === 'pending')
                          <span class="badge badge-warning">Pending</span>
                        @elseif($booking->status === 'cancelled')
                          <span class="badge badge-secondary">Cancelled</span>
                        @else
                          <span class="badge badge-danger">Failed</span>
                        @endif
                      </td>
                      <td>
                        @if($booking->payment_status === 'paid')
                          <span class="badge badge-success">Paid</span>
                        @elseif($booking->payment_status === 'pending')
                          <span class="badge badge-warning">Pending</span>
                        @else
                          <span class="badge badge-danger">Failed</span>
                        @endif
                      </td>
                      <td>{{ $booking->created_at->format('d M Y, h:i A') }}</td>
                      <td>
                        <a href="{{ route('crm.bookings.show', $booking) }}" class="btn btn-primary btn-sm">View</a>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="11" class="text-center">No bookings found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <div class="card-footer">
                {{ $bookings->links() }}
              </div>
            </div>

          </div>
        </div>
        </div>
    </div>
</div>
@endsection
