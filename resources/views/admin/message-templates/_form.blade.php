<div class="form-group">
  <label>Name</label>
  <input type="text" name="name" value="{{ old('name', $messageTemplate->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
  @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
</div>

<div class="form-group">
  <label>Channel</label>
  <select name="channel" id="templateChannel" class="form-control @error('channel') is-invalid @enderror" required>
    <option value="whatsapp" @selected(old('channel', $messageTemplate->channel ?? '') === 'whatsapp')>WhatsApp</option>
    <option value="email" @selected(old('channel', $messageTemplate->channel ?? '') === 'email')>Email</option>
  </select>
  @error('channel')<span class="invalid-feedback">{{ $message }}</span>@enderror
</div>

<div class="form-group" id="subjectGroup">
  <label>Subject <span class="text-muted small">(email only)</span></label>
  <input type="text" name="subject" value="{{ old('subject', $messageTemplate->subject ?? '') }}" class="form-control @error('subject') is-invalid @enderror">
  @error('subject')<span class="invalid-feedback">{{ $message }}</span>@enderror
</div>

<div class="form-group">
  <label>Body</label>
  <textarea name="body" id="templateBody" rows="8" class="form-control @error('body') is-invalid @enderror">{{ old('body', $messageTemplate->body ?? '') }}</textarea>
  @error('body')<span class="invalid-feedback">{{ $message }}</span>@enderror
  <small class="form-text text-muted">Merge fields: <code>{customer_name}</code>, <code>{package}</code>, <code>{travel_date}</code></small>
</div>

<div class="form-group form-check">
  <input type="checkbox" name="is_active" value="1" id="templateActive" class="form-check-input" @checked(old('is_active', $messageTemplate->is_active ?? true))>
  <label class="form-check-label" for="templateActive">Active</label>
</div>

<script>
  (function ($) {
    function syncChannelUI() {
      var isEmail = $('#templateChannel').val() === 'email';
      $('#subjectGroup').toggle(isEmail);

      var $body = $('#templateBody');
      var editor = $body.data('ckeditor-instance');

      if (isEmail && !editor && window.FBRichText) {
        $body.addClass('rich-text-editor');
        window.FBRichText.init($body.closest('form'));
      } else if (!isEmail && editor) {
        editor.updateSourceElement();
        window.FBRichText.destroy($body.closest('form'));
        $body.removeClass('rich-text-editor');
      }
    }

    $('#templateChannel').on('change', syncChannelUI);
    $(function () { syncChannelUI(); });

    $('form').on('submit', function () {
      var editor = $('#templateBody').data('ckeditor-instance');
      if (editor) {
        editor.updateSourceElement();
      }
    });
  })(jQuery);
</script>
