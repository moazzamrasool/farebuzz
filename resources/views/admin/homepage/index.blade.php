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
                <h3 class="card-title">Homepage Sections</h3>
                <div class="card-tools">
                  <small class="text-muted">Drag the <i class="fa fa-arrows-alt"></i> handle to reorder how sections appear on the homepage.</small>
                </div>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th style="width:40px"></th>
                      <th>#</th>
                      <th>Section</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="sectionsTableBody">
                    @foreach ($sections as $key => $section)
                    <tr id="section-row-{{ $section->id }}" data-id="{{ $section->id }}">
                      <td class="text-muted" style="cursor:move"><i class="fa fa-arrows-alt drag-handle"></i></td>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ $section->name }}</td>
                      <td>
                        <form action="{{ route('crm.homepage-sections.toggle-status', $section->key) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm {{ $section->status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                            {{ ucfirst($section->status) }}
                          </button>
                        </form>
                      </td>
                      <td>
                        <a href="{{ route('crm.homepage-sections.edit', $section->key) }}" class="btn btn-primary btn-sm">Edit Section</a>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        </div>
    </div>
</div>

<script src="{{ asset('admin/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script>
  $(function () {
    $('#sectionsTableBody').sortable({
      handle: '.drag-handle',
      axis: 'y',
      update: function () {
        var order = $('#sectionsTableBody tr').map(function () { return $(this).data('id'); }).get();
        $('#sectionsTableBody tr').each(function (i) { $(this).find('td').eq(1).text(i + 1); });
        $.post('{{ route('crm.homepage-sections.reorder') }}', {
          _token: '{{ csrf_token() }}',
          order: order
        });
      }
    });
  });
</script>
@endsection
