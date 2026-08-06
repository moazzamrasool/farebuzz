
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
                <h3 class="card-title">Packages</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-primary" id="openAddPackageBtn">+ Add New</button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Image</th>
                      <th>Name</th>
                      <th>Slug</th>
                      <th>Category</th>
                      <th>Type</th>
                      <th>Meta</th>
                      <th>Status</th>
                      <th>Created At</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="packagesTableBody">
                    @forelse ($packages as $key=>$package)
                    <tr id="package-row-{{ $package->id }}">
                      <td>{{ $packages->firstItem() + $key }}</td>
                      <td class="cell-image">
                        @if($package->image)
                          <img src="{{ asset('storage/'.$package->image) }}" alt="{{ $package->name }}" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">
                        @else
                          —
                        @endif
                      </td>
                      <td class="cell-name">{{ $package->name }}</td>
                      <td class="cell-slug">{{ $package->slug }}</td>
                      <td class="cell-category">{{ $package->travelCategory->name ?? 'N/A' }}</td>
                      <td class="cell-type">{{ ucfirst($package->type) }}</td>
                      <td class="cell-meta">{{ \Illuminate\Support\Str::limit($package->meta, 40) }}</td>
                      <td class="cell-status">
                        <form action="{{ route('crm.packages.toggle-status', $package->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm {{ $package->status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                            {{ ucfirst($package->status) }}
                          </button>
                        </form>
                      </td>
                      <td class="cell-created-at">{{ $package->created_at->format('d-m-Y') }}</td>
                      <td>
                        <button type="button" class="btn btn-primary btn-sm btn-edit-package" data-edit-url="{{ route('crm.packages.edit', $package->id) }}">Edit</button>
                        <form action="{{ route('crm.packages.destroy', $package->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this package?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr id="noPackagesRow">
                      <td colspan="10" class="text-center">No packages found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                {{ $packages->links() }}
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        </div>
    </div>
</div>

