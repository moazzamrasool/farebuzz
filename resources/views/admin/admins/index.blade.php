
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
                <h3 class="card-title">Companies</h3>
                <div class="card-tools">
                  <a href="{{ route('crm.admins.create') }}" class="btn btn-primary">+ Add Company</a>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>SN.</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Unique ID</th>
                      <th>Status</th>
                      <th>Last Action</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($admins as $key=>$admin)
                    <tr>
                      <td>{{ $admins->firstItem() + $key }}</td>
                      <td>{{ $admin->name }}</td>
                      <td>{{ $admin->email }}</td>
                      <td><code>{{ $admin->unique_id }}</code></td>
                      <td>
                        @if($admin->is_blocked)
                          <span class="badge badge-danger">Blocked</span>
                        @else
                          <span class="badge badge-success">Active</span>
                        @endif
                      </td>
                      <td>
                        @if($admin->is_blocked && $admin->blocked_at)
                          <small class="text-muted">
                            Blocked by {{ $admin->blockedBy->name ?? 'Unknown' }}<br>
                            {{ $admin->blocked_at->format('d M Y, h:i A') }}
                            @if($admin->blocked_reason)
                              <br><span class="text-danger">"{{ Str::limit($admin->blocked_reason, 60) }}"</span>
                            @endif
                          </small>
                        @elseif(!$admin->is_blocked && $admin->unblocked_at)
                          <small class="text-muted">
                            Unblocked by {{ $admin->unblockedBy->name ?? 'Unknown' }}<br>
                            {{ $admin->unblocked_at->format('d M Y, h:i A') }}
                          </small>
                        @else
                          <small class="text-muted">—</small>
                        @endif
                      </td>
                      <td>
                        <a href="{{ route('crm.admins.edit', $admin->id) }}" class="btn btn-primary btn-sm">Edit</a>

                        @if($admin->is_blocked)
                          <button type="button" class="btn btn-success btn-sm btn-unblock-company"
                                  data-toggle="modal" data-target="#companyBlockModal"
                                  data-action-url="{{ route('crm.admins.unblock', $admin->id) }}"
                                  data-company-name="{{ $admin->name }}">
                            Unblock
                          </button>
                        @else
                          <button type="button" class="btn btn-danger btn-sm btn-block-company"
                                  data-toggle="modal" data-target="#companyBlockModal"
                                  data-action-url="{{ route('crm.admins.block', $admin->id) }}"
                                  data-company-name="{{ $admin->name }}">
                            Block
                          </button>
                        @endif

                        <form action="{{ route('crm.admins.destroy', $admin->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this company?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="7" class="text-center">No companies found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                {{ $admins->links() }}
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        </div>
    </div>
</div>

<!-- Block / Unblock confirmation modal — shared, JS fills in the target company and action -->
<div class="modal fade" id="companyBlockModal" tabindex="-1" role="dialog" aria-labelledby="companyBlockModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="companyBlockForm" method="POST" action="">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="companyBlockModalLabel">Block Company</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning" id="companyBlockWarning">
            <strong>This takes the company's entire public site and CRM offline immediately.</strong>
            All admins and users at this company will be logged out and unable to log back in until unblocked.
          </div>
          <p>Company: <strong id="companyBlockName"></strong></p>
          <div class="form-group">
            <label for="companyBlockReason" id="companyBlockReasonLabel">Reason (optional)</label>
            <textarea name="reason" id="companyBlockReason" rows="3" class="form-control" placeholder="e.g. Payment overdue, policy violation..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger" id="companyBlockSubmitBtn">Block Company</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(function () {
    var $modal = $('#companyBlockModal');
    var $form = $('#companyBlockForm');

    $(document).on('click', '.btn-block-company', function () {
      var $btn = $(this);
      $form.attr('action', $btn.data('action-url'));
      $('#companyBlockName').text($btn.data('company-name'));
      $('#companyBlockModalLabel').text('Block Company');
      $('#companyBlockReasonLabel').text('Reason (optional)');
      $('#companyBlockWarning').removeClass('d-none').show();
      $('#companyBlockSubmitBtn').removeClass('btn-success').addClass('btn-danger').text('Block Company');
      $form.find('[name="reason"]').val('');
    });

    $(document).on('click', '.btn-unblock-company', function () {
      var $btn = $(this);
      $form.attr('action', $btn.data('action-url'));
      $('#companyBlockName').text($btn.data('company-name'));
      $('#companyBlockModalLabel').text('Unblock Company');
      $('#companyBlockReasonLabel').text('Note (optional)');
      $('#companyBlockWarning').hide();
      $('#companyBlockSubmitBtn').removeClass('btn-danger').addClass('btn-success').text('Unblock Company');
      $form.find('[name="reason"]').val('');
    });
  });
</script>
@endsection
