<div class="modal fade" id="whatsappModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="fab fa-whatsapp"></i> Send WhatsApp</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div class="form-group">
          <label>Template</label>
          <select id="whatsappTemplate" class="form-control">
            <option value="">— Blank message —</option>
          </select>
        </div>
        <div class="form-group">
          <label>Message</label>
          <textarea id="whatsappMessage" class="form-control" rows="5"></textarea>
        </div>
        <p class="text-muted small mb-0">Opens WhatsApp with this message pre-filled to {{ $enquiry->phone }}. The message is logged to the timeline when you click Send.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" id="whatsappSendBtn" class="btn btn-success"><i class="fab fa-whatsapp"></i> Send</button>
      </div>
    </div>
  </div>
</div>

<script>
  (function ($) {
    var composeUrl = '{{ route('crm.package-enquiries.whatsapp.compose', $enquiry->id) }}';
    var sendUrl = '{{ route('crm.package-enquiries.whatsapp.send', $enquiry->id) }}';
    var templates = [];

    $('#whatsappModal').on('show.bs.modal', function () {
      $.getJSON(composeUrl, function (response) {
        templates = response.templates || [];
        var $select = $('#whatsappTemplate');
        $select.find('option:not(:first)').remove();
        templates.forEach(function (t) {
          $select.append($('<option>').val(t.id).text(t.name));
        });
      });
    });

    $('#whatsappTemplate').on('change', function () {
      var id = $(this).val();
      var template = templates.find(function (t) { return String(t.id) === String(id); });
      $('#whatsappMessage').val(template ? template.body : '');
    });

    $('#whatsappSendBtn').on('click', function () {
      var message = $('#whatsappMessage').val();
      if (!message.trim()) {
        alert('Please enter a message.');
        return;
      }

      var $btn = $(this).prop('disabled', true);

      $.post(sendUrl, {
        _token: '{{ csrf_token() }}',
        message: message,
        message_template_id: $('#whatsappTemplate').val() || null
      }).done(function (response) {
        window.open(response.url, '_blank');
        $('#whatsappModal').modal('hide');
        window.location.reload();
      }).fail(function () {
        alert('Could not send WhatsApp message. Please try again.');
      }).always(function () {
        $btn.prop('disabled', false);
      });
    });
  })(jQuery);
</script>
