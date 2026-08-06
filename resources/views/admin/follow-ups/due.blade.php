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
                <h3 class="card-title">Follow-ups Due</h3>
                <div class="card-tools">
                  <a href="{{ route('crm.package-enquiries.index') }}" class="btn btn-secondary btn-sm">Back to Leads</a>
                </div>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>Lead</th>
                      <th>Phone</th>
                      <th>Due</th>
                      <th>Note</th>
                      <th>Assigned To</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($followUps as $followUp)
                    <tr>
                      <td>{{ $followUp->leadable->name ?? 'Deleted lead' }}</td>
                      <td>{{ $followUp->leadable->phone ?? '—' }}</td>
                      <td>{{ $followUp->due_at->format('d M Y, h:i A') }}</td>
                      <td>{{ $followUp->note ?? '—' }}</td>
                      <td>{{ $followUp->assignedAdmin->name ?? '—' }}</td>
                      <td>
                        <span class="badge {{ $followUp->due_at->isPast() ? 'badge-danger' : 'badge-warning' }}">
                          {{ $followUp->due_at->isPast() ? 'Overdue' : 'Due Today' }}
                        </span>
                      </td>
                      <td>
                        @if($followUp->leadable)
                          <a href="{{ route('crm.package-enquiries.show', $followUp->leadable_id) }}" class="btn btn-primary btn-sm">View Lead</a>
                          <form action="{{ route('crm.package-enquiries.follow-ups.complete', [$followUp->leadable_id, $followUp->id]) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Mark Complete</button>
                          </form>
                        @endif
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="7" class="text-center">No follow-ups due right now.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <div class="card-footer">
                {{ $followUps->links() }}
              </div>
            </div>
          </div>
        </div>
        </div>
    </div>
</div>
@endsection
