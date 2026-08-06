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
                <h3 class="card-title">CMS Pages</h3>
                <div class="card-tools">
                  <a href="{{ route('crm.cms-pages.create') }}" class="btn btn-primary">+ Add New</a>
                </div>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Title</th>
                      <th>Slug</th>
                      <th>Status</th>
                      <th>Updated At</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($cmsPages as $key => $cmsPage)
                    <tr>
                      <td>{{ $cmsPages->firstItem() + $key }}</td>
                      <td>{{ $cmsPage->title }}</td>
                      <td><code>/{{ $cmsPage->slug }}</code></td>
                      <td>
                        <form action="{{ route('crm.cms-pages.toggle-status', $cmsPage->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm {{ $cmsPage->status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                            {{ ucfirst($cmsPage->status) }}
                          </button>
                        </form>
                      </td>
                      <td>{{ $cmsPage->updated_at->format('d-m-Y') }}</td>
                      <td>
                        <a href="{{ route('crm.cms-pages.edit', $cmsPage->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('crm.cms-pages.destroy', $cmsPage->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this page?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="6" class="text-center">No pages found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <div class="card-footer">
                {{ $cmsPages->links() }}
              </div>
            </div>
          </div>
        </div>
        </div>
    </div>
</div>
@endsection
