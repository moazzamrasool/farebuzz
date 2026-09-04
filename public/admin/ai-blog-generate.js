/* FareBuzz CRM — "Generate with AI" for the Blog editor.
 * Talks to crm.blogs.ai-generate (App\Http\Controllers\Admin\AiBlogController) and
 * pre-fills the existing form (including the featured image, via a synthetic File on
 * the #featured_image input) — nothing here saves anything, the admin still reviews
 * and clicks Create/Update normally.
 */
(function ($) {
  'use strict';

  function csrfHeaders() {
    return { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') };
  }

  // Polls until fn() returns a truthy value — used to wait for the CKEditor instance,
  // which public/admin/custom.js initRichText() attaches asynchronously.
  function waitFor(fn, callback, triesLeft) {
    triesLeft = triesLeft === undefined ? 100 : triesLeft;
    var value = fn();
    if (value) return callback(value);
    if (triesLeft <= 0) return;
    setTimeout(function () { waitFor(fn, callback, triesLeft - 1); }, 100);
  }

  function setRichText($textarea, html) {
    if (!$textarea.length) return;
    waitFor(function () { return $textarea.data('ckeditor-instance'); }, function (editor) {
      editor.setData(html || '');
    });
  }

  function base64ToFile(base64, filename, mime) {
    var byteChars = atob(base64);
    var byteNumbers = new Array(byteChars.length);
    for (var i = 0; i < byteChars.length; i++) {
      byteNumbers[i] = byteChars.charCodeAt(i);
    }
    var byteArray = new Uint8Array(byteNumbers);
    return new File([byteArray], filename, { type: mime });
  }

  // Assigns a generated File onto a real <input type="file"> via the DataTransfer API,
  // then fires 'change' so it behaves exactly like a manual file pick (triggers the
  // existing image preview binding in admin.blogs._form and gets uploaded normally).
  function setFileInput(input, file) {
    if (!input || typeof DataTransfer === 'undefined') return false;
    var dt = new DataTransfer();
    dt.items.add(file);
    input.files = dt.files;
    input.dispatchEvent(new Event('change', { bubbles: true }));
    return true;
  }

  function applyDraft(draft, imageBase64) {
    $('#title').val(draft.title || '').trigger('input');
    if (draft.slug) $('#slug').val(draft.slug);
    if (draft.category) $('#category').val(draft.category);
    $('#excerpt').val(draft.excerpt || '');
    $('#reading_time').val(draft.reading_time || '');

    setRichText($('#content'), draft.content_html);

    $('#meta_title').val(draft.meta_title || '').trigger('input');
    $('#meta_description').val(draft.meta_description || '').trigger('input');
    $('#meta_keywords').val(draft.meta_keywords || '');
    $('#focus_keyword').val(draft.focus_keyword || '');
    $('#tags').val(draft.tags || '');
    $('#og_title').val(draft.og_title || '');
    $('#og_description').val(draft.og_description || '');

    if (imageBase64) {
      var slug = (draft.slug || 'ai-blog-cover').toString().replace(/[^a-z0-9-]+/gi, '-');
      var file = base64ToFile(imageBase64, slug + '.png', 'image/png');
      setFileInput(document.getElementById('featured_image'), file);
    }

    var $seoPanel = $('#seoPanel');
    if (!$seoPanel.hasClass('show')) {
      $seoPanel.collapse('show');
    }
  }

  $(function () {
    var $modal = $('#aiGenerateBlogModal');
    if (!$modal.length || !window.FB_AI_GENERATE_BLOG_URL) return;

    var $btn = $('#aiGenerateBlogSubmitBtn');
    var $error = $modal.find('.ai-generate-error');

    function setLoading(isLoading) {
      $btn.prop('disabled', isLoading);
      $btn.find('.btn-label').toggleClass('d-none', isLoading);
      $btn.find('.spinner-border').toggleClass('d-none', !isLoading);
    }

    $btn.on('click', function () {
      $error.addClass('d-none').text('');

      var payload = {
        topic: $('#ai_blog_topic').val(),
        category: $('#ai_blog_category').val(),
        tone: $('#ai_blog_tone').val()
      };

      if (!payload.topic) {
        $error.removeClass('d-none').text('Please describe what this blog post should be about.');
        return;
      }

      setLoading(true);

      $.ajax({
        url: window.FB_AI_GENERATE_BLOG_URL,
        type: 'POST',
        data: payload,
        headers: csrfHeaders(),
        dataType: 'json',
        success: function (response) {
          if (!response.success) {
            $error.removeClass('d-none').text(response.message || 'Something went wrong.');
            return;
          }

          applyDraft(response.data, response.image_base64);
          $modal.modal('hide');
          if (typeof toastr !== 'undefined') {
            toastr.success(response.image_base64
              ? 'AI draft and cover image applied — review everything before saving.'
              : 'AI draft applied — review everything before saving. (Cover image could not be generated — please upload one manually.)');
          }
        },
        error: function (xhr) {
          var message = (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong. Please try again.';
          $error.removeClass('d-none').text(message);
        },
        complete: function () {
          setLoading(false);
        }
      });
    });
  });
})(jQuery);