<!-- Add / Edit Package Modal -->
<div class="modal fade zoom-modal" id="packageModal" tabindex="-1" role="dialog" aria-labelledby="packageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <form id="packageForm" action="{{ route('crm.packages.store') }}" method="POST" enctype="multipart/form-data" data-http-method="POST" novalidate>
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="packageModalLabel">Add Package</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="pkg_travel_category_id">Package Category</label>
              <select name="travel_category_id" id="pkg_travel_category_id" class="form-control">
                <option value="">Select a category</option>
                @foreach($travelCategories as $category)
                  <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
              </select>
              <span class="invalid-feedback d-block" data-error-for="travel_category_id"></span>
            </div>
            <div class="form-group col-md-6">
              <label for="pkg_type">Package Type</label>
              <select name="type" id="pkg_type" class="form-control">
                <option value="domestic" selected>Domestic</option>
                <option value="international">International</option>
              </select>
              <span class="invalid-feedback d-block" data-error-for="type"></span>
            </div>
          </div>

          <div class="form-group">
            <label for="pkg_name">Name</label>
            <input type="text" name="name" id="pkg_name" class="form-control" placeholder="e.g. India Beach Packages, Honeymoon Specials" required>
            <span class="invalid-feedback d-block" data-error-for="name"></span>
          </div>

          <div class="form-group">
            <label for="pkg_slug">Slug</label>
            <input type="text" name="slug" id="pkg_slug" class="form-control" placeholder="e.g. india-beach-packages (auto-generated from name if left blank)">
            <span class="invalid-feedback d-block" data-error-for="slug"></span>
          </div>

          <div class="form-group">
            <label for="pkg_description">Description</label>
            <textarea name="description" id="pkg_description" rows="4" class="form-control" placeholder="e.g. Curated beach packages across India for every traveller."></textarea>
            <span class="invalid-feedback d-block" data-error-for="description"></span>
          </div>

          <div class="form-group">
            <label for="pkg_meta">Meta</label>
            <textarea name="meta" id="pkg_meta" rows="2" class="form-control" placeholder="e.g. SEO meta description for this package listing"></textarea>
            <span class="invalid-feedback d-block" data-error-for="meta"></span>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Image <small class="text-muted">size: 216x270</small></label>
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
            <div class="form-group col-md-6">
              <label>Banner Image <small class="text-muted">size: 1366x396</small></label>
              <div class="img-upload">
                <input type="file" name="banner_image" class="img-upload-input d-none" accept="image/*">
                <div class="img-upload-box" data-multiple="false">
                  <div class="img-upload-placeholder">
                    <i class="fa fa-cloud-upload-alt"></i>
                    <span>Click or drag a banner image here</span>
                  </div>
                  <div class="img-upload-preview-wrap"></div>
                  <button type="button" class="img-upload-remove" title="Remove">&times;</button>
                </div>
              </div>
              <span class="invalid-feedback d-block" data-error-for="banner_image"></span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="pkg_status">Status</label>
              <select name="status" id="pkg_status" class="form-control">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <span class="invalid-feedback d-block" data-error-for="status"></span>
            </div>
            <div class="form-group col-md-6">
              <label for="pkg_sort_order">Sort Order</label>
              <input type="number" name="sort_order" id="pkg_sort_order" min="0" class="form-control" value="0">
              <span class="invalid-feedback d-block" data-error-for="sort_order"></span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="packageSubmitBtn">
            <span class="btn-label">Create Package</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(function () {
    var $modal = $('#packageModal');
    var $form = $('#packageForm');
    var storeUrl = $form.attr('action');

    function setMode(mode) {
      if (mode === 'add') {
        $('#packageModalLabel').text('Add Package');
        $('#packageSubmitBtn .btn-label').text('Create Package');
        $form.attr('action', storeUrl).data('http-method', 'POST');
      } else {
        $('#packageModalLabel').text('Edit Package');
        $('#packageSubmitBtn .btn-label').text('Update Package');
      }
    }

    $('#openAddPackageBtn').on('click', function () {
      setMode('add');
      $modal.modal('show');
    });

    $(document).on('click', '.btn-edit-package', function () {
      var url = $(this).data('edit-url');
      $.get(url, function (response) {
        if (!response.success) return;
        var p = response.package;
        setMode('edit');
        $form.attr('action', p.update_url).data('http-method', 'PUT');
        $form.find('[name="travel_category_id"]').val(p.travel_category_id);
        $form.find('[name="type"]').val(p.type);
        $form.find('[name="name"]').val(p.name);
        $form.find('[name="slug"]').val(p.slug);
        $form.find('[name="description"]').val(p.description);
        $form.find('[name="meta"]').val(p.meta);
        $form.find('[name="status"]').val(p.status);
        $form.find('[name="sort_order"]').val(p.sort_order);

        var $image = $form.find('input[name="image"]').closest('.img-upload');
        if (p.image_url) {
          FBImageUpload.setPreview($image, p.image_url);
        }
        var $banner = $form.find('input[name="banner_image"]').closest('.img-upload');
        if (p.banner_image_url) {
          FBImageUpload.setPreview($banner, p.banner_image_url);
        }

        $modal.modal('show');
      });
    });

    FBModalForm.bind({
      form: '#packageForm',
      modal: '#packageModal',
      onSuccess: function (response) {
        var pkg = response.package;
        if ($('#package-row-' + pkg.id).length) {
          updatePackageRow(pkg);
        } else {
          appendPackageRow(pkg);
        }
      },
      onReset: function () {
        setMode('add');
      }
    });

    function updatePackageRow(pkg) {
      var $row = $('#package-row-' + pkg.id);
      var safeName = FBModalForm.escapeHtml(pkg.name);

      $row.find('.cell-image').html(
        pkg.image_url
          ? '<img src="' + FBModalForm.escapeHtml(pkg.image_url) + '" alt="' + safeName + '" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">'
          : '—'
      );
      $row.find('.cell-name').text(pkg.name);
      $row.find('.cell-slug').text(pkg.slug);
      $row.find('.cell-category').text(pkg.category_name);
      $row.find('.cell-type').text(pkg.type_label);
      $row.find('.cell-meta').text(pkg.meta);

      $row.addClass('row-just-updated');
      setTimeout(function () { $row.removeClass('row-just-updated'); }, 700);
    }

    function appendPackageRow(pkg) {
      $('#noPackagesRow').remove();

      var safeName = FBModalForm.escapeHtml(pkg.name);
      var imageCell = pkg.image_url
        ? '<img src="' + FBModalForm.escapeHtml(pkg.image_url) + '" alt="' + safeName + '" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">'
        : '—';
      var statusBtnClass = pkg.status === 'active' ? 'btn-success' : 'btn-secondary';

      var row = '' +
        '<tr id="package-row-' + pkg.id + '" class="row-just-added">' +
          '<td>#</td>' +
          '<td class="cell-image">' + imageCell + '</td>' +
          '<td class="cell-name">' + safeName + '</td>' +
          '<td class="cell-slug">' + FBModalForm.escapeHtml(pkg.slug) + '</td>' +
          '<td class="cell-category">' + FBModalForm.escapeHtml(pkg.category_name) + '</td>' +
          '<td class="cell-type">' + FBModalForm.escapeHtml(pkg.type_label) + '</td>' +
          '<td class="cell-meta">' + FBModalForm.escapeHtml(pkg.meta || '') + '</td>' +
          '<td class="cell-status">' +
            '<form action="' + pkg.toggle_url + '" method="POST" class="d-inline">' +
              '@csrf' +
              '<button type="submit" class="btn btn-sm ' + statusBtnClass + '">' + pkg.status_label + '</button>' +
            '</form>' +
          '</td>' +
          '<td class="cell-created-at">' + FBModalForm.escapeHtml(pkg.created_at) + '</td>' +
          '<td>' +
            '<button type="button" class="btn btn-primary btn-sm btn-edit-package" data-edit-url="' + pkg.edit_url + '">Edit</button> ' +
            '<form action="' + pkg.destroy_url + '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this package?\');">' +
              '@csrf' +
              '<input type="hidden" name="_method" value="DELETE">' +
              '<button type="submit" class="btn btn-danger btn-sm">Delete</button>' +
            '</form>' +
          '</td>' +
        '</tr>';

      $('#packagesTableBody').prepend(row);

      $('#packagesTableBody tr').each(function (index) {
        $(this).find('td').eq(0).text(index + 1);
      });
    }
  });
</script>
@endsection
