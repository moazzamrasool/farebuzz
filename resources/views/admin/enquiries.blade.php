
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
                <h3 class="card-title">Business Enquiries</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>SN.</th>
                      <th>Business Name</th>
                      <th>Owner</th>
                      <th>Email</th>
                      <th>Phone</th>
                      <th>Type</th>
                      <th>Status</th>
                      <th>Unique ID</th>
                      <th>Submitted At</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($enquiries as $key=>$enquiry)
                    <tr>
                      <td>{{ $enquiries->firstItem() + $key }}</td>
                      <td>{{ $enquiry->business_name }}</td>
                      <td>{{ $enquiry->owner_name }}</td>
                      <td>{{ $enquiry->email }}</td>
                      <td>{{ $enquiry->phone }}</td>
                      <td>{{ $enquiry->business_type ?? 'N/A' }}</td>
                      <td>
                        @if($enquiry->status === 'pending')
                          <span class="badge badge-warning">Pending</span>
                        @elseif($enquiry->status === 'approved')
                          <span class="badge badge-success">Approved</span>
                        @else
                          <span class="badge badge-danger">Rejected</span>
                        @endif
                      </td>
                      <td>
                        @if($enquiry->business && $enquiry->business->unique_id)
                          <code>{{ $enquiry->business->unique_id }}</code>
                        @else
                          —
                        @endif
                      </td>
                      <td>{{ $enquiry->created_at->format('d M Y, h:i A') }}</td>
                      <td>
                        @if($enquiry->status === 'pending')
                          <form action="{{ route('crm.enquiries.approve', $enquiry->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve this business? A unique ID will be generated.');">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                          </form>
                          <form action="{{ route('crm.enquiries.reject', $enquiry->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reject this enquiry?');">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                          </form>
                        @else
                          —
                        @endif
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="10" class="text-center">No business enquiries found.</td>
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
@endsection
