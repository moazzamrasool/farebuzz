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
                <h3 class="card-title">Navbar Menu</h3>
                <div class="card-tools">
                  <small class="text-muted mr-3">Drag the <i class="fa fa-arrows-alt"></i> handle to reorder.</small>
                  <button type="button" class="btn btn-primary" id="openAddNavItemBtn">+ Add New</button>
                </div>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th style="width:40px"></th>
                      <th>#</th>
                      <th>Label</th>
                      <th>Link Type</th>
                      <th>Links To</th>
                      <th>New Tab</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="navItemsTableBody">
                    @forelse ($items as $key => $item)
                    <tr id="nav-item-row-{{ $item->id }}" data-id="{{ $item->id }}">
                      <td class="text-muted" style="cursor:move"><i class="fa fa-arrows-alt drag-handle"></i></td>
                      <td>{{ $key + 1 }}</td>
                      <td class="cell-label">
                        {{ $item->label }}
                        @if($item->parent)
                          <br><small class="text-muted cell-parent">&#8618; child of {{ $item->parent->label }}</small>
                        @endif
                      </td>
                      <td class="cell-type">{{ ucfirst(str_replace('_', ' ', $item->link_type)) }}</td>
                      <td class="cell-value">{{ $item->link_value }}</td>
                      <td class="cell-newtab">{{ $item->open_in_new_tab ? 'Yes' : 'No' }}</td>
                      <td class="cell-status">
                        <form action="{{ route('crm.navbar-menu.toggle-status', $item) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm {{ $item->status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                            {{ ucfirst($item->status) }}
                          </button>
                        </form>
                      </td>
                      <td>
                        <button type="button" class="btn btn-primary btn-sm btn-edit-nav-item" data-edit-url="{{ route('crm.navbar-menu.edit', $item) }}">Edit</button>
                        <form action="{{ route('crm.navbar-menu.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this navbar item? Any child items under it will be deleted too.');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr id="noNavItemsRow">
                      <td colspan="8" class="text-center">No navbar items found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        </div>
    </div>
</div>

