@extends('layouts.admin.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">

            @if($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Create Quotation — {{ $enquiry->name }}</h3>
                <div class="card-tools">
                  <a href="{{ route('crm.package-enquiries.show', $enquiry) }}" class="btn btn-secondary btn-sm">&larr; Back to Lead</a>
                </div>
              </div>
              <form action="{{ route('crm.quotations.store', $enquiry) }}" method="POST" id="quotationForm">
                @csrf
                <div class="card-body">

                  <div class="row">
                    <div class="col-md-4 form-group">
                      <label>Customer Name</label>
                      <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $enquiry->name) }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                      <label>Customer Email</label>
                      <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email', $enquiry->email) }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                      <label>Customer Phone</label>
                      <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone', $enquiry->phone) }}">
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-4 form-group">
                      <label>Package</label>
                      <input type="text" class="form-control" value="{{ $enquiry->holidayPackage->title ?? 'No package linked to this lead' }}" disabled>
                    </div>
                    <div class="col-md-4 form-group">
                      <label>Valid Until</label>
                      <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until') }}">
                    </div>
                  </div>

                  <hr>

                  <h5>Price Breakdown</h5>
                  <table class="table" id="itemsTable">
                    <thead>
                      <tr>
                        <th>Description</th>
                        <th style="width:110px;">Qty</th>
                        <th style="width:160px;">Unit Price (INR)</th>
                        <th style="width:160px;">Amount (INR)</th>
                        <th style="width:50px;"></th>
                      </tr>
                    </thead>
                    <tbody id="itemsBody">
                      @php $oldItems = old('items', [['description' => '', 'quantity' => 1, 'unit_price' => '']]); @endphp
                      @foreach($oldItems as $item)
                        <tr class="item-row">
                          <td><input type="text" name="items[][description]" class="form-control" value="{{ $item['description'] ?? '' }}" required></td>
                          <td><input type="number" name="items[][quantity]" class="form-control item-qty" value="{{ $item['quantity'] ?? 1 }}" min="1" required></td>
                          <td><input type="number" name="items[][unit_price]" class="form-control item-price" value="{{ $item['unit_price'] ?? '' }}" step="0.01" min="0" required></td>
                          <td><span class="item-amount">0.00</span></td>
                          <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-times"></i></button></td>
                        </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="3" class="text-right"><strong>Total</strong></td>
                        <td><strong>&#8377; <span id="grandTotal">0.00</span></strong></td>
                        <td></td>
                      </tr>
                    </tfoot>
                  </table>
                  <button type="button" class="btn btn-outline-primary btn-sm" id="addRow"><i class="fas fa-plus"></i> Add Item</button>

                  <hr>

                  <div class="form-group">
                    <label>Notes / Terms (optional)</label>
                    <textarea name="notes" class="form-control" rows="4">{{ old('notes') }}</textarea>
                  </div>

                </div>
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send Quotation</button>
                </div>
              </form>
            </div>

          </div>
        </div>
        </div>
    </div>
</div>

<script>
(function () {
  const itemRowTemplate = document.querySelector('.item-row').outerHTML;

  function recalcRow(row) {
    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    row.querySelector('.item-amount').textContent = (qty * price).toFixed(2);
  }

  function recalcTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(function (row) {
      recalcRow(row);
      total += parseFloat(row.querySelector('.item-amount').textContent) || 0;
    });
    document.getElementById('grandTotal').textContent = total.toFixed(2);
  }

  document.getElementById('itemsBody').addEventListener('input', function (e) {
    if (e.target.classList.contains('item-qty') || e.target.classList.contains('item-price')) {
      recalcTotal();
    }
  });

  document.getElementById('itemsBody').addEventListener('click', function (e) {
    const btn = e.target.closest('.remove-row');
    if (!btn) return;
    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) {
      btn.closest('.item-row').remove();
      recalcTotal();
    }
  });

  document.getElementById('addRow').addEventListener('click', function () {
    const wrapper = document.createElement('tbody');
    wrapper.innerHTML = itemRowTemplate;
    const newRow = wrapper.firstElementChild;
    newRow.querySelectorAll('input').forEach(function (input) {
      if (input.type === 'number') {
        input.value = input.classList.contains('item-qty') ? 1 : '';
      } else {
        input.value = '';
      }
    });
    newRow.querySelector('.item-amount').textContent = '0.00';
    document.getElementById('itemsBody').appendChild(newRow);
  });

  recalcTotal();
})();
</script>
@endsection
