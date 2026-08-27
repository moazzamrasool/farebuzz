@extends('layouts.admin.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">

            @if(session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
              <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- ══════════ STATUS BAR ══════════ --}}
            <div class="card">
              <div class="card-body d-flex flex-wrap align-items-center justify-content-between" style="gap:12px;">
                <div>
                  <h4 class="mb-1">{{ $enquiry->name }} <span class="badge {{ $enquiry->status->badgeClass() }}">{{ $enquiry->status->label() }}</span></h4>
                  <span class="text-muted small">Lead #{{ $enquiry->id }} &middot; Created {{ $enquiry->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <form action="{{ route('crm.package-enquiries.update-status', $enquiry->id) }}" method="POST" class="form-inline lead-status-form" style="gap:8px;">
                  @csrf
                  @method('PUT')
                  <select name="status" class="form-control form-control-sm lead-status-select" data-requires-reason="{{ implode(',', \App\Enums\LeadStatus::requiringLostReason()) }}">
                    @foreach($statusOptions as $value => $label)
                      <option value="{{ $value }}" @selected($enquiry->status->value === $value)>{{ $label }}</option>
                    @endforeach
                  </select>
                  <input type="text" name="lost_reason" value="{{ $enquiry->lost_reason }}" class="form-control form-control-sm lead-lost-reason {{ $enquiry->status->requiresLostReason() ? '' : 'd-none' }}" placeholder="Reason for lost/junk...">
                  <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
                </form>
              </div>
              <a href="{{ route('crm.package-enquiries.index') }}" class="card-footer text-muted small">&larr; Back to Leads</a>
            </div>

            <div class="row">
              {{-- ══════════ LEFT: DETAILS + QUICK ACTIONS ══════════ --}}
              <div class="col-md-4">
                <div class="card">
                  <div class="card-header"><h3 class="card-title">Lead Details</h3></div>
                  <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                      <tr><th style="width:120px;">Phone</th><td>{{ $enquiry->phone }}</td></tr>
                      <tr><th>Email</th><td>{{ $enquiry->email }}</td></tr>
                      <tr><th>Package</th><td>{{ $enquiry->holidayPackage->title ?? 'N/A' }}</td></tr>
                      <tr><th>Budget</th><td>{{ $enquiry->budget ? '₹'.number_format($enquiry->budget, 2) : '—' }}</td></tr>
                      <tr><th>Travel Date</th><td>{{ optional($enquiry->travel_date)->format('d M Y') ?? '—' }}</td></tr>
                      <tr><th>Travellers</th><td>{{ $enquiry->travellers ?? '—' }}</td></tr>
                      <tr><th>Source</th><td>{{ $enquiry->source ? ucfirst($enquiry->source) : '—' }}</td></tr>
                      <tr><th>Assigned Agent</th><td>{{ $enquiry->assignedAdmin->name ?? 'Unassigned' }}</td></tr>
                      <tr><th>Message</th><td>{{ $enquiry->message ?? '—' }}</td></tr>
                    </table>
                  </div>
                </div>

                <div class="card">
                  <div class="card-header"><h3 class="card-title">Quick Actions</h3></div>
                  <div class="card-body d-flex flex-wrap" style="gap:8px;">
                    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#logCallModal"><i class="fas fa-phone"></i> Log a Call</button>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#addNoteModal"><i class="fas fa-sticky-note"></i> Add Note</button>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#followUpModal"><i class="fas fa-calendar-plus"></i> Schedule Follow-up</button>
                    <button type="button" class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#whatsappModal"><i class="fab fa-whatsapp"></i> Send WhatsApp</button>
                    <button type="button" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#emailModal"><i class="fas fa-envelope"></i> Send Email</button>
                    @if($hasItinerary)
                      <a href="{{ route('crm.package-enquiries.itinerary.preview', $enquiry) }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="fas fa-eye"></i> Preview Itinerary</a>
                      <form action="{{ route('crm.package-enquiries.itinerary.send', $enquiry) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-info btn-sm"><i class="fas fa-map"></i> Send Itinerary</button>
                      </form>
                    @endif
                    <a href="{{ route('crm.quotations.create', $enquiry) }}" class="btn btn-outline-warning btn-sm"><i class="fas fa-file-invoice-dollar"></i> Create Quotation</a>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#reassignModal"><i class="fas fa-user-tag"></i> Reassign</button>
                  </div>
                </div>

                <div class="card">
                  <div class="card-header"><h3 class="card-title">Quotations</h3></div>
                  <div class="card-body p-0">
                    @forelse($enquiry->quotations as $quotation)
                      <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div>
                          <a href="{{ route('crm.quotations.show', $quotation) }}">{{ $quotation->quotation_number }}</a>
                          <div class="small text-muted">
                            &#8377;{{ number_format($quotation->total_amount, 2) }}
                            &middot; {{ $quotation->sent_at ? $quotation->sent_at->format('d M Y') : 'not sent' }}
                          </div>
                        </div>
                        <a href="{{ route('crm.quotations.download', $quotation) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-download"></i></a>
                      </div>
                    @empty
                      <p class="text-muted text-center py-3 mb-0">No quotations sent yet.</p>
                    @endforelse
                  </div>
                </div>

                <div class="card">
                  <div class="card-header"><h3 class="card-title">Follow-ups</h3></div>
                  <div class="card-body p-0">
                    @forelse($enquiry->followUps as $followUp)
                      <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div>
                          <span class="badge {{ $followUp->status === 'completed' ? 'badge-success' : ($followUp->due_at->isPast() ? 'badge-danger' : 'badge-warning') }}">
                            {{ $followUp->status === 'completed' ? 'Completed' : ($followUp->due_at->isPast() ? 'Overdue' : 'Pending') }}
                          </span>
                          <div class="small">{{ $followUp->due_at->format('d M Y, h:i A') }}</div>
                          @if($followUp->note)<div class="small text-muted">{{ $followUp->note }}</div>@endif
                        </div>
                        @if($followUp->status === 'pending')
                          <form action="{{ route('crm.package-enquiries.follow-ups.complete', [$enquiry->id, $followUp->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Complete</button>
                          </form>
                        @endif
                      </div>
                    @empty
                      <p class="text-muted text-center py-3 mb-0">No follow-ups scheduled.</p>
                    @endforelse
                  </div>
                </div>
              </div>

              {{-- ══════════ RIGHT: ACTIVITY TIMELINE ══════════ --}}
              <div class="col-md-8">
                <div class="card">
                  <div class="card-header"><h3 class="card-title">Activity Timeline</h3></div>
                  <div class="card-body">
                    @if($enquiry->activities->isEmpty())
                      <p class="text-muted text-center py-5 mb-0">No activity logged yet.</p>
                    @else
                      <div class="timeline">
                        @foreach($enquiry->activities as $activity)
                          @php
                            $icon = match($activity->type) {
                              'status_change' => 'fa-exchange-alt bg-blue',
                              'note' => 'fa-sticky-note bg-gray',
                              'call' => 'fa-phone bg-purple',
                              'email' => 'fa-envelope bg-info',
                              'quotation' => 'fa-file-invoice-dollar bg-warning',
                              'whatsapp' => 'fa-whatsapp fab bg-success',
                              'follow_up_scheduled' => 'fa-calendar-plus bg-warning',
                              'follow_up_completed' => 'fa-calendar-check bg-success',
                              'assignment_change' => 'fa-user-tag bg-secondary',
                              default => 'fa-circle bg-secondary',
                            };
                          @endphp
                          <div>
                            <i class="fas {{ $icon }}"></i>
                            <div class="timeline-item">
                              <span class="time"><i class="fas fa-clock"></i> {{ $activity->created_at->format('d M Y, h:i A') }}</span>
                              <h3 class="timeline-header">{{ $activity->admin->name ?? 'System' }}</h3>
                              <div class="timeline-body">
                                {{ $activity->description }}
                              </div>
                            </div>
                          </div>
                        @endforeach
                        <div><i class="fas fa-clock bg-gray"></i></div>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>

        </div>
    </div>
