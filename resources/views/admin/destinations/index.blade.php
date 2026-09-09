
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
                <h3 class="card-title">Destinations</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#destinationsBulkUploadModal"><i class="fa fa-file-excel"></i> Bulk Upload</button>
                  <button type="button" class="btn btn-primary" id="openAddDestinationBtn">+ Add New</button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Image</th>
                      <th>Title</th>
                      <th>Slug</th>
                      <th>Local</th>
                      <th>Country / City</th>
                      <th>Meta</th>
                      <th>Featured</th>
                      <th>Status</th>
                      <th>Created At</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="destinationsTableBody">
                    @forelse ($destinations as $key=>$destination)
                    <tr id="destination-row-{{ $destination->id }}">
                      <td>{{ $destinations->firstItem() + $key }}</td>
                      <td class="cell-image">
                        @if($destination->cover_image)
                          <img src="{{ asset('storage/'.$destination->cover_image) }}" alt="{{ $destination->name }}" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">
                        @else
                          —
                        @endif
                      </td>
                      <td class="cell-name">{{ $destination->name }}</td>
                      <td class="cell-slug">{{ $destination->slug }}</td>
                      <td class="cell-local">{{ ucfirst($destination->local) }}</td>
                      <td class="cell-location">{{ $destination->country }}{{ $destination->city ? ', '.$destination->city : '' }}</td>
                      <td class="cell-meta">{{ \Illuminate\Support\Str::limit($destination->meta, 40) }}</td>
                      <td class="cell-featured">{!! $destination->featured ? '<span class="badge badge-info">Yes</span>' : '<span class="badge badge-secondary">No</span>' !!}</td>
                      <td class="cell-status">
                        <form action="{{ route('crm.destinations.toggle-status', $destination->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm {{ $destination->status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                            {{ ucfirst($destination->status) }}
                          </button>
                        </form>
                      </td>
                      <td class="cell-created-at">{{ $destination->created_at->format('d-m-Y') }}</td>
                      <td>
                        <a href="{{ route('crm.destinations.edit', $destination->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('crm.destinations.destroy', $destination->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this destination?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr id="noDestinationsRow">
                      <td colspan="11" class="text-center">No destinations found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                {{ $destinations->links() }}
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        </div>
    </div>
</div>

@include('admin.partials.bulk-upload-modal', ['module' => 'destinations', 'moduleLabel' => 'Destinations', 'modalId' => 'destinationsBulkUploadModal'])

