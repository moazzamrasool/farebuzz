<div class="modal fade" id="emailModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('crm.package-enquiries.email.send', $enquiry->id) }}" method="POST" enctype="multipart/form-data" class="modal-content" id="emailComposeForm">
      @csrf
      <div class="modal-header"><h5 class="modal-title"><i class="fas fa-envelope"></i> Send Email</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div class="form-group">
          <label>Template</label>
          <select id="emailTemplate" class="form-control">
            <option value="">— Blank email —</option>
          </select>
          <input type="hidden" name="message_template_id" id="emailTemplateId">
        </div>
        <div class="form-group">
          <label>Subject</label>
          <input type="text" name="subject" id="emailSubject" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Body</label>
          <textarea name="body" id="emailBody" class="form-control rich-text-editor" data-editor-height="200" rows="8" required></textarea>
        </div>
        <div class="form-group">
          <label>Attachment (optional)</label>
          <input type="file" name="attachment" class="form-control-file">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-info">Send Email</button>
      </div>
    </form>
  </div>
</div>

<script>
  (function ($) {
    var composeUrl = '{{ route('crm.package-enquiries.email.compose', $enquiry->id) }}';
    var templates = [];

    $('#emailModal').on('show.bs.modal', function () {
      $.getJSON(composeUrl, function (response) {
        templates = response.templates || [];
        var $select = $('#emailTemplate');
        $select.find('option:not(:first)').remove();
        templates.forEach(function (t) {
          $select.append($('<option>').val(t.id).text(t.name));
        });
      });
    });

    $('#emailTemplate').on('change', function () {
      var id = $(this).val();
      var template = templates.find(function (t) { return String(t.id) === String(id); });
      $('#emailTemplateId').val(id || '');
      $('#emailSubject').val(template ? template.subject : '');
      var editor = $('#emailBody').data('ckeditor-instance');
      var body = template ? template.body : '';
      if (editor) {
        editor.setData(body);
      } else {
        $('#emailBody').val(body);
      }
    });

    // CKEditor's own auto-sync only fires on a native, non-AJAX submit — this form
    // does submit natively (needs multipart/form-data for the attachment), but the
    // sync must still happen explicitly since it's bound after editor creation.
    $('#emailComposeForm').on('submit', function () {
      var editor = $('#emailBody').data('ckeditor-instance');
      if (editor) {
        editor.updateSourceElement();
      }
    });
  })(jQuery);
</script>
