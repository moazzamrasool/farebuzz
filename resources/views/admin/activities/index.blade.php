
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
                <h3 class="card-title">Activities</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#activitiesBulkUploadModal"><i class="fa fa-file-excel"></i> Bulk Upload</button>
                  <button type="button" class="btn btn-primary" id="openAddActivityBtn">+ Add New</button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>SN.</th>
                      <th>Image</th>
                      <th>Name</th>
                      <th>Destination</th>
                      <th>Category</th>
                      <th>Price</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="activitiesTableBody">
                    @forelse ($activities as $key=>$activity)
                    <tr id="activity-row-{{ $activity->id }}">
                      <td>{{ $activities->firstItem() + $key }}</td>
                      <td class="cell-image">
                        @if($activity->image)
                          <img src="{{ asset('storage/'.$activity->image) }}" alt="{{ $activity->name }}" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">
                        @else
                          —
                        @endif
                      </td>
                      <td class="cell-name">{{ $activity->name }}</td>
                      <td class="cell-destination">{{ $activity->destination->name ?? 'N/A' }}</td>
                      <td class="cell-category">{{ $activity->category ?? 'N/A' }}</td>
                      <td class="cell-price">{{ $activity->price ? number_format($activity->price, 2) : 'N/A' }}</td>
                      <td class="cell-status">
                        <form action="{{ route('crm.activities.toggle-status', $activity->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm {{ $activity->status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                            {{ ucfirst($activity->status) }}
                          </button>
                        </form>
                      </td>
                      <td>
                        <button type="button" class="btn btn-primary btn-sm btn-edit-activity" data-edit-url="{{ route('crm.activities.edit', $activity->id) }}">Edit</button>
                        <form action="{{ route('crm.activities.destroy', $activity->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this activity?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr id="noActivitiesRow">
                      <td colspan="8" class="text-center">No activities found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                {{ $activities->links() }}
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        </div>
    </div>
</div>

@include('admin.partials.bulk-upload-modal', ['module' => 'activities', 'moduleLabel' => 'Activities', 'modalId' => 'activitiesBulkUploadModal'])

