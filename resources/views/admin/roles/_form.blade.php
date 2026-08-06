@csrf

@php $assignedPermissionIds = $assignedPermissionIds ?? []; @endphp

<div class="form-group">
  <label for="name">Role Name</label>
  <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
    value="{{ old('name', $role->name ?? '') }}" required>
  @error('name') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <label class="d-block mb-0">Permissions</label>
    <div class="custom-control custom-checkbox">
      <input type="checkbox" class="custom-control-input" id="permission-select-all">
      <label class="custom-control-label font-weight-bold" for="permission-select-all">Select All / Deselect All</label>
    </div>
  </div>
  @foreach($permissionGroups as $module => $permissions)
    <div class="card card-outline card-secondary mb-2">
      <div class="card-header py-2 d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0 text-capitalize">{{ str_replace('-', ' ', $module) }}</h6>
        <div class="custom-control custom-checkbox">
          <input type="checkbox" class="custom-control-input permission-select-module" id="permission-select-module-{{ $loop->index }}" data-module="{{ $module }}">
          <label class="custom-control-label" for="permission-select-module-{{ $loop->index }}">Select All</label>
        </div>
      </div>
      <div class="card-body py-2">
        <div class="row">
          @foreach($permissions as $permission)
            <div class="col-md-3 col-6">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                  class="custom-control-input permission-checkbox" data-module="{{ $module }}" id="permission-{{ $permission->id }}"
                  {{ in_array($permission->id, old('permissions', $assignedPermissionIds)) ? 'checked' : '' }}>
                <label class="custom-control-label" for="permission-{{ $permission->id }}">
                  {{ ucfirst(explode('.', $permission->name)[1] ?? $permission->name) }}
                </label>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  @endforeach
  @error('permissions') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<button type="submit" class="btn btn-primary">{{ isset($role) ? 'Update' : 'Create' }} Role</button>
<a href="{{ route('crm.roles.index') }}" class="btn btn-secondary">Cancel</a>

<script>
  (function ($) {
    function syncModuleCheckbox($module) {
      var $boxes = $('.permission-checkbox[data-module="' + $module + '"]');
      var allChecked = $boxes.length > 0 && $boxes.length === $boxes.filter(':checked').length;
      $('.permission-select-module[data-module="' + $module + '"]').prop('checked', allChecked);
    }

    function syncSelectAllCheckbox() {
      var $boxes = $('.permission-checkbox');
      var allChecked = $boxes.length > 0 && $boxes.length === $boxes.filter(':checked').length;
      $('#permission-select-all').prop('checked', allChecked);
    }

    $('#permission-select-all').on('change', function () {
      $('.permission-checkbox, .permission-select-module').prop('checked', this.checked);
    });

    $(document).on('change', '.permission-select-module', function () {
      $('.permission-checkbox[data-module="' + $(this).data('module') + '"]').prop('checked', this.checked);
      syncSelectAllCheckbox();
    });

    $(document).on('change', '.permission-checkbox', function () {
      syncModuleCheckbox($(this).data('module'));
      syncSelectAllCheckbox();
    });

    // Reflect already-checked state (edit page, or old() on a failed submit) on load.
    $('.permission-select-module').each(function () {
      syncModuleCheckbox($(this).data('module'));
    });
    syncSelectAllCheckbox();
  })(jQuery);
</script>
