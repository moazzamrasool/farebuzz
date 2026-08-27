
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
                <h3 class="card-title">Activity Categories</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-primary" id="openAddActivityCategoryBtn">+ Add New</button>
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
                      <th>Slug</th>
                      <th>Sort Order</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="activityCategoriesTableBody">
                    @forelse ($activityCategories as $key=>$category)
                    <tr id="activity-category-row-{{ $category->id }}">
                      <td>{{ $activityCategories->firstItem() + $key }}</td>
                      <td class="cell-icon">
                        @if($category->icon)
                          <i class="bi {{ $category->icon }}"></i>
                        @else
                          —
                        @endif
                      </td>
                      <td class="cell-name">{{ $category->name }}</td>
                      <td class="cell-slug">{{ $category->slug }}</td>
                      <td class="cell-sort-order">{{ $category->sort_order }}</td>
                      <td class="cell-status">
                        <form action="{{ route('crm.activity-categories.toggle-status', $category->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm {{ $category->status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                            {{ ucfirst($category->status) }}
                          </button>
                        </form>
                      </td>
                      <td>
                        <button type="button" class="btn btn-primary btn-sm btn-edit-activity-category" data-edit-url="{{ route('crm.activity-categories.edit', $category->id) }}">Edit</button>
                        <form action="{{ route('crm.activity-categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this activity category?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr id="noActivityCategoriesRow">
                      <td colspan="7" class="text-center">No activity categories found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                {{ $activityCategories->links() }}
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        </div>
    </div>
</div>

<!-- Add / Edit Activity Category Modal -->
<div class="modal fade zoom-modal" id="activityCategoryModal" tabindex="-1" role="dialog" aria-labelledby="activityCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="activityCategoryForm" action="{{ route('crm.activity-categories.store') }}" method="POST" data-http-method="POST" novalidate>
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="activityCategoryModalLabel">Add Activity Category</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="modal_name">Name</label>
            <input type="text" name="name" id="modal_name" class="form-control" placeholder="e.g. Adventure, Water Sports, Sightseeing" required>
            <span class="invalid-feedback d-block" data-error-for="name"></span>
          </div>

          <div class="form-group">
            <label for="modal_description">Description</label>
            <textarea name="description" id="modal_description" rows="4" class="form-control" placeholder="e.g. Adrenaline-fuelled experiences like scuba diving and trekking."></textarea>
            <span class="invalid-feedback d-block" data-error-for="description"></span>
          </div>

          <div class="form-group">
            <label for="modal_icon">Icon <small class="text-muted">(<a href="https://icons.getbootstrap.com/" target="_blank" rel="noopener">Bootstrap Icons</a> class, e.g. bi-water)</small></label>
            <input type="text" name="icon" id="modal_icon" class="form-control" placeholder="e.g. bi-water">
            <span class="invalid-feedback d-block" data-error-for="icon"></span>
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
          <button type="submit" class="btn btn-primary" id="activityCategorySubmitBtn">
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
    var $modal = $('#activityCategoryModal');
    var $form = $('#activityCategoryForm');
    var storeUrl = $form.attr('action');

    function setMode(mode) {
      if (mode === 'add') {
        $('#activityCategoryModalLabel').text('Add Activity Category');
        $('#activityCategorySubmitBtn .btn-label').text('Create Category');
        $form.attr('action', storeUrl).data('http-method', 'POST');
      } else {
        $('#activityCategoryModalLabel').text('Edit Activity Category');
        $('#activityCategorySubmitBtn .btn-label').text('Update Category');
      }
    }

    $('#openAddActivityCategoryBtn').on('click', function () {
      setMode('add');
      $form[0].reset();
      $modal.modal('show');
    });

    $(document).on('click', '.btn-edit-activity-category', function () {
      var url = $(this).data('edit-url');
      $.get(url, function (response) {
        if (!response.success) return;
        var category = response.category;
        setMode('edit');
        $form.attr('action', category.update_url).data('http-method', 'PUT');
        $form.find('[name="name"]').val(category.name);
        $form.find('[name="description"]').val(category.description);
        $form.find('[name="icon"]').val(category.icon);
        $form.find('[name="status"]').val(category.status);
        $form.find('[name="sort_order"]').val(category.sort_order);
        $modal.modal('show');
      });
    });

    FBModalForm.bind({
      form: '#activityCategoryForm',
      modal: '#activityCategoryModal',
      onSuccess: function (response) {
        var category = response.category;
        if ($('#activity-category-row-' + category.id).length) {
          updateCategoryRow(category);
        } else {
          appendCategoryRow(category);
        }
      },
      onReset: function () {
        setMode('add');
      }
    });

    function iconCell(category) {
      return category.icon
        ? '<i class="bi ' + FBModalForm.escapeHtml(category.icon) + '"></i>'
        : '—';
    }

    function updateCategoryRow(category) {
      var $row = $('#activity-category-row-' + category.id);

      $row.find('.cell-icon').html(iconCell(category));
      $row.find('.cell-name').text(category.name);
      $row.find('.cell-slug').text(category.slug);
      $row.find('.cell-sort-order').text(category.sort_order);

      $row.addClass('row-just-updated');
      setTimeout(function () { $row.removeClass('row-just-updated'); }, 700);
    }

    function appendCategoryRow(category) {
      $('#noActivityCategoriesRow').remove();

      var safeName = FBModalForm.escapeHtml(category.name);
      var statusBtnClass = category.status === 'active' ? 'btn-success' : 'btn-secondary';

      var row = '' +
        '<tr id="activity-category-row-' + category.id + '" class="row-just-added">' +
          '<td>#</td>' +
          '<td class="cell-icon">' + iconCell(category) + '</td>' +
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
            '<button type="button" class="btn btn-primary btn-sm btn-edit-activity-category" data-edit-url="' + category.edit_url + '">Edit</button> ' +
            '<form action="' + category.destroy_url + '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this activity category?\');">' +
              '@csrf' +
              '<input type="hidden" name="_method" value="DELETE">' +
              '<button type="submit" class="btn btn-danger btn-sm">Delete</button>' +
            '</form>' +
          '</td>' +
        '</tr>';

      $('#activityCategoriesTableBody').prepend(row);

      $('#activityCategoriesTableBody tr').each(function (index) {
        $(this).find('td').eq(0).text(index + 1);
      });
    }
  });
</script>
@endsection
