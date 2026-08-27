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
                <h3 class="card-title">
                  Booking {{ $booking->booking_reference }}
                  @if($booking->booking_type === 'hotel')
                    <span class="badge badge-info ml-2">Hotel</span>
                  @else
                    <span class="badge badge-primary ml-2">Package</span>
                  @endif
                </h3>
                <div class="card-tools">
                  <a href="{{ route('crm.bookings.invoice', $booking) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-download"></i> Invoice PDF</a>
                  @if($hasItinerary)
                    <a href="{{ route('crm.bookings.itinerary', $booking) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-map"></i> Itinerary PDF</a>
                    <a href="{{ route('crm.bookings.itinerary.preview', $booking) }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="fas fa-eye"></i> Preview</a>
                    @if($booking->user?->email)
                      <form action="{{ route('crm.bookings.send-itinerary', $booking) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-info btn-sm"><i class="fas fa-envelope"></i> Send Itinerary to Customer</button>
                      </form>
                    @endif
                  @endif
                  <a href="{{ route('crm.bookings.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                </div>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    @if($booking->booking_type === 'hotel')
                      <h6>Stay Details</h6>
                      <table class="table table-borderless table-sm">
                        <tr><th style="width:160px;">Hotel</th><td>{{ $booking->hotel->name ?? $booking->package_title }}</td></tr>
                        <tr><th>Room Type</th><td>{{ $booking->room_type_name ?? '—' }}</td></tr>
                        @if($booking->room_type_bed_type)
                          <tr><th>Bed Type</th><td>{{ $booking->room_type_bed_type->label() }}</td></tr>
                        @endif
                        <tr><th>Check-in</th><td>{{ $booking->check_in_date?->format('d M Y') }}</td></tr>
                        <tr><th>Check-out</th><td>{{ $booking->check_out_date?->format('d M Y') }}</td></tr>
                        <tr><th>Nights</th><td>{{ $booking->nights }}</td></tr>
                        <tr><th>Rooms</th><td>{{ $booking->rooms }}</td></tr>
                        <tr><th>Guests</th><td>{{ $booking->adults }} Adult(s){{ $booking->children ? ', '.$booking->children.' Child(ren)' : '' }}</td></tr>
                        <tr><th>Status</th><td>{{ ucfirst($booking->status) }}</td></tr>
                      </table>
                    @else
                      <h6>Trip Details</h6>
                      <table class="table table-borderless table-sm">
                        <tr><th style="width:160px;">Package</th><td>{{ $booking->package_title }}</td></tr>
                        <tr><th>Travel Date</th><td>{{ $booking->travel_date->format('d M Y') }}</td></tr>
                        <tr><th>Departure City</th><td>{{ $booking->departure_city ?? '—' }}</td></tr>
                        <tr><th>Room Type</th><td>{{ $booking->room_type_name ?? '—' }}</td></tr>
                        <tr><th>Travellers</th><td>{{ $booking->adults }} Adult(s){{ $booking->children ? ', '.$booking->children.' Child(ren)' : '' }}</td></tr>
                        <tr><th>Status</th><td>{{ ucfirst($booking->status) }}</td></tr>
                      </table>
                    @endif
                  </div>
                  <div class="col-md-6">
                    <h6>Traveller</h6>
                    <table class="table table-borderless table-sm">
                      <tr><th style="width:160px;">Name</th><td>{{ $booking->traveller_name }}</td></tr>
                      <tr><th>Email</th><td>{{ $booking->traveller_email }}</td></tr>
                      <tr><th>Phone</th><td>{{ $booking->traveller_phone }}</td></tr>
                      <tr><th>Address</th><td>{{ $booking->traveller_address }}</td></tr>
                      <tr><th>GST</th><td>{{ $booking->gst_number ?? '—' }}</td></tr>
                    </table>
                  </div>
                </div>

                @include('bookings.partials._day_itinerary_cards', ['booking' => $booking, 'variant' => 'admin'])

                @if($booking->activities->isNotEmpty())
                  <hr>
                  <h6>Add-ons</h6>
                  <table class="table table-sm">
                    <thead><tr><th>Activity</th><th>Unit Price</th><th>Qty</th><th>Line Total</th></tr></thead>
                    <tbody>
                      @foreach($booking->activities as $bookingActivity)
                        <tr>
                          <td>{{ $bookingActivity->name }}</td>
                          <td>₹{{ number_format($bookingActivity->unit_price, 2) }}</td>
                          <td>{{ $bookingActivity->qty }}</td>
                          <td>₹{{ number_format($bookingActivity->line_total, 2) }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                @endif

                @if($booking->hotels->isNotEmpty())
                  <hr>
                  <h6>Hotel</h6>
                  <table class="table table-sm">
                    <thead><tr><th>Hotel</th><th>Unit Price / Night</th><th>Nights</th><th>Line Total</th></tr></thead>
                    <tbody>
                      @foreach($booking->hotels as $bookingHotel)
                        <tr>
                          <td>{{ $bookingHotel->name }}@if($bookingHotel->bed_type) &middot; {{ $bookingHotel->bed_type->label() }}@endif</td>
                          <td>₹{{ number_format($bookingHotel->unit_price, 2) }}</td>
                          <td>{{ $bookingHotel->nights }}</td>
                          <td>₹{{ number_format($bookingHotel->line_total, 2) }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                @endif

                <hr>
                <h6>Price Breakdown</h6>
                <table class="table table-borderless table-sm" style="max-width:400px;">
                  <tr><th>Base Fare</th><td>₹{{ number_format($booking->base_fare, 2) }}</td></tr>
                  <tr><th>Discount</th><td>−₹{{ number_format($booking->discount_amount, 2) }}</td></tr>
                  @if($booking->activities_total > 0)
                    <tr><th>Add-ons</th><td>₹{{ number_format($booking->activities_total, 2) }}</td></tr>
                  @endif
                  @if($booking->hotels_total > 0)
                    <tr><th>Hotel</th><td>₹{{ number_format($booking->hotels_total, 2) }}</td></tr>
                  @endif
                  @if($booking->coupon_code)
                    <tr><th>Coupon Discount</th><td class="text-success">−₹{{ number_format($booking->coupon_discount_amount, 2) }} <span class="badge badge-success">{{ $booking->coupon_code }}</span></td></tr>
                  @endif
                  <tr><th>Taxes &amp; Fees</th><td>₹{{ number_format($booking->taxes_fee, 2) }}</td></tr>
                  <tr><th>Total</th><td><strong>₹{{ number_format($booking->total_amount, 2) }}</strong></td></tr>
                  <tr><th>Payment Status</th><td>{{ ucfirst($booking->payment_status) }}</td></tr>
                </table>

                <hr>
                <h6>
                  Payment Transactions
                  @if($booking->payment_status !== 'paid' && $booking->payments->isNotEmpty())
                    <form action="{{ route('crm.bookings.reconcile-payment', $booking) }}" method="POST" class="d-inline float-right">
                      @csrf
                      <button type="submit" class="btn btn-outline-warning btn-sm" onclick="return confirm('Ask PayU for this booking\'s real payment status and confirm it if PayU says it succeeded?');">
                        <i class="fas fa-sync"></i> Verify with PayU
                      </button>
                    </form>
                  @endif
                </h6>
                <table class="table table-hover table-sm">
                  <thead><tr><th>Txn ID</th><th>Gateway</th><th>Mode</th><th>Amount</th><th>Status</th><th>Hash Verified</th><th>Date</th></tr></thead>
                  <tbody>
                    @forelse($booking->payments as $payment)
                      <tr>
                        <td><code>{{ $payment->gateway_txn_id }}</code></td>
                        <td>{{ strtoupper($payment->gateway) }}</td>
                        <td><span class="badge {{ $payment->mode === 'live' ? 'badge-danger' : 'badge-secondary' }}">{{ strtoupper($payment->mode) }}</span></td>
                        <td>₹{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ ucfirst($payment->status) }}</td>
                        <td>{{ $payment->hash_verified ? 'Yes' : 'No' }}</td>
                        <td>{{ $payment->created_at->format('d M Y, h:i A') }}</td>
                      </tr>
                    @empty
                      <tr><td colspan="7" class="text-center">No payment attempts yet.</td></tr>
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
