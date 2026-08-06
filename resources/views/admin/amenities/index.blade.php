
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
                <h3 class="card-title">Amenities</h3>
                <div class="card-tools">
                  @if($currentAdmin?->isAdmin() || $currentAdmin?->can('amenities.create'))
                    <button type="button" class="btn btn-primary" id="openAddAmenityBtn">+ Add New</button>
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
                      <th>Name</th>
                      <th>Sort Order</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="amenitiesTableBody">
                    @forelse ($amenities as $key=>$amenity)
                    <tr id="amenity-row-{{ $amenity->id }}">
                      <td>{{ $amenities->firstItem() + $key }}</td>
                      <td class="cell-icon"><i class="{{ $amenity->icon }}"></i> <code>{{ $amenity->icon }}</code></td>
                      <td class="cell-name">{{ $amenity->name }}</td>
                      <td class="cell-sort-order">{{ $amenity->sort_order }}</td>
                      <td class="cell-status">
                        <form action="{{ route('crm.amenities.toggle-status', $amenity->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm {{ $amenity->status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                            {{ ucfirst($amenity->status) }}
                          </button>
                        </form>
                      </td>
                      <td>
                        @if($currentAdmin?->isAdmin() || $currentAdmin?->can('amenities.edit'))
                          <button type="button" class="btn btn-primary btn-sm btn-edit-amenity" data-edit-url="{{ route('crm.amenities.edit', $amenity->id) }}">Edit</button>
                        @endif
                        @if($currentAdmin?->isAdmin() || $currentAdmin?->can('amenities.delete'))
                          <form action="{{ route('crm.amenities.destroy', $amenity->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this amenity?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                          </form>
                        @endif
                      </td>
                    </tr>
                    @empty
                    <tr id="noAmenitiesRow">
                      <td colspan="6" class="text-center">No amenities found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                {{ $amenities->links() }}
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        </div>
    </div>
</div>

<!-- Add / Edit Amenity Modal -->
<div class="modal fade zoom-modal" id="amenityModal" tabindex="-1" role="dialog" aria-labelledby="amenityModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="amenityForm" action="{{ route('crm.amenities.store') }}" method="POST" data-http-method="POST" novalidate>
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="amenityModalLabel">Add Amenity</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="am_name">Name</label>
            <input type="text" name="name" id="am_name" class="form-control" placeholder="e.g. Free WiFi, Pool, Parking" required>
            <span class="invalid-feedback d-block" data-error-for="name"></span>
          </div>

          <div class="form-group">
            <label for="am_icon">Icon <small class="text-muted">(Bootstrap Icons class, e.g. bi-wifi &mdash; <a href="https://icons.getbootstrap.com/" target="_blank" rel="noopener">browse &amp; copy icon names</a>)</small></label>
            <input type="text" name="icon" id="am_icon" class="form-control" placeholder="e.g. bi-wifi">
            <span class="invalid-feedback d-block" data-error-for="icon"></span>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="am_status">Status</label>
              <select name="status" id="am_status" class="form-control">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <span class="invalid-feedback d-block" data-error-for="status"></span>
            </div>
            <div class="form-group col-md-6">
              <label for="am_sort_order">Sort Order</label>
              <input type="number" name="sort_order" id="am_sort_order" min="0" class="form-control" value="0">
              <span class="invalid-feedback d-block" data-error-for="sort_order"></span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="amenitySubmitBtn">
            <span class="btn-label">Create Amenity</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(function () {
    var $modal = $('#amenityModal');
    var $form = $('#amenityForm');
    var storeUrl = $form.attr('action');

    function setMode(mode) {
      if (mode === 'add') {
        $('#amenityModalLabel').text('Add Amenity');
        $('#amenitySubmitBtn .btn-label').text('Create Amenity');
        $form.attr('action', storeUrl).data('http-method', 'POST');
      } else {
        $('#amenityModalLabel').text('Edit Amenity');
        $('#amenitySubmitBtn .btn-label').text('Update Amenity');
      }
    }

    $('#openAddAmenityBtn').on('click', function () {
      setMode('add');
      $modal.modal('show');
    });

    $(document).on('click', '.btn-edit-amenity', function () {
      var url = $(this).data('edit-url');
      $.get(url, function (response) {
        if (!response.success) return;
        var a = response.amenity;
        setMode('edit');
        $form.attr('action', a.update_url).data('http-method', 'PUT');
        $form.find('[name="name"]').val(a.name);
        $form.find('[name="icon"]').val(a.icon);
        $form.find('[name="status"]').val(a.status);
        $form.find('[name="sort_order"]').val(a.sort_order);
        $modal.modal('show');
      });
    });

    FBModalForm.bind({
      form: '#amenityForm',
      modal: '#amenityModal',
      onSuccess: function (response) {
        var amenity = response.amenity;
        if ($('#amenity-row-' + amenity.id).length) {
          updateAmenityRow(amenity);
        } else {
          appendAmenityRow(amenity);
        }
      },
      onReset: function () {
        setMode('add');
      }
    });

    function updateAmenityRow(amenity) {
      var $row = $('#amenity-row-' + amenity.id);
      $row.find('.cell-icon').html('<i class="' + FBModalForm.escapeHtml(amenity.icon || '') + '"></i> <code>' + FBModalForm.escapeHtml(amenity.icon || '') + '</code>');
      $row.find('.cell-name').text(amenity.name);
      $row.find('.cell-sort-order').text(amenity.sort_order);

      $row.addClass('row-just-updated');
      setTimeout(function () { $row.removeClass('row-just-updated'); }, 700);
    }

    function appendAmenityRow(amenity) {
      $('#noAmenitiesRow').remove();

      var statusBtnClass = amenity.status === 'active' ? 'btn-success' : 'btn-secondary';

      var row = '' +
        '<tr id="amenity-row-' + amenity.id + '" class="row-just-added">' +
          '<td>#</td>' +
          '<td class="cell-icon"><i class="' + FBModalForm.escapeHtml(amenity.icon || '') + '"></i> <code>' + FBModalForm.escapeHtml(amenity.icon || '') + '</code></td>' +
          '<td class="cell-name">' + FBModalForm.escapeHtml(amenity.name) + '</td>' +
          '<td class="cell-sort-order">' + amenity.sort_order + '</td>' +
          '<td class="cell-status">' +
            '<form action="' + amenity.toggle_url + '" method="POST" class="d-inline">' +
              '@csrf' +
              '<button type="submit" class="btn btn-sm ' + statusBtnClass + '">' + amenity.status_label + '</button>' +
            '</form>' +
          '</td>' +
          '<td>' +
            '<button type="button" class="btn btn-primary btn-sm btn-edit-amenity" data-edit-url="' + amenity.edit_url + '">Edit</button> ' +
            '<form action="' + amenity.destroy_url + '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this amenity?\');">' +
              '@csrf' +
              '<input type="hidden" name="_method" value="DELETE">' +
              '<button type="submit" class="btn btn-danger btn-sm">Delete</button>' +
            '</form>' +
          '</td>' +
        '</tr>';

      $('#amenitiesTableBody').prepend(row);

      $('#amenitiesTableBody tr').each(function (index) {
        $(this).find('td').eq(0).text(index + 1);
      });
    }
  });
</script>
@endsection