<!-- Add / Edit Navbar Item Modal -->
<div class="modal fade zoom-modal" id="navItemModal" tabindex="-1" role="dialog" aria-labelledby="navItemModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <form id="navItemForm" action="{{ route('crm.navbar-menu.store') }}" method="POST" data-http-method="POST" novalidate>
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="navItemModalLabel">Add Navbar Item</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="ni_label">Label</label>
              <input type="text" name="label" id="ni_label" class="form-control" placeholder="e.g. India Packages" required>
              <span class="invalid-feedback d-block" data-error-for="label"></span>
            </div>
            <div class="form-group col-md-6">
              <label for="ni_parent_id">Parent Item <small class="text-muted">(optional — makes this a dropdown child)</small></label>
              <select name="parent_id" id="ni_parent_id" class="form-control">
                <option value="">— None (top-level) —</option>
                @foreach($parentOptions as $parent)
                  <option value="{{ $parent->id }}">{{ $parent->label }}</option>
                @endforeach
              </select>
              <span class="invalid-feedback d-block" data-error-for="parent_id"></span>
            </div>
          </div>

          <div class="form-group">
            <label for="ni_link_type">Link Type</label>
            <select name="link_type" id="ni_link_type" class="form-control">
              <option value="route">Internal Route</option>
              <option value="cms_page">CMS Page</option>
              <option value="category">Package Category</option>
              <option value="custom">Custom URL</option>
            </select>
            <span class="invalid-feedback d-block" data-error-for="link_type"></span>
          </div>

          <div class="form-group link-value-field" data-for-type="route">
            <label for="ni_link_value_route">Route</label>
            <select id="ni_link_value_route" class="form-control link-value-input" data-link-type="route">
              @foreach($routeOptions as $routeName)
                <option value="{{ $routeName }}">{{ $routeName }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group link-value-field" data-for-type="cms_page" style="display:none;">
            <label for="ni_link_value_cms">CMS Page</label>
            <select id="ni_link_value_cms" class="form-control link-value-input" data-link-type="cms_page">
              <option value="">Select a page</option>
              @foreach($cmsPageOptions as $page)
                <option value="{{ $page->slug }}">{{ $page->title }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group link-value-field" data-for-type="category" style="display:none;">
            <label for="ni_link_value_category">Package Category</label>
            <select id="ni_link_value_category" class="form-control link-value-input" data-link-type="category">
              <option value="">Select a category</option>
              @foreach($categoryOptions as $category)
                <option value="{{ $category->slug }}">{{ $category->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group link-value-field" data-for-type="custom" style="display:none;">
            <label for="ni_link_value_custom">URL</label>
            <input type="text" id="ni_link_value_custom" class="form-control link-value-input" data-link-type="custom" placeholder="https://example.com or /some-path">
          </div>
          <input type="hidden" name="link_value" id="ni_link_value">
          <span class="invalid-feedback d-block" data-error-for="link_value"></span>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label class="d-block">Open in New Tab</label>
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="open_in_new_tab" value="1" class="custom-control-input" id="ni_open_in_new_tab">
                <label class="custom-control-label" for="ni_open_in_new_tab">Yes</label>
              </div>
            </div>
            <div class="form-group col-md-4">
              <label for="ni_status">Status</label>
              <select name="status" id="ni_status" class="form-control">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="form-group col-md-4">
              <label for="ni_sort_order">Sort Order</label>
              <input type="number" name="sort_order" id="ni_sort_order" min="0" class="form-control" value="0">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="navItemSubmitBtn">
            <span class="btn-label">Create Item</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="{{ asset('admin/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script>
  $(function () {
    // Drag-and-drop reorder — identical mechanic to Homepage Sections.
    $('#navItemsTableBody').sortable({
      handle: '.drag-handle',
      axis: 'y',
      update: function () {
        var order = $('#navItemsTableBody tr').map(function () { return $(this).data('id'); }).get();
        $('#navItemsTableBody tr').each(function (i) { $(this).find('td').eq(1).text(i + 1); });
        $.post('{{ route('crm.navbar-menu.reorder') }}', {
          _token: '{{ csrf_token() }}',
          order: order
        });
      }
    });

    // Link Type swaps which link-value control is active; the chosen control's value is
    // mirrored into the single hidden "link_value" input actually submitted with the form.
    function syncLinkValueField() {
      var type = $('#ni_link_type').val();
      $('.link-value-field').hide();
      $('.link-value-field[data-for-type="' + type + '"]').show();
      $('#ni_link_value').val($('.link-value-input[data-link-type="' + type + '"]').val() || '');
    }
    $('#ni_link_type').on('change', syncLinkValueField);
    $(document).on('input change', '.link-value-input', syncLinkValueField);

    var $modal = $('#navItemModal');
    var $form = $('#navItemForm');
    var storeUrl = $form.attr('action');

    function setMode(mode) {
      if (mode === 'add') {
        $('#navItemModalLabel').text('Add Navbar Item');
        $('#navItemSubmitBtn .btn-label').text('Create Item');
        $form.attr('action', storeUrl).data('http-method', 'POST');
      } else {
        $('#navItemModalLabel').text('Edit Navbar Item');
        $('#navItemSubmitBtn .btn-label').text('Update Item');
      }
    }

    $('#openAddNavItemBtn').on('click', function () {
      $form[0].reset();
      setMode('add');
      syncLinkValueField();
      $modal.modal('show');
    });

    $(document).on('click', '.btn-edit-nav-item', function () {
      var url = $(this).data('edit-url');
      $.get(url, function (response) {
        if (!response.success) return;
        var i = response.item;
        setMode('edit');
        $form.attr('action', i.update_url).data('http-method', 'PUT');
        $form.find('[name="label"]').val(i.label);
        $form.find('[name="parent_id"]').val(i.parent_id || '');
        $form.find('[name="status"]').val(i.status);
        $form.find('[name="sort_order"]').val(i.sort_order);
        $form.find('[name="open_in_new_tab"]').prop('checked', !!i.open_in_new_tab);
        $('#ni_link_type').val(i.link_type);
        $('.link-value-input[data-link-type="' + i.link_type + '"]').val(i.link_value);
        syncLinkValueField();
        $modal.modal('show');
      });
    });

    FBModalForm.bind({
      form: '#navItemForm',
      modal: '#navItemModal',
      onSuccess: function () {
        window.location.reload();
      },
      onReset: function () {
        setMode('add');
      }
    });

    syncLinkValueField();
  });
</script>
@endsection
