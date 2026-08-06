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
                <h3 class="card-title">Blog Posts</h3>
                <div class="card-tools">
                  <a href="{{ route('crm.blogs.create') }}" class="btn btn-primary">+ Add New</a>
                </div>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Title</th>
                      <th>Category</th>
                      <th>Status</th>
                      <th>Published Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($blogs as $key => $blog)
                    <tr>
                      <td>{{ $blogs->firstItem() + $key }}</td>
                      <td>{{ $blog->title }}</td>
                      <td>{{ $blog->category_label }}</td>
                      <td>
                        <form action="{{ route('crm.blogs.toggle-status', $blog->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm {{ $blog->status === 'published' ? 'btn-success' : 'btn-secondary' }}">
                            {{ ucfirst($blog->status) }}
                          </button>
                        </form>
                      </td>
                      <td>{{ $blog->published_at?->format('d-m-Y') ?? '—' }}</td>
                      <td>
                        <a href="{{ route('crm.blogs.edit', $blog->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('crm.blogs.destroy', $blog->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this blog post?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="6" class="text-center">No blog posts found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <div class="card-footer">
                {{ $blogs->links() }}
              </div>
            </div>
          </div>
        </div>
        </div>
    </div>
</div>
@endsection
