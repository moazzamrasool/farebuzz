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
                <h3 class="card-title">Customer — {{ $customer->name }}</h3>
                <div class="card-tools">
                  <a href="{{ route('crm.customers.index') }}" class="btn btn-secondary btn-sm">&larr; Back to Customers</a>
                </div>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                      <tr><th style="width:160px;">Customer ID</th><td><code>{{ $customer->public_id }}</code></td></tr>
                      <tr><th>Name</th><td>{{ $customer->name }}</td></tr>
                      <tr><th>Email</th><td>{{ $customer->email }}</td></tr>
                      <tr><th>Phone</th><td>{{ $customer->phone ?: '—' }}</td></tr>
                      <tr><th>Registered</th><td>{{ $customer->created_at->format('d M Y, h:i A') }}</td></tr>
                      <tr>
                        <th>Verification</th>
                        <td>
                          @if($customer->email_verified_at)
                            <span class="badge badge-success">Verified on {{ $customer->email_verified_at->format('d M Y') }}</span>
                          @else
                            <span class="badge badge-warning">Pending</span>
                          @endif
                        </td>
                      </tr>
                      <tr>
                        <th>Account Status</th>
                        <td>
                          @if($customer->status === 'suspended')
                            <span class="badge badge-danger">Suspended</span>
                          @elseif($customer->status === 'active')
                            <span class="badge badge-success">Active</span>
                          @else
                            <span class="badge badge-secondary">Pending Verification</span>
                          @endif
                        </td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-6">
                    <h5>Actions</h5>
                    <div class="d-flex flex-wrap gap-2">
                      @unless($customer->email_verified_at)
                        <form action="{{ route('crm.customers.verify', $customer) }}" method="POST" class="d-inline mr-2 mb-2">
                          @csrf
                          <button type="submit" class="btn btn-success btn-sm">Manually Verify</button>
                        </form>
                        <form action="{{ route('crm.customers.resend-verification', $customer) }}" method="POST" class="d-inline mr-2 mb-2">
                          @csrf
                          <button type="submit" class="btn btn-info btn-sm">Resend Verification Email</button>
                        </form>
                      @endunless
                      <form action="{{ route('crm.customers.toggle-status', $customer) }}" method="POST" class="d-inline mb-2"
                            onsubmit="return confirm('{{ $customer->status === 'suspended' ? 'Activate' : 'Suspend' }} this customer?');">
                        @csrf
                        @if($customer->status === 'suspended')
                          <button type="submit" class="btn btn-success btn-sm">Activate Account</button>
                        @else
                          <button type="submit" class="btn btn-danger btn-sm">Suspend Account</button>
                        @endif
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-header"><h3 class="card-title">Bookings</h3></div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>Reference</th>
                      <th>Package</th>
                      <th>Status</th>
                      <th>Payment</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($bookings as $booking)
                    <tr>
                      <td>{{ $booking->booking_reference }}</td>
                      <td>{{ $booking->holidayPackage->title ?? '—' }}</td>
                      <td>{{ ucfirst($booking->status) }}</td>
                      <td>{{ ucfirst($booking->payment_status) }}</td>
                      <td>{{ $booking->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">No bookings with this company.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            <div class="card">
              <div class="card-header"><h3 class="card-title">Enquiries</h3></div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>Package</th>
                      <th>Status</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($enquiries as $enquiry)
                    <tr>
                      <td>{{ $enquiry->holidayPackage->title ?? '—' }}</td>
                      <td>{{ ucfirst($enquiry->status->value ?? $enquiry->status) }}</td>
                      <td>{{ $enquiry->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center">No enquiries with this company.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
        </div>
    </div>
</div>
@endsection
