
@extends('layouts.admin.app')

@section('content')
@php $currentAdmin = Auth::guard('admin')->user(); @endphp
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
                <h3 class="card-title">{{ $label }}s</h3>
                <div class="card-tools">
                  @if($currentAdmin?->isAdmin() || $currentAdmin?->can($routeName.'.create'))
                    <button type="button" class="btn btn-primary" id="openAddFeatureBtn">+ Add New</button>
                  @endif
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>SN.</th>
                      <th>Icon</th>
                      <th>Title</th>
                      <th>Sort Order</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="featuresTableBody">
                    @forelse ($features as $key=>$feature)
                    <tr id="feature-row-{{ $feature->id }}">
                      <td>{{ $features->firstItem() + $key }}</td>
                      <td class="cell-icon"><i class="{{ $feature->icon }}"></i> <code>{{ $feature->icon }}</code></td>
                      <td class="cell-title">{{ $feature->title }}</td>
                      <td class="cell-sort-order">{{ $feature->sort_order }}</td>
                      <td class="cell-status">
                        <form action="{{ route('crm.'.$routeName.'.toggle-status', $feature->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm {{ $feature->status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                            {{ ucfirst($feature->status) }}
                          </button>
                        </form>
                      </td>
                      <td>
                        @if($currentAdmin?->isAdmin() || $currentAdmin?->can($routeName.'.edit'))
                          <button type="button" class="btn btn-primary btn-sm btn-edit-feature" data-edit-url="{{ route('crm.'.$routeName.'.edit', $feature->id) }}">Edit</button>
                        @endif
                        @if($currentAdmin?->isAdmin() || $currentAdmin?->can($routeName.'.delete'))
                          <form action="{{ route('crm.'.$routeName.'.destroy', $feature->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this {{ strtolower($label) }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                          </form>
                        @endif
                      </td>
                    </tr>
                    @empty
                    <tr id="noFeaturesRow">
                      <td colspan="6" class="text-center">No {{ strtolower($label) }}s found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                {{ $features->links() }}
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        </div>
    </div>
</div>

<!-- Add / Edit Feature Modal -->
<div class="modal fade zoom-modal" id="featureModal" tabindex="-1" role="dialog" aria-labelledby="featureModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="featureForm" action="{{ route('crm.'.$routeName.'.store') }}" method="POST" data-http-method="POST" novalidate>
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="featureModalLabel">Add {{ $label }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="feat_title">Title</label>
            <input type="text" name="title" id="feat_title" class="form-control"
              placeholder="{{ $routeName === 'inclusions' ? 'e.g. Return flights, Daily breakfast' : 'e.g. Personal expenses, Travel insurance' }}" required>
            <span class="invalid-feedback d-block" data-error-for="title"></span>
          </div>

          <div class="form-group">
            <label for="feat_icon">Icon <small class="text-muted">(Bootstrap Icons class, e.g. bi-check-circle-fill &mdash; <a href="https://icons.getbootstrap.com/" target="_blank" rel="noopener">browse &amp; copy icon names</a>)</small></label>
            <input type="text" name="icon" id="feat_icon" class="form-control" placeholder="e.g. bi-check-circle-fill">
            <span class="invalid-feedback d-block" data-error-for="icon"></span>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="feat_status">Status</label>
              <select name="status" id="feat_status" class="form-control">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <span class="invalid-feedback d-block" data-error-for="status"></span>
            </div>
            <div class="form-group col-md-6">
              <label for="feat_sort_order">Sort Order</label>
              <input type="number" name="sort_order" id="feat_sort_order" min="0" class="form-control" value="0">
              <span class="invalid-feedback d-block" data-error-for="sort_order"></span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="featureSubmitBtn">
            <span class="btn-label">Create {{ $label }}</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(function () {
    var $modal = $('#featureModal');
    var $form = $('#featureForm');
    var storeUrl = $form.attr('action');
    var label = @json($label);

    function setMode(mode) {
      if (mode === 'add') {
        $('#featureModalLabel').text('Add ' + label);
        $('#featureSubmitBtn .btn-label').text('Create ' + label);
        $form.attr('action', storeUrl).data('http-method', 'POST');
      } else {
        $('#featureModalLabel').text('Edit ' + label);
        $('#featureSubmitBtn .btn-label').text('Update ' + label);
      }
    }

    $('#openAddFeatureBtn').on('click', function () {
      setMode('add');
      $modal.modal('show');
    });

    $(document).on('click', '.btn-edit-feature', function () {
      var url = $(this).data('edit-url');
      $.get(url, function (response) {
        if (!response.success) return;
        var f = response.feature;
        setMode('edit');
        $form.attr('action', f.update_url).data('http-method', 'PUT');
        $form.find('[name="title"]').val(f.title);
        $form.find('[name="icon"]').val(f.icon);
        $form.find('[name="status"]').val(f.status);
        $form.find('[name="sort_order"]').val(f.sort_order);
        $modal.modal('show');
      });
    });

    FBModalForm.bind({
      form: '#featureForm',
      modal: '#featureModal',
      onSuccess: function (response) {
        var feature = response.feature;
        if ($('#feature-row-' + feature.id).length) {
          updateFeatureRow(feature);
        } else {
          appendFeatureRow(feature);
        }
      },
      onReset: function () {
        setMode('add');
      }
    });

    function updateFeatureRow(feature) {
      var $row = $('#feature-row-' + feature.id);
      $row.find('.cell-icon').html('<i class="' + FBModalForm.escapeHtml(feature.icon || '') + '"></i> <code>' + FBModalForm.escapeHtml(feature.icon || '') + '</code>');
      $row.find('.cell-title').text(feature.title);
      $row.find('.cell-sort-order').text(feature.sort_order);

      $row.addClass('row-just-updated');
      setTimeout(function () { $row.removeClass('row-just-updated'); }, 700);
    }

    function appendFeatureRow(feature) {
      $('#noFeaturesRow').remove();

      var statusBtnClass = feature.status === 'active' ? 'btn-success' : 'btn-secondary';

      var row = '' +
        '<tr id="feature-row-' + feature.id + '" class="row-just-added">' +
          '<td>#</td>' +
          '<td class="cell-icon"><i class="' + FBModalForm.escapeHtml(feature.icon || '') + '"></i> <code>' + FBModalForm.escapeHtml(feature.icon || '') + '</code></td>' +
          '<td class="cell-title">' + FBModalForm.escapeHtml(feature.title) + '</td>' +
          '<td class="cell-sort-order">' + feature.sort_order + '</td>' +
          '<td class="cell-status">' +
            '<form action="' + feature.toggle_url + '" method="POST" class="d-inline">' +
              '@csrf' +
              '<button type="submit" class="btn btn-sm ' + statusBtnClass + '">' + feature.status_label + '</button>' +
            '</form>' +
          '</td>' +
          '<td>' +
            '<button type="button" class="btn btn-primary btn-sm btn-edit-feature" data-edit-url="' + feature.edit_url + '">Edit</button> ' +
            '<form action="' + feature.destroy_url + '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this ' + label.toLowerCase() + '?\');">' +
              '@csrf' +
              '<input type="hidden" name="_method" value="DELETE">' +
              '<button type="submit" class="btn btn-danger btn-sm">Delete</button>' +
            '</form>' +
          '</td>' +
        '</tr>';

      $('#featuresTableBody').prepend(row);

      $('#featuresTableBody tr').each(function (index) {
        $(this).find('td').eq(0).text(index + 1);
      });
    }
  });
</script>
@endsection
