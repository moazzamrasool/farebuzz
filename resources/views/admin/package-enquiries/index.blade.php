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
                <h3 class="card-title">Leads</h3>
                <div class="card-tools">
                  <a href="{{ route('crm.follow-ups.due') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-bell"></i> Follow-ups Due</a>
                  <a href="{{ route('crm.leads.dashboard') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-chart-pie"></i> CRM Dashboard</a>
                </div>
              </div>

              <div class="card-body border-bottom">
                <form method="GET" action="{{ route('crm.package-enquiries.index') }}" class="form-row align-items-end">
                  <div class="col-md-2 form-group mb-2">
                    <label class="small text-muted mb-1">Status</label>
                    <select name="status" class="form-control form-control-sm">
                      <option value="">All Statuses</option>
                      @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-2 form-group mb-2">
                    <label class="small text-muted mb-1">Agent</label>
                    <select name="assigned_admin_id" class="form-control form-control-sm">
                      <option value="">All Agents</option>
                      @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" @selected((string) request('assigned_admin_id') === (string) $agent->id)>{{ $agent->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-2 form-group mb-2">
                    <label class="small text-muted mb-1">Source</label>
                    <select name="source" class="form-control form-control-sm">
                      <option value="">All Sources</option>
                      @foreach($sources as $source)
                        <option value="{{ $source }}" @selected(request('source') === $source)>{{ ucfirst($source) }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-2 form-group mb-2">
                    <label class="small text-muted mb-1">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
                  </div>
                  <div class="col-md-2 form-group mb-2">
                    <label class="small text-muted mb-1">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
                  </div>
                  <div class="col-md-2 form-group mb-2">
                    <label class="small text-muted mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, phone, email" class="form-control form-control-sm">
                  </div>
                  <div class="col-12 mt-1">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route('crm.package-enquiries.index') }}" class="btn btn-secondary btn-sm">Clear</a>
                  </div>
                </form>
              </div>

              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>SN.</th>
                      <th>Name</th>
                      <th>Package</th>
                      <th>Phone</th>
                      <th>Status</th>
                      <th>Agent</th>
                      <th>Source</th>
                      <th>Next Follow-up</th>
                      <th>Last Activity</th>
                      <th>Created</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($enquiries as $key=>$enquiry)
                    <tr>
                      <td>{{ $enquiries->firstItem() + $key }}</td>
                      <td>{{ $enquiry->name }}</td>
                      <td>{{ $enquiry->holidayPackage->title ?? 'N/A' }}</td>
                      <td>{{ $enquiry->phone }}</td>
                      <td>
                        <form action="{{ route('crm.package-enquiries.update-status', $enquiry->id) }}" method="POST" class="d-inline-block lead-status-form">
                          @csrf
                          @method('PUT')
                          <select name="status" class="form-control form-control-sm d-inline-block lead-status-select" data-requires-reason="{{ implode(',', \App\Enums\LeadStatus::requiringLostReason()) }}" style="width:auto;">
                            @foreach($statusOptions as $value => $label)
                              <option value="{{ $value }}" @selected($enquiry->status->value === $value)>{{ $label }}</option>
                            @endforeach
                          </select>
                          <input type="text" name="lost_reason" class="form-control form-control-sm d-none lead-lost-reason mt-1" placeholder="Reason for lost/junk...">
                        </form>
                      </td>
                      <td>
                        <form action="{{ route('crm.package-enquiries.assign', $enquiry->id) }}" method="POST" class="d-inline-block">
                          @csrf
                          @method('PUT')
                          <select name="assigned_admin_id" class="form-control form-control-sm d-inline-block" style="width:auto;" onchange="this.form.submit()">
                            <option value="">Unassigned</option>
                            @foreach($agents as $agent)
                              <option value="{{ $agent->id }}" @selected($enquiry->assigned_admin_id === $agent->id)>{{ $agent->name }}</option>
                            @endforeach
                          </select>
                        </form>
                      </td>
                      <td>{{ $enquiry->source ? ucfirst($enquiry->source) : '—' }}</td>
                      <td>{{ optional($enquiry->pendingFollowUp)->due_at?->format('d M Y, h:i A') ?? '—' }}</td>
                      <td>{{ optional($enquiry->last_activity_at)->diffForHumans() ?? '—' }}</td>
                      <td>{{ $enquiry->created_at->format('d M Y') }}</td>
                      <td>
                        <a href="{{ route('crm.package-enquiries.show', $enquiry->id) }}" class="btn btn-primary btn-sm">Open</a>
                        <form action="{{ route('crm.package-enquiries.destroy', $enquiry->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this lead?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="11" class="text-center">No leads found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                {{ $enquiries->links() }}
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        </div>
    </div>
</div>

<script>
  (function ($) {
    function toggleLostReason($form) {
      var status = $form.find('.lead-status-select').val();
      var requires = ($form.find('.lead-status-select').data('requires-reason') || '').toString().split(',');
      $form.find('.lead-lost-reason').toggleClass('d-none', requires.indexOf(status) === -1);
    }

    $(document).on('change', '.lead-status-select', function () {
      var $form = $(this).closest('form');
      toggleLostReason($form);
      var requires = ($(this).data('requires-reason') || '').toString().split(',');
      if (requires.indexOf($(this).val()) === -1) {
        $form.trigger('submit');
      }
    });

    $(document).on('submit', '.lead-status-form', function (e) {
      var $form = $(this);
      var requires = ($form.find('.lead-status-select').data('requires-reason') || '').toString().split(',');
      var status = $form.find('.lead-status-select').val();
      if (requires.indexOf(status) !== -1 && !$form.find('.lead-lost-reason').val()) {
        e.preventDefault();
        $form.find('.lead-lost-reason').removeClass('d-none').focus();
      }
    });
  })(jQuery);
</script>
@endsection
