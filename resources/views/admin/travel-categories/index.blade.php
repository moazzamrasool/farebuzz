
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
                <h3 class="card-title">Travel Categories</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-primary" id="openAddCategoryBtn">+ Add New</button>
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
                      <th>Slug</th>
                      <th>Sort Order</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="categoriesTableBody">
                    @forelse ($travelCategories as $key=>$category)
                    <tr id="category-row-{{ $category->id }}">
                      <td>{{ $travelCategories->firstItem() + $key }}</td>
                      <td class="cell-image">
                        @if($category->image)
                          <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">
                        @else
                          —
                        @endif
                      </td>
                      <td class="cell-name">{{ $category->name }}</td>
                      <td class="cell-slug">{{ $category->slug }}</td>
                      <td class="cell-sort-order">{{ $category->sort_order }}</td>
                      <td class="cell-status">
                        <form action="{{ route('crm.travel-categories.toggle-status', $category->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm {{ $category->status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                            {{ ucfirst($category->status) }}
                          </button>
                        </form>
                      </td>
                      <td>
                        <button type="button" class="btn btn-primary btn-sm btn-edit-category" data-edit-url="{{ route('crm.travel-categories.edit', $category->id) }}">Edit</button>
                        <form action="{{ route('crm.travel-categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this travel category?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr id="noCategoriesRow">
                      <td colspan="7" class="text-center">No travel categories found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                {{ $travelCategories->links() }}
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        </div>
    </div>
</div>

<!-- Add / Edit Category Modal -->
<div class="modal fade zoom-modal" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="categoryForm" action="{{ route('crm.travel-categories.store') }}" method="POST" enctype="multipart/form-data" data-http-method="POST" novalidate>
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="categoryModalLabel">Add Travel Category</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="modal_name">Name</label>
            <input type="text" name="name" id="modal_name" class="form-control" placeholder="e.g. Beaches, Honeymoon, Best Seller" required>
            <span class="invalid-feedback d-block" data-error-for="name"></span>
          </div>

          <div class="form-group">
            <label for="modal_description">Description</label>
            <textarea name="description" id="modal_description" rows="4" class="form-control" placeholder="e.g. Sun, sand and sea &mdash; our best beach getaways."></textarea>
            <span class="invalid-feedback d-block" data-error-for="description"></span>
          </div>

          <div class="form-group">
            <label>Image <small class="text-muted">size: 300x300</small></label>
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

          <div class="form-group">
            <label for="modal_badge_color">Badge Color <small class="text-muted">(shown on package/destination badge tags)</small></label>
            <input type="color" name="badge_color" id="modal_badge_color" class="form-control" value="#0d6efd" style="max-width:100px;height:42px;">
            <span class="invalid-feedback d-block" data-error-for="badge_color"></span>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="modal_status">Status</label>
              <select name="status" id="modal_status" class="form-control">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <span class="invalid-feedback d-block" data-error-for="status"></span>
            </div>
            <div class="form-group col-md-6">
              <label for="modal_sort_order">Sort Order</label>
              <input type="number" name="sort_order" id="modal_sort_order" min="0" class="form-control" value="0">
              <span class="invalid-feedback d-block" data-error-for="sort_order"></span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="categorySubmitBtn">
            <span class="btn-label">Create Category</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(function () {
    var $modal = $('#categoryModal');
    var $form = $('#categoryForm');
    var storeUrl = $form.attr('action');

    function setMode(mode) {
      if (mode === 'add') {
        $('#categoryModalLabel').text('Add Travel Category');
        $('#categorySubmitBtn .btn-label').text('Create Category');
        $form.attr('action', storeUrl).data('http-method', 'POST');
      } else {
        $('#categoryModalLabel').text('Edit Travel Category');
        $('#categorySubmitBtn .btn-label').text('Update Category');
      }
    }

    $('#openAddCategoryBtn').on('click', function () {
      setMode('add');
      $modal.modal('show');
    });

    $(document).on('click', '.btn-edit-category', function () {
      var url = $(this).data('edit-url');
      $.get(url, function (response) {
        if (!response.success) return;
        var category = response.category;
        setMode('edit');
        $form.attr('action', category.update_url).data('http-method', 'PUT');
        $form.find('[name="name"]').val(category.name);
        $form.find('[name="description"]').val(category.description);
        $form.find('[name="badge_color"]').val(category.badge_color || '#0d6efd');
        $form.find('[name="status"]').val(category.status);
        $form.find('[name="sort_order"]').val(category.sort_order);
        if (category.image_url) {
          FBImageUpload.setPreview($form.find('.img-upload'), category.image_url);
        }
        $modal.modal('show');
      });
    });

    FBModalForm.bind({
      form: '#categoryForm',
      modal: '#categoryModal',
      onSuccess: function (response) {
        var category = response.category;
        if ($('#category-row-' + category.id).length) {
          updateCategoryRow(category);
        } else {
          appendCategoryRow(category);
        }
      },
      onReset: function () {
        setMode('add');
      }
    });

    function updateCategoryRow(category) {
      var $row = $('#category-row-' + category.id);
      var safeName = FBModalForm.escapeHtml(category.name);

      $row.find('.cell-image').html(
        category.image_url
          ? '<img src="' + FBModalForm.escapeHtml(category.image_url) + '" alt="' + safeName + '" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">'
          : '—'
      );
      $row.find('.cell-name').text(category.name);
      $row.find('.cell-slug').text(category.slug);
      $row.find('.cell-sort-order').text(category.sort_order);

      $row.addClass('row-just-updated');
      setTimeout(function () { $row.removeClass('row-just-updated'); }, 700);
    }

    function appendCategoryRow(category) {
      $('#noCategoriesRow').remove();

      var safeName = FBModalForm.escapeHtml(category.name);
      var imageCell = category.image_url
        ? '<img src="' + FBModalForm.escapeHtml(category.image_url) + '" alt="' + safeName + '" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">'
        : '—';

      var statusBtnClass = category.status === 'active' ? 'btn-success' : 'btn-secondary';

      var row = '' +
        '<tr id="category-row-' + category.id + '" class="row-just-added">' +
          '<td>#</td>' +
          '<td class="cell-image">' + imageCell + '</td>' +
          '<td class="cell-name">' + safeName + '</td>' +
          '<td class="cell-slug">' + FBModalForm.escapeHtml(category.slug) + '</td>' +
          '<td class="cell-sort-order">' + category.sort_order + '</td>' +
          '<td class="cell-status">' +
            '<form action="' + category.toggle_url + '" method="POST" class="d-inline">' +
              '@csrf' +
              '<button type="submit" class="btn btn-sm ' + statusBtnClass + '">' + category.status_label + '</button>' +
            '</form>' +
          '</td>' +
          '<td>' +
            '<button type="button" class="btn btn-primary btn-sm btn-edit-category" data-edit-url="' + category.edit_url + '">Edit</button> ' +
            '<form action="' + category.destroy_url + '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this travel category?\');">' +
              '@csrf' +
              '<input type="hidden" name="_method" value="DELETE">' +
              '<button type="submit" class="btn btn-danger btn-sm">Delete</button>' +
            '</form>' +
          '</td>' +
        '</tr>';

      $('#categoriesTableBody').prepend(row);

      $('#categoriesTableBody tr').each(function (index) {
        $(this).find('td').eq(0).text(index + 1);
      });
    }
  });
</script>
@endsection