</div>

{{-- ══════════ MODALS ══════════ --}}

<div class="modal fade" id="logCallModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('crm.package-enquiries.notes.store', $enquiry->id) }}" method="POST" class="modal-content">
      @csrf
      <input type="hidden" name="type" value="call">
      <div class="modal-header"><h5 class="modal-title">Log a Call</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div class="form-group">
          <label>Outcome</label>
          <select name="outcome" class="form-control" required>
            <option value="connected">Connected</option>
            <option value="no_answer">No Answer</option>
            <option value="busy">Busy</option>
            <option value="callback">Callback Requested</option>
          </select>
        </div>
        <div class="form-group">
          <label>Note</label>
          <textarea name="note" class="form-control" rows="3" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="addNoteModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('crm.package-enquiries.notes.store', $enquiry->id) }}" method="POST" class="modal-content">
      @csrf
      <input type="hidden" name="type" value="note">
      <div class="modal-header"><h5 class="modal-title">Add Note</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div class="form-group">
          <label>Note Type</label>
          <select name="channel" class="form-control">
            <option value="general">General</option>
            <option value="call">Call</option>
            <option value="meeting">Meeting</option>
            <option value="email">Email</option>
            <option value="whatsapp">WhatsApp</option>
          </select>
        </div>
        <div class="form-group">
          <label>Note</label>
          <textarea name="note" class="form-control" rows="3" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="followUpModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('crm.package-enquiries.follow-ups.store', $enquiry->id) }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header"><h5 class="modal-title">Schedule Follow-up</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div class="form-group">
          <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-primary follow-up-preset" data-days="1">Tomorrow</button>
            <button type="button" class="btn btn-outline-primary follow-up-preset" data-days="3">+3 Days</button>
            <button type="button" class="btn btn-outline-primary follow-up-preset" data-days="7">+7 Days</button>
          </div>
        </div>
        <div class="form-group">
          <label>Due Date &amp; Time</label>
          <input type="datetime-local" name="due_at" id="followUpDueAt" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Assign To</label>
          <select name="assigned_admin_id" class="form-control">
            <option value="">{{ $enquiry->assignedAdmin->name ?? 'Lead owner' }} (current)</option>
            @foreach($agents as $agent)
              <option value="{{ $agent->id }}">{{ $agent->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Purpose / Note</label>
          <textarea name="note" class="form-control" rows="2"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Schedule</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="reassignModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('crm.package-enquiries.assign', $enquiry->id) }}" method="POST" class="modal-content">
      @csrf
      @method('PUT')
      <div class="modal-header"><h5 class="modal-title">Reassign Lead</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div class="form-group">
          <label>Agent</label>
          <select name="assigned_admin_id" class="form-control">
            <option value="">Unassigned</option>
            @foreach($agents as $agent)
              <option value="{{ $agent->id }}" @selected($enquiry->assigned_admin_id === $agent->id)>{{ $agent->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

@include('admin.package-enquiries.partials._whatsapp-compose', ['enquiry' => $enquiry])
@include('admin.package-enquiries.partials._email-compose', ['enquiry' => $enquiry])

<script>
  (function ($) {
    function toggleLostReason($form) {
      var status = $form.find('.lead-status-select').val();
      var requires = ($form.find('.lead-status-select').data('requires-reason') || '').toString().split(',');
      $form.find('.lead-lost-reason').toggleClass('d-none', requires.indexOf(status) === -1);
    }

    $(document).on('change', '.lead-status-select', function () {
      toggleLostReason($(this).closest('form'));
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

    $(document).on('click', '.follow-up-preset', function () {
      var days = parseInt($(this).data('days'), 10);
      var due = new Date();
      due.setDate(due.getDate() + days);
      due.setHours(10, 0, 0, 0);
      var pad = function (n) { return n < 10 ? '0' + n : n; };
      var value = due.getFullYear() + '-' + pad(due.getMonth() + 1) + '-' + pad(due.getDate()) + 'T' + pad(due.getHours()) + ':' + pad(due.getMinutes());
      $('#followUpDueAt').val(value);
    });
  })(jQuery);
</script>
@endsection
