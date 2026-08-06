@extends('layouts.user_dashboard')

@section('title', 'My Enquiries – FareBuzzer')

@section('content')
<div class="dash-page-title">My Enquiries</div>

<div class="dash-card">
  @if($enquiries->isEmpty())
    <div class="empty-state">
      <i class="bi bi-chat-dots" style="font-size:28px;display:block;margin-bottom:8px;"></i>
      You haven't submitted any enquiries yet.
    </div>
  @else
    <div style="overflow-x:auto;">
      <table class="dash-table">
        <thead>
          <tr><th>Package</th><th>Travel Date</th><th>Travellers</th><th>Status</th><th>Submitted</th></tr>
        </thead>
        <tbody>
          @foreach($enquiries as $enquiry)
            <tr>
              <td>{{ $enquiry->holidayPackage->title ?? 'N/A' }}</td>
              <td>{{ optional($enquiry->travel_date)->format('d M Y') ?? '—' }}</td>
              <td>{{ $enquiry->travellers ?? '—' }}</td>
              <td>
                @php $map = ['new' => 'badge-amber', 'contacted' => 'badge-blue', 'closed' => 'badge-green']; @endphp
                <span class="badge-status {{ $map[$enquiry->status] ?? 'badge-gray' }}">{{ ucfirst($enquiry->status) }}</span>
              </td>
              <td>{{ $enquiry->created_at->format('d M Y') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $enquiries->links() }}</div>
  @endif
</div>
@endsection
