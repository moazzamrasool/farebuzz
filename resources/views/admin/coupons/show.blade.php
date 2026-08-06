@extends('layouts.admin.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Coupon: <code>{{ $coupon->code }}</code> — {{ $coupon->title }}</h3>
                <a href="{{ route('crm.coupons.edit', $coupon->id) }}" class="btn btn-primary btn-sm">Edit</a>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="info-box">
                      <span class="info-box-icon bg-info"><i class="fas fa-hashtag"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Times Used</span>
                        <span class="info-box-number">{{ $coupon->usages_count }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="info-box">
                      <span class="info-box-icon bg-success"><i class="fas fa-rupee-sign"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Total Discount Given</span>
                        <span class="info-box-number">₹{{ number_format($totalDiscountGiven, 2) }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="info-box">
                      <span class="info-box-icon {{ $coupon->status === 'active' ? 'bg-success' : 'bg-secondary' }}"><i class="fas fa-toggle-on"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Status</span>
                        <span class="info-box-number">{{ ucfirst($coupon->status) }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="info-box">
                      <span class="info-box-icon bg-warning"><i class="fas fa-calendar"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Valid</span>
                        <span class="info-box-number" style="font-size:14px;">{{ $coupon->valid_from->format('d M Y') }} – {{ $coupon->valid_to->format('d M Y') }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <dl class="row mt-3">
                  <dt class="col-sm-3">Discount</dt>
                  <dd class="col-sm-9">
                    @if($coupon->discount_type === 'percentage')
                      {{ rtrim(rtrim(number_format($coupon->discount_value, 2), '0'), '.') }}%
                      @if($coupon->max_discount_amount) (capped at ₹{{ number_format($coupon->max_discount_amount, 2) }}) @endif
                    @else
                      ₹{{ number_format($coupon->discount_value, 2) }} flat
                    @endif
                  </dd>
                  <dt class="col-sm-3">Min Booking Amount</dt>
                  <dd class="col-sm-9">₹{{ number_format($coupon->min_booking_amount, 2) }}</dd>
                  <dt class="col-sm-3">Applicable To</dt>
                  <dd class="col-sm-9">{{ ucwords(str_replace('_', ' ', $coupon->applicable_to)) }}</dd>
                  <dt class="col-sm-3">Per-User Limit</dt>
                  <dd class="col-sm-9">{{ $coupon->per_user_limit ?? 'Unlimited' }}</dd>
                </dl>
              </div>
            </div>

            <div class="card">
              <div class="card-header"><h3 class="card-title">Redemptions</h3></div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>SN.</th>
                      <th>Booking Ref</th>
                      <th>Email</th>
                      <th>Discount Given</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($usages as $key => $usage)
                    <tr>
                      <td>{{ $usages->firstItem() + $key }}</td>
                      <td>{{ $usage->booking->booking_reference ?? '—' }}</td>
                      <td>{{ $usage->email }}</td>
                      <td>₹{{ number_format($usage->discount_amount, 2) }}</td>
                      <td>{{ $usage->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">This coupon has not been used yet.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <div class="card-footer">
                {{ $usages->links() }}
              </div>
            </div>

          </div>
        </div>
        </div>
    </div>
</div>
@endsection
