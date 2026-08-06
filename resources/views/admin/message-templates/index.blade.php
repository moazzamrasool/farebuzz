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
                <h3 class="card-title">Message Templates</h3>
                <div class="card-tools">
                  <a href="{{ route('crm.message-templates.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Template</a>
                </div>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Channel</th>
                      <th>Subject</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($messageTemplates as $template)
                    <tr>
                      <td>{{ $template->name }}</td>
                      <td><span class="badge {{ $template->channel === 'whatsapp' ? 'badge-success' : 'badge-info' }}">{{ ucfirst($template->channel) }}</span></td>
                      <td>{{ $template->subject ?? '—' }}</td>
                      <td>
                        <span class="badge {{ $template->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $template->is_active ? 'Active' : 'Inactive' }}</span>
                      </td>
                      <td>
                        <a href="{{ route('crm.message-templates.edit', $template->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('crm.message-templates.destroy', $template->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this template?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="5" class="text-center">No message templates yet.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <div class="card-footer">
                {{ $messageTemplates->links() }}
              </div>
            </div>
          </div>
        </div>
        </div>
    </div>
</div>
@endsection