<!-- Add / Edit Destination Modal -->
<div class="modal fade zoom-modal" id="destinationModal" tabindex="-1" role="dialog" aria-labelledby="destinationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <form id="destinationForm" action="{{ route('crm.destinations.store') }}" method="POST" enctype="multipart/form-data" data-http-method="POST" novalidate>
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="destinationModalLabel">Add Destination</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="dest_name">Name</label>
            <input type="text" name="name" id="dest_name" class="form-control" placeholder="e.g. Goa, Maldives, Manali" required>
            <span class="invalid-feedback d-block" data-error-for="name"></span>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="dest_slug">Slug</label>
              <input type="text" name="slug" id="dest_slug" class="form-control" placeholder="e.g. goa (auto-generated from name if left blank)">
              <span class="invalid-feedback d-block" data-error-for="slug"></span>
            </div>
            <div class="form-group col-md-6">
              <label for="dest_local">Local</label>
              <select name="local" id="dest_local" class="form-control">
                <option value="domestic" selected>Domestic</option>
                <option value="international">International</option>
              </select>
              <span class="invalid-feedback d-block" data-error-for="local"></span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="dest_country">Country</label>
              <input type="text" name="country" id="dest_country" class="form-control" placeholder="e.g. India" required>
              <span class="invalid-feedback d-block" data-error-for="country"></span>
            </div>
            <div class="form-group col-md-6">
              <label for="dest_city">City / Region</label>
              <input type="text" name="city" id="dest_city" class="form-control" placeholder="e.g. Goa">
              <span class="invalid-feedback d-block" data-error-for="city"></span>
            </div>
          </div>

          <div class="form-group">
            <label for="dest_description">Description</label>
            <textarea name="description" id="dest_description" rows="4" class="form-control rich-text-editor" data-editor-height="200" placeholder="e.g. Sun-kissed beaches, vibrant nightlife and Portuguese heritage."></textarea>
            <span class="invalid-feedback d-block" data-error-for="description"></span>
          </div>

          <div class="form-group">
            <label for="dest_meta">Meta</label>
            <textarea name="meta" id="dest_meta" rows="2" class="form-control" placeholder="e.g. SEO meta description for this destination page"></textarea>
            <span class="invalid-feedback d-block" data-error-for="meta"></span>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Cover Image <small class="text-muted">size: 1200x600</small></label>
              <div class="img-upload">
                <input type="file" name="cover_image" class="img-upload-input d-none" accept="image/*">
                <div class="img-upload-box" data-multiple="false">
                  <div class="img-upload-placeholder">
                    <i class="fa fa-cloud-upload-alt"></i>
                    <span>Click or drag an image here</span>
                  </div>
                  <div class="img-upload-preview-wrap"></div>
                  <button type="button" class="img-upload-remove" title="Remove">&times;</button>
                </div>
              </div>
              <span class="invalid-feedback d-block" data-error-for="cover_image"></span>
            </div>
            <div class="form-group col-md-6">
              <label>Gallery Images <small class="text-muted">size: 800x600 each</small></label>
              <div class="img-upload">
                <input type="file" name="gallery_images[]" class="img-upload-input d-none" accept="image/*" multiple>
                <div class="img-upload-box" data-multiple="true">
                  <div class="img-upload-placeholder">
                    <i class="fa fa-images"></i>
                    <span>Click or drag images here</span>
                  </div>
                  <div class="img-upload-preview-wrap"></div>
                </div>
              </div>
              <small class="form-text text-muted">Uploading new gallery images replaces the existing gallery.</small>
              <span class="invalid-feedback d-block" data-error-for="gallery_images"></span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="dest_status">Status</label>
              <select name="status" id="dest_status" class="form-control">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <span class="invalid-feedback d-block" data-error-for="status"></span>
            </div>
            <div class="form-group col-md-4">
              <label for="dest_sort_order">Sort Order</label>
              <input type="number" name="sort_order" id="dest_sort_order" min="0" class="form-control" value="0">
              <span class="invalid-feedback d-block" data-error-for="sort_order"></span>
            </div>
            <div class="form-group col-md-4">
              <label class="d-block">Featured</label>
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="featured" value="1" class="custom-control-input" id="dest_featured">
                <label class="custom-control-label" for="dest_featured">Show as featured</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="destinationSubmitBtn">
            <span class="btn-label">Create Destination</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(function () {
    var $modal = $('#destinationModal');
    var $form = $('#destinationForm');
    var storeUrl = $form.attr('action');

    function resetToAddMode() {
      $('#destinationModalLabel').text('Add Destination');
      $('#destinationSubmitBtn .btn-label').text('Create Destination');
      $form.attr('action', storeUrl).data('http-method', 'POST');
    }

    $('#openAddDestinationBtn').on('click', function () {
      resetToAddMode();
      $modal.modal('show');
    });

    // Editing navigates to the full edit page (crm.destinations.edit) instead of
    // this modal — the modal only ever covers the Add fields (no SEO/long-form
    // content), so it can't safely round-trip an edit without silently leaving
    // those fields untouched-but-invisible to the admin.

    // FBModalForm submits via FormData($form[0]), which only sees the hidden
    // <textarea>'s stale value — CKEditor's own auto-sync only fires on a native,
    // non-AJAX form submit. So sync the editor's current HTML back into the
    // textarea ourselves, bound ahead of FBModalForm's own submit handler below
    // (jQuery runs same-event handlers in bind order) so it's in place before
    // FormData snapshots the form.
    $form.on('submit', function () {
      var editor = $form.find('[name="description"]').data('ckeditor-instance');
      if (editor) {
        editor.updateSourceElement();
      }
    });

    FBModalForm.bind({
      form: '#destinationForm',
      modal: '#destinationModal',
      onSuccess: function (response) {
        appendDestinationRow(response.destination);
      },
      onReset: function () {
        resetToAddMode();
        var descriptionEditor = $form.find('[name="description"]').data('ckeditor-instance');
        if (descriptionEditor) {
          descriptionEditor.setData('');
        }
      }
    });

    function appendDestinationRow(destination) {
      $('#noDestinationsRow').remove();

      var safeName = FBModalForm.escapeHtml(destination.name);
      var imageCell = destination.cover_image_url
        ? '<img src="' + FBModalForm.escapeHtml(destination.cover_image_url) + '" alt="' + safeName + '" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">'
        : '—';
      var statusBtnClass = destination.status === 'active' ? 'btn-success' : 'btn-secondary';
      var featuredBadge = destination.featured
        ? '<span class="badge badge-info">Yes</span>'
        : '<span class="badge badge-secondary">No</span>';

      var row = '' +
        '<tr id="destination-row-' + destination.id + '" class="row-just-added">' +
          '<td>#</td>' +
          '<td class="cell-image">' + imageCell + '</td>' +
          '<td class="cell-name">' + safeName + '</td>' +
          '<td class="cell-slug">' + FBModalForm.escapeHtml(destination.slug) + '</td>' +
          '<td class="cell-local">' + FBModalForm.escapeHtml(destination.local_label) + '</td>' +
          '<td class="cell-location">' + FBModalForm.escapeHtml(destination.location) + '</td>' +
          '<td class="cell-meta">' + FBModalForm.escapeHtml(destination.meta || '') + '</td>' +
          '<td class="cell-featured">' + featuredBadge + '</td>' +
          '<td class="cell-status">' +
            '<form action="' + destination.toggle_url + '" method="POST" class="d-inline">' +
              '@csrf' +
              '<button type="submit" class="btn btn-sm ' + statusBtnClass + '">' + destination.status_label + '</button>' +
            '</form>' +
          '</td>' +
          '<td class="cell-created-at">' + FBModalForm.escapeHtml(destination.created_at) + '</td>' +
          '<td>' +
            '<a href="' + destination.edit_url + '" class="btn btn-primary btn-sm">Edit</a> ' +
            '<form action="' + destination.destroy_url + '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this destination?\');">' +
              '@csrf' +
              '<input type="hidden" name="_method" value="DELETE">' +
              '<button type="submit" class="btn btn-danger btn-sm">Delete</button>' +
            '</form>' +
          '</td>' +
        '</tr>';

      $('#destinationsTableBody').prepend(row);

      $('#destinationsTableBody tr').each(function (index) {
        $(this).find('td').eq(0).text(index + 1);
      });
    }
  });
</script>
@endsection
