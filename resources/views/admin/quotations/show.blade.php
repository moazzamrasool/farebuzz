@extends('layouts.admin.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Quotation {{ $quotation->quotation_number }}</h3>
                <div class="card-tools">
                  <a href="{{ route('crm.quotations.download', $quotation) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-download"></i> Download PDF</a>
                  @if($quotation->packageEnquiry)
                    <a href="{{ route('crm.package-enquiries.show', $quotation->packageEnquiry) }}" class="btn btn-secondary btn-sm">&larr; Back to Lead</a>
                  @endif
                </div>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                      <tr><th style="width:160px;">Customer</th><td>{{ $quotation->customer_name }}</td></tr>
                      <tr><th>Email</th><td>{{ $quotation->customer_email }}</td></tr>
                      <tr><th>Phone</th><td>{{ $quotation->customer_phone ?: '—' }}</td></tr>
                      <tr><th>Package</th><td>{{ $quotation->holidayPackage->title ?? '—' }}</td></tr>
                      <tr><th>Valid Until</th><td>{{ $quotation->valid_until?->format('d M Y') ?: '—' }}</td></tr>
                      <tr><th>Sent</th><td>{{ $quotation->sent_at ? $quotation->sent_at->format('d M Y, h:i A') : 'Not sent yet' }}</td></tr>
                      <tr><th>Created By</th><td>{{ $quotation->createdBy->name ?? '—' }}</td></tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-header"><h3 class="card-title">Price Breakdown</h3></div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>Description</th>
                      <th>Qty</th>
                      <th>Unit Price</th>
                      <th>Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($quotation->items as $item)
                      <tr>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>&#8377;{{ number_format($item->unit_price, 2) }}</td>
                        <td>&#8377;{{ number_format($item->amount, 2) }}</td>
                      </tr>
                    @endforeach
                    <tr>
                      <td colspan="3" class="text-right"><strong>Total</strong></td>
                      <td><strong>&#8377;{{ number_format($quotation->total_amount, 2) }}</strong></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            @if($quotation->notes)
              <div class="card">
                <div class="card-header"><h3 class="card-title">Notes &amp; Terms</h3></div>
                <div class="card-body">{{ $quotation->notes }}</div>
              </div>
            @endif

          </div>
        </div>
        </div>
    </div>
</div>
@endsection
