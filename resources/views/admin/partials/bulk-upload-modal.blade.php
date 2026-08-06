{{-- Shared bulk-upload modal. Include with @include('admin.partials.bulk-upload-modal', ['module' => 'destinations', 'moduleLabel' => 'Destinations']) --}}
@php
    $modalId = $modalId ?? (\Illuminate\Support\Str::camel($module).'BulkUploadModal');
    $formId = $formId ?? (\Illuminate\Support\Str::camel($module).'BulkUploadForm');
    $accept = $accept ?? '.xlsx,.xls,.csv';
@endphp
<div class="modal fade zoom-modal" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <form id="{{ $formId }}" action="{{ route('crm.'.$module.'.bulk-upload') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="{{ $modalId }}Label">Bulk Upload {{ $moduleLabel }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <a href="{{ route('crm.'.$module.'.bulk-upload.template') }}" class="btn btn-outline-secondary btn-sm">
              <i class="fa fa-download"></i> Download Sample Template
            </a>
            <small class="form-text text-muted d-block mt-1">Includes example rows and an Instructions sheet listing the valid values for your account.</small>
          </div>

          <div class="form-group">
            <label for="{{ $formId }}_file">Excel / CSV File</label>
            <input type="file" name="file" id="{{ $formId }}_file" class="form-control-file" accept="{{ $accept }}" required>
            <span class="invalid-feedback d-block" data-error-for="file"></span>
          </div>

          <div class="bulk-upload-result d-none">
            <hr>
            <div class="bulk-upload-summary mb-2"></div>
            <div class="bulk-upload-errors table-responsive" style="max-height: 260px; overflow-y: auto;"></div>
            <a href="#" class="bulk-upload-report-link btn btn-outline-danger btn-sm d-none" target="_blank">
              <i class="fa fa-file-download"></i> Download Error Report
            </a>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">
            <span class="btn-label">Import</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(function () {
    window.FBBulkUpload.bind({
      form: '#{{ $formId }}',
      modal: '#{{ $modalId }}'
    });
  });
</script>