<!-- Add / Edit Activity Modal -->
<div class="modal fade zoom-modal" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="activityForm" action="{{ route('crm.activities.store') }}" method="POST" enctype="multipart/form-data" data-http-method="POST" novalidate>
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="activityModalLabel">Add Activity</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="act_destination_id">Destination (optional)</label>
              <select name="destination_id" id="act_destination_id" class="form-control">
                <option value="">None</option>
                @foreach($destinations as $dest)
                  <option value="{{ $dest->id }}">{{ $dest->name }}</option>
                @endforeach
              </select>
              <span class="invalid-feedback d-block" data-error-for="destination_id"></span>
            </div>
            <div class="form-group col-md-6">
              <label for="act_travel_category_id">Package Category (optional)</label>
              <select name="travel_category_id" id="act_travel_category_id" class="form-control">
                <option value="">None</option>
                @foreach($travelCategories as $category)
                  <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
              </select>
              <span class="invalid-feedback d-block" data-error-for="travel_category_id"></span>
            </div>
          </div>

          <div class="form-group">
            <label for="act_name">Name</label>
            <input type="text" name="name" id="act_name" class="form-control" placeholder="e.g. Sunset Cruise, Scuba Diving" required>
            <span class="invalid-feedback d-block" data-error-for="name"></span>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="act_category">Category</label>
              <input type="text" name="category" id="act_category" placeholder="e.g. Adventure, Water Sports, Sightseeing" class="form-control">
              <span class="invalid-feedback d-block" data-error-for="category"></span>
            </div>
            <div class="form-group col-md-6">
              <label for="act_price">Price</label>
              <input type="number" step="0.01" min="0" name="price" id="act_price" class="form-control" placeholder="e.g. 2500">
              <span class="invalid-feedback d-block" data-error-for="price"></span>
            </div>
          </div>

          <div class="form-group">
            <label for="act_duration">Duration</label>
            <input type="text" name="duration" id="act_duration" class="form-control" placeholder="e.g. 3 hours, Half day">
            <span class="invalid-feedback d-block" data-error-for="duration"></span>
          </div>

          <div class="form-group">
            <label for="act_description">Description</label>
            <textarea name="description" id="act_description" rows="4" class="form-control" placeholder="e.g. Enjoy a thrilling water sports session with expert guides."></textarea>
            <span class="invalid-feedback d-block" data-error-for="description"></span>
          </div>

          <div class="form-group">
            <label>Image <small class="text-muted">size: 400x300</small></label>
            <div class="img-upload">
              <input type="file" name="image" class="img-upload-input d-none" accept="image/*">
              <div class="img-upload-box" data-multiple="false">
                <div class="img-upload-placeholder">
                  <i class="fa fa-cloud-upload-alt"></i>
                  <span>Click or drag an image here</span>
                </div>
                <div class="img-upload-preview-wrap"></div>
                <button type="button" class="img-upload-remove" title="Remove">&times;</button>
              </div>
            </div>
            <span class="invalid-feedback d-block" data-error-for="image"></span>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="act_status">Status</label>
              <select name="status" id="act_status" class="form-control">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <span class="invalid-feedback d-block" data-error-for="status"></span>
            </div>
            <div class="form-group col-md-6">
              <label for="act_sort_order">Sort Order</label>
              <input type="number" name="sort_order" id="act_sort_order" min="0" class="form-control" value="0">
              <span class="invalid-feedback d-block" data-error-for="sort_order"></span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="activitySubmitBtn">
            <span class="btn-label">Create Activity</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(function () {
    var $modal = $('#activityModal');
    var $form = $('#activityForm');
    var storeUrl = $form.attr('action');

    function setMode(mode) {
      if (mode === 'add') {
        $('#activityModalLabel').text('Add Activity');
        $('#activitySubmitBtn .btn-label').text('Create Activity');
        $form.attr('action', storeUrl).data('http-method', 'POST');
      } else {
        $('#activityModalLabel').text('Edit Activity');
        $('#activitySubmitBtn .btn-label').text('Update Activity');
      }
    }

    $('#openAddActivityBtn').on('click', function () {
      setMode('add');
      $modal.modal('show');
    });

    $(document).on('click', '.btn-edit-activity', function () {
      var url = $(this).data('edit-url');
      $.get(url, function (response) {
        if (!response.success) return;
        var a = response.activity;
        setMode('edit');
        $form.attr('action', a.update_url).data('http-method', 'PUT');
        $form.find('[name="destination_id"]').val(a.destination_id);
        $form.find('[name="travel_category_id"]').val(a.travel_category_id);
        $form.find('[name="name"]').val(a.name);
        $form.find('[name="category"]').val(a.category);
        $form.find('[name="duration"]').val(a.duration);
        $form.find('[name="price"]').val(a.price);
        $form.find('[name="description"]').val(a.description);
        $form.find('[name="status"]').val(a.status);
        $form.find('[name="sort_order"]').val(a.sort_order);
        if (a.image_url) {
          FBImageUpload.setPreview($form.find('.img-upload'), a.image_url);
        }
        $modal.modal('show');
      });
    });

    FBModalForm.bind({
      form: '#activityForm',
      modal: '#activityModal',
      onSuccess: function (response) {
        var activity = response.activity;
        if ($('#activity-row-' + activity.id).length) {
          updateActivityRow(activity);
        } else {
          appendActivityRow(activity);
        }
      },
      onReset: function () {
        setMode('add');
      }
    });

    function updateActivityRow(activity) {
      var $row = $('#activity-row-' + activity.id);
      var safeName = FBModalForm.escapeHtml(activity.name);

      $row.find('.cell-image').html(
        activity.image_url
          ? '<img src="' + FBModalForm.escapeHtml(activity.image_url) + '" alt="' + safeName + '" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">'
          : '—'
      );
      $row.find('.cell-name').text(activity.name);
      $row.find('.cell-destination').text(activity.destination_name);
      $row.find('.cell-category').text(activity.category);
      $row.find('.cell-price').text(activity.price);

      $row.addClass('row-just-updated');
      setTimeout(function () { $row.removeClass('row-just-updated'); }, 700);
    }

    function appendActivityRow(activity) {
      $('#noActivitiesRow').remove();

      var safeName = FBModalForm.escapeHtml(activity.name);
      var imageCell = activity.image_url
        ? '<img src="' + FBModalForm.escapeHtml(activity.image_url) + '" alt="' + safeName + '" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">'
        : '—';
      var statusBtnClass = activity.status === 'active' ? 'btn-success' : 'btn-secondary';

      var row = '' +
        '<tr id="activity-row-' + activity.id + '" class="row-just-added">' +
          '<td>#</td>' +
          '<td class="cell-image">' + imageCell + '</td>' +
          '<td class="cell-name">' + safeName + '</td>' +
          '<td class="cell-destination">' + FBModalForm.escapeHtml(activity.destination_name) + '</td>' +
          '<td class="cell-category">' + FBModalForm.escapeHtml(activity.category) + '</td>' +
          '<td class="cell-price">' + FBModalForm.escapeHtml(activity.price) + '</td>' +
          '<td class="cell-status">' +
            '<form action="' + activity.toggle_url + '" method="POST" class="d-inline">' +
              '@csrf' +
              '<button type="submit" class="btn btn-sm ' + statusBtnClass + '">' + activity.status_label + '</button>' +
            '</form>' +
          '</td>' +
          '<td>' +
            '<button type="button" class="btn btn-primary btn-sm btn-edit-activity" data-edit-url="' + activity.edit_url + '">Edit</button> ' +
            '<form action="' + activity.destroy_url + '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this activity?\');">' +
              '@csrf' +
              '<input type="hidden" name="_method" value="DELETE">' +
              '<button type="submit" class="btn btn-danger btn-sm">Delete</button>' +
            '</form>' +
          '</td>' +
        '</tr>';

      $('#activitiesTableBody').prepend(row);

      $('#activitiesTableBody tr').each(function (index) {
        $(this).find('td').eq(0).text(index + 1);
      });
    }
  });
</script>
@endsection
