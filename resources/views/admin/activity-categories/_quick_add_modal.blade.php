{{-- Reusable "+ Add New Category" inline modal. Any <select name="activity_category_id">
     on the including page automatically gets the "+ Add New Category" behavior:
     picking it opens this modal, creates the category via AJAX, and appends + selects
     the new option back on the select that triggered it — without touching/resetting
     whatever other form it lives in (including one inside another open modal). --}}
<div class="modal fade" id="quickAddActivityCategoryModal" tabindex="-1" role="dialog" aria-labelledby="quickAddActivityCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="quickAddActivityCategoryForm" action="{{ route('crm.activity-categories.store') }}" method="POST" novalidate>
        @csrf
        <input type="hidden" name="status" value="active">
        <input type="hidden" name="sort_order" value="0">
        <div class="modal-header">
          <h5 class="modal-title" id="quickAddActivityCategoryModalLabel">Add Activity Category</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="quick_activity_category_name">Name</label>
            <input type="text" name="name" id="quick_activity_category_name" class="form-control" placeholder="e.g. Adventure, Water Sports" required>
            <span class="invalid-feedback d-block" data-error-for="name"></span>
          </div>
          <div class="form-group">
            <label for="quick_activity_category_description">Description <small class="text-muted">(optional)</small></label>
            <textarea name="description" id="quick_activity_category_description" rows="3" class="form-control"></textarea>
            <span class="invalid-feedback d-block" data-error-for="description"></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="quickAddActivityCategorySubmitBtn">
            <span class="btn-label">Create Category</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(function ($) {
  $(function () {
    var $quickModal = $('#quickAddActivityCategoryModal');
    var $quickForm = $('#quickAddActivityCategoryForm');
    var ADD_NEW_VALUE = '__add_new__';
    var $activeSelect = null; // the <select> that triggered the quick-add, so we know where to apply the result

    // Remember each select's last real (non-"add new") value so it can be reverted
    // while the quick-add modal is open. Focus always fires before change on a
    // <select>, so this is populated before the "add new" selection is ever seen.
    $(document).on('focus', 'select[name="activity_category_id"]', function () {
      var $sel = $(this);
      if ($sel.val() !== ADD_NEW_VALUE) {
        $sel.data('prevVal', $sel.val());
      }
    });

    $(document).on('change', 'select[name="activity_category_id"]', function () {
      var $sel = $(this);
      if ($sel.val() !== ADD_NEW_VALUE) {
        $sel.data('prevVal', $sel.val());
        return;
      }

      $activeSelect = $sel;
      $sel.val($sel.data('prevVal') || '');
      $quickForm[0].reset();
      $quickForm.find('.is-invalid').removeClass('is-invalid');
      $quickForm.find('[data-error-for]').text('');
      $quickModal.modal('show');
    });

    // Bootstrap 4 has no built-in support for stacked modals: showing this modal
    // while the Activity modal is still open leaves it fighting the first modal's
    // backdrop for stacking order, and hiding it strips 'modal-open' off <body>
    // (Bootstrap does this unconditionally), unlocking scroll under the Activity
    // modal that's still open behind it. Fix is scoped to #quickAddActivityCategoryModal
    // only — it does not touch any other modal in the CRM.
    $quickModal.on('show.bs.modal', function () {
      var z = 1055 + (10 * $('.modal.show').length);
      $quickModal.css('z-index', z);
      setTimeout(function () {
        $('.modal-backdrop').not('.quick-add-backdrop-tagged').last()
          .css('z-index', z - 1).addClass('quick-add-backdrop-tagged');
      }, 0);
    });

    $quickModal.on('hidden.bs.modal', function () {
      if ($('.modal.show').length) {
        $('body').addClass('modal-open');
      }
      $activeSelect = null;
    });

    $quickForm.off('submit').on('submit', function (e) {
      e.preventDefault();
      var $btn = $('#quickAddActivityCategorySubmitBtn');
      $btn.prop('disabled', true);
      $btn.find('.btn-label').addClass('d-none');
      $btn.find('.spinner-border').removeClass('d-none');

      $.ajax({
        url: $quickForm.attr('action'),
        type: 'POST',
        data: $quickForm.serialize(),
        dataType: 'json',
        success: function (response) {
          if (!response.success) return;
          var category = response.category;
          var optionHtml = '<option value="' + category.id + '">' + FBModalForm.escapeHtml(category.name) + '</option>';

          $('select[name="activity_category_id"]').each(function () {
            var $sel = $(this);
            if ($sel.find('option[value="' + category.id + '"]').length === 0) {
              $sel.find('option[value="' + ADD_NEW_VALUE + '"]').before(optionHtml);
            }
          });

          if ($activeSelect) {
            $activeSelect.val(category.id).data('prevVal', category.id);
          }

          if (typeof toastr !== 'undefined') {
            toastr.success('Category added.');
          }
          $quickModal.modal('hide');
        },
        error: function (xhr) {
          if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
            $.each(xhr.responseJSON.errors, function (field, messages) {
              var $target = $quickForm.find('[data-error-for="' + field + '"]');
              if ($target.length) {
                $quickForm.find('[name="' + field + '"]').addClass('is-invalid');
                $target.text(messages[0]);
              } else if (typeof toastr !== 'undefined') {
                // Field has no visible input in this modal (e.g. status) — surface it as a toast instead of failing silently.
                toastr.error(messages[0]);
              }
            });
          } else if (typeof toastr !== 'undefined') {
            toastr.error('Something went wrong. Please try again.');
          }
        },
        complete: function () {
          $btn.prop('disabled', false);
          $btn.find('.btn-label').removeClass('d-none');
          $btn.find('.spinner-border').addClass('d-none');
        }
      });
    });
  });
})(jQuery);
</script>
