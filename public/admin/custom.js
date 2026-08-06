/* FareBuzz CRM — shared admin UI helpers: styled image uploads + AJAX modal forms */
(function ($) {
  'use strict';

  // ────────────────────────────────────────────────────────────
  // Styled image upload (dropzone with click / drag&drop / preview)
  // Markup contract:
  // <div class="img-upload" data-multiple="false">
  //   <input type="file" class="img-upload-input d-none" ...>
  //   <div class="img-upload-box">
  //     <div class="img-upload-placeholder">...</div>
  //     <div class="img-upload-preview-wrap"></div>
  //   </div>
  // </div>
  // ────────────────────────────────────────────────────────────
  function renderFilesPreview($widget, files) {
    var $box = $widget.find('.img-upload-box');
    var $previewWrap = $widget.find('.img-upload-preview-wrap');
    $previewWrap.empty();

    if (!files || !files.length) {
      $box.removeClass('has-image');
      return;
    }

    $box.addClass('has-image');
    $.each(files, function (i, file) {
      if (!file.type || file.type.indexOf('image') === -1) return;
      var reader = new FileReader();
      reader.onload = function (e) {
        $previewWrap.append(
          '<div class="img-upload-thumb"><img src="' + e.target.result + '" alt=""></div>'
        );
      };
      reader.readAsDataURL(file);
    });
  }

  function initImageUpload(root) {
    $(root).find('.img-upload').each(function () {
      var $widget = $(this);
      if ($widget.data('fbBound')) return;
      $widget.data('fbBound', true);

      var $input = $widget.find('.img-upload-input');
      var $box = $widget.find('.img-upload-box');
      var multiple = !!$input.prop('multiple');

      $box.on('click', function (e) {
        if ($(e.target).closest('.img-upload-remove').length) return;
        $input.trigger('click');
      });

      $input.on('change', function () {
        renderFilesPreview($widget, this.files);
      });

      $box.on('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $box.addClass('drag-over');
      });
      $box.on('dragleave drop', function () {
        $box.removeClass('drag-over');
      });
      $box.on('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var dt = e.originalEvent.dataTransfer;
        if (!dt || !dt.files || !dt.files.length) return;

        if (!multiple) {
          var singleTransfer = new DataTransfer();
          singleTransfer.items.add(dt.files[0]);
          $input[0].files = singleTransfer.files;
        } else {
          $input[0].files = dt.files;
        }
        $input.trigger('change');
      });

      $widget.on('click', '.img-upload-remove', function (e) {
        e.stopPropagation();
        $input.val('');
        $widget.find('.img-upload-preview-wrap').empty();
        $box.removeClass('has-image');
      });
    });
  }

  function resetImageUpload($widget) {
    $widget.find('.img-upload-input').val('');
    $widget.find('.img-upload-preview-wrap').empty();
    $widget.find('.img-upload-box').removeClass('has-image');
  }

  function setImageUploadPreview($widget, urls) {
    var $previewWrap = $widget.find('.img-upload-preview-wrap');
    $previewWrap.empty();
    urls = Array.isArray(urls) ? urls : (urls ? [urls] : []);

    if (!urls.length) {
      $widget.find('.img-upload-box').removeClass('has-image');
      return;
    }
    $widget.find('.img-upload-box').addClass('has-image');
    urls.forEach(function (url) {
      $previewWrap.append('<div class="img-upload-thumb"><img src="' + url + '" alt=""></div>');
    });
  }

  window.FBImageUpload = {
    init: initImageUpload,
    reset: resetImageUpload,
    setPreview: setImageUploadPreview
  };

  // ────────────────────────────────────────────────────────────
  // AJAX modal form helper — wires submit, validation errors,
  // loading state and modal reset. Shared across Master Data modules.
  // ────────────────────────────────────────────────────────────
  function escapeHtml(value) {
    return $('<div>').text(value === null || value === undefined ? '' : value).html();
  }

  function bindModalForm(options) {
    var $form = $(options.form);
    var $modal = $(options.modal);

    function submitButton() {
      return $form.find('[type="submit"]');
    }

    function clearErrors() {
      $form.find('.is-invalid').removeClass('is-invalid');
      $form.find('[data-error-for]').text('');
    }

    function setLoading(isLoading) {
      var $btn = submitButton();
      $btn.prop('disabled', isLoading);
      $btn.find('.btn-label').toggleClass('d-none', isLoading);
      $btn.find('.spinner-border').toggleClass('d-none', !isLoading);
    }

    $form.off('submit.fbModalForm').on('submit.fbModalForm', function (e) {
      e.preventDefault();
      clearErrors();
      setLoading(true);

      var formData = new FormData($form[0]);
      var httpMethod = $form.data('http-method');
      if (httpMethod && httpMethod.toUpperCase() !== 'POST') {
        formData.append('_method', httpMethod);
      }

      $.ajax({
        url: $form.attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            if (typeof toastr !== 'undefined') {
              toastr.success(response.message || 'Saved successfully.');
            }
            if (typeof options.onSuccess === 'function') {
              options.onSuccess(response, $form, $modal);
            }
            $modal.modal('hide');
          }
        },
        error: function (xhr) {
          if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
            $.each(xhr.responseJSON.errors, function (field, messages) {
              $form.find('[name="' + field + '"]').addClass('is-invalid');
              $form.find('[data-error-for="' + field + '"]').text(messages[0]);
            });
          } else if (typeof toastr !== 'undefined') {
            toastr.error('Something went wrong. Please try again.');
          }
        },
        complete: function () {
          setLoading(false);
        }
      });
    });

    $modal.off('hidden.bs.modal.fbModalForm').on('hidden.bs.modal.fbModalForm', function () {
      $form[0].reset();
      clearErrors();
      $form.find('.img-upload').each(function () {
        resetImageUpload($(this));
      });
      if (typeof options.onReset === 'function') {
        options.onReset($form, $modal);
      }
    });
  }

  window.FBModalForm = {
    bind: bindModalForm,
    escapeHtml: escapeHtml
  };

  // ────────────────────────────────────────────────────────────
  // FBBulkUpload — Excel/CSV bulk-upload modal handler. Mirrors FBModalForm's
  // AJAX/spinner/toastr conventions, but the response shape is a summary +
  // per-row error list rather than {success, message}, so it renders a result
  // panel inside the modal instead of patching a single table row/closing on
  // success. The underlying list only needs a refresh if rows were actually
  // imported, so that's deferred to modal-close rather than done immediately
  // (the user should get to read the summary/errors first).
  // ────────────────────────────────────────────────────────────
  function bindBulkUpload(options) {
    var $form = $(options.form);
    var $modal = $(options.modal);
    var $result = $form.find('.bulk-upload-result');
    var $summary = $form.find('.bulk-upload-summary');
    var $errors = $form.find('.bulk-upload-errors');
    var $reportLink = $form.find('.bulk-upload-report-link');

    function submitButton() {
      return $form.find('[type="submit"]');
    }

    function clearErrors() {
      $form.find('.is-invalid').removeClass('is-invalid');
      $form.find('[data-error-for]').text('');
    }

    function setLoading(isLoading) {
      var $btn = submitButton();
      $btn.prop('disabled', isLoading);
      $btn.find('.btn-label').toggleClass('d-none', isLoading);
      $btn.find('.spinner-border').toggleClass('d-none', !isLoading);
    }

    function renderResult(response) {
      $result.removeClass('d-none');
      $summary.html(
        '<span class="badge badge-success">Imported: ' + response.imported + '</span> ' +
        '<span class="badge badge-warning">Skipped: ' + response.skipped + '</span>'
      );

      if (response.errors && response.errors.length) {
        var rows = response.errors.map(function (e) {
          return '<tr><td>' + escapeHtml(e.row) + '</td><td>' + escapeHtml(e.errors.join('; ')) + '</td></tr>';
        }).join('');
        $errors.html('<table class="table table-sm table-bordered mb-0"><thead><tr><th style="width:70px;">Row</th><th>Reason</th></tr></thead><tbody>' + rows + '</tbody></table>');
      } else {
        $errors.html('');
      }

      if (response.error_report_url) {
        $reportLink.attr('href', response.error_report_url).removeClass('d-none');
      } else {
        $reportLink.addClass('d-none');
      }

      $modal.data('fbBulkUploadImported', response.imported > 0);
    }

    $form.off('submit.fbBulkUpload').on('submit.fbBulkUpload', function (e) {
      e.preventDefault();
      clearErrors();
      setLoading(true);
      $result.addClass('d-none');

      var formData = new FormData($form[0]);

      $.ajax({
        url: $form.attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            renderResult(response);
            if (typeof toastr !== 'undefined') {
              toastr.success('Import finished — ' + response.imported + ' row(s) imported, ' + response.skipped + ' skipped.');
            }
          }
        },
        error: function (xhr) {
          if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
            $.each(xhr.responseJSON.errors, function (field, messages) {
              $form.find('[name="' + field + '"]').addClass('is-invalid');
              $form.find('[data-error-for="' + field + '"]').text(messages[0]);
            });
          } else if (typeof toastr !== 'undefined') {
            toastr.error('Something went wrong. Please try again.');
          }
        },
        complete: function () {
          setLoading(false);
        }
      });
    });

    $modal.off('hidden.bs.modal.fbBulkUpload').on('hidden.bs.modal.fbBulkUpload', function () {
      var shouldReload = $modal.data('fbBulkUploadImported');
      $form[0].reset();
      clearErrors();
      $result.addClass('d-none');
      $summary.html('');
      $errors.html('');
      $reportLink.addClass('d-none');
      $modal.removeData('fbBulkUploadImported');
      if (shouldReload) {
        window.location.reload();
      }
    });
  }

  window.FBBulkUpload = {
    bind: bindBulkUpload
  };

  // ────────────────────────────────────────────────────────────
  // CKEditor 5 rich-text helper — self-hosted GPL build, no API key/account.
  // Markup contract: <textarea class="rich-text-editor" ...>
  //
  // CKEditor's UMD build exposes one global, window.CKEDITOR, holding every plugin
  // class as a property (CKEDITOR.Bold, CKEDITOR.Essentials, ...) — the `plugins`
  // array must hold those actual class references, not strings (only `toolbar`
  // entries are plain string ids). custom.js loads BEFORE ckeditor5.umd.js (see
  // layouts/admin/app.blade.php), so this config is built lazily inside
  // initRichText() rather than at parse time, once CKEDITOR is guaranteed to exist.
  // ────────────────────────────────────────────────────────────
  function buildCkeditorConfig() {
    var C = window.CKEDITOR;

    // Defensive: resolve each plugin by name and drop any that don't exist on this
    // vendored build instead of letting one bad name crash editor creation entirely —
    // logs a console warning naming the culprit so a mismatch is easy to spot/fix.
    // NOTE: CKEditor 5's official "Line Height" feature is a paid/premium plugin (ships
    // in the separate ckeditor5-premium-features package, needs a commercial license) —
    // not usable with our self-hosted GPL build. Line-height/letter-spacing here are
    // instead offered as named presets via the free, open-source Style + GeneralHtmlSupport
    // plugins (applies a CSS class from a dropdown; see the .line-*/.letter-* rules in
    // public/admin/custom.css and public/frontend/asset/css/style.css).
    var pluginNames = [
      'Essentials', 'Paragraph', 'Heading',
      'Bold', 'Italic', 'Underline', 'Strikethrough', 'Subscript', 'Superscript', 'RemoveFormat',
      'FontFamily', 'FontSize', 'FontColor', 'FontBackgroundColor', 'Highlight',
      'Alignment',
      'List', 'TodoList', 'Indent', 'IndentBlock',
      'Link', 'LinkImage', 'BlockQuote',
      'Table', 'TableToolbar', 'TableProperties', 'TableCellProperties',
      'HorizontalLine', 'SpecialCharacters', 'FindAndReplace', 'SourceEditing',
      'Image', 'ImageUpload', 'ImageStyle', 'ImageToolbar', 'ImageResize', 'ImageCaption', 'ImageInsert', 'MediaEmbed',
      'SimpleUploadAdapter',
      'GeneralHtmlSupport', 'Style'
    ];
    var plugins = [];
    pluginNames.forEach(function (name) {
      if (C[name]) {
        plugins.push(C[name]);
      } else if (window.console) {
        console.warn('CKEditor plugin "' + name + '" not found on this build — skipped.');
      }
    });

    return {
      licenseKey: 'GPL',
      plugins: plugins,
      toolbar: {
        items: [
          'undo', 'redo', '|',
          'heading', '|',
          'fontFamily', 'fontSize', '|',
          'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', 'removeFormat', '|',
          'fontColor', 'fontBackgroundColor', 'highlight', '|',
          'alignment', 'style', '|',
          'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent', '|',
          'link', 'blockQuote', 'insertTable', 'horizontalLine', 'specialCharacters', 'findAndReplace', '|',
          'insertImage', 'mediaEmbed', '|',
          'sourceEditing'
        ],
        shouldNotGroupWhenFull: true
      },
      image: {
        // wrapText = alignLeft/alignRight grouped (float, text wraps beside the image);
        // breakText = alignCenter/alignBlockLeft/alignBlockRight grouped (block position,
        // no wrap) — CKEditor's own standard groupings. Matches ckeditor5-content.css on
        // the frontend (public/frontend/asset/css), so a floated image wraps text
        // identically in the editor and on the published page.
        toolbar: [
          'imageStyle:wrapText', 'imageStyle:breakText', 'imageStyle:inline', '|',
          'toggleImageCaption', 'imageTextAlternative', 'linkImage', 'resizeImage'
        ],
        styles: { options: ['inline', 'alignLeft', 'alignRight', 'alignCenter', 'alignBlockLeft', 'alignBlockRight'] },
        resizeUnit: '%'
      },
      table: {
        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
      },
      fontSize: { options: [9, 11, 13, 'default', 17, 19, 21, 24, 28, 32] },
      fontFamily: {
        options: [
          'default',
          'Arial, sans-serif',
          'Georgia, serif',
          'Times New Roman, serif',
          'Courier New, monospace',
          'Verdana, sans-serif'
        ]
      },
      // Named formatting presets (Style plugin, built on GeneralHtmlSupport) — the closest
      // free/GPL equivalent to line-height/letter-spacing controls, since CKEditor's own
      // dedicated Line Height feature is premium-only. Applies a plain CSS class; the
      // actual spacing values live in CSS (custom.css here, style.css on the frontend).
      style: {
        definitions: [
          { name: 'Tight line spacing', element: 'p', classes: ['line-tight'] },
          { name: 'Loose line spacing', element: 'p', classes: ['line-loose'] },
          { name: 'Tight letter spacing', element: 'span', classes: ['letter-tight'] },
          { name: 'Wide letter spacing', element: 'span', classes: ['letter-wide'] }
        ]
      },
      htmlSupport: {
        allow: [
          { name: 'p', classes: ['line-tight', 'line-loose'] },
          { name: 'span', classes: ['letter-tight', 'letter-wide'] }
        ]
      },
      // Posts straight to EditorUploadController::image() (routes/admin.php:
      // crm.editor.upload-image), same CSRF header every admin AJAX call uses.
      simpleUpload: {
        uploadUrl: window.FB_IMAGE_UPLOAD_URL,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
      }
    };
  }

  function initRichText(root) {
    if (typeof window.CKEDITOR === 'undefined') return;
    var ClassicEditor = window.CKEDITOR.ClassicEditor;
    $(root).find('.rich-text-editor').each(function () {
      var $el = $(this);
      if ($el.data('ckeditor-instance') || $el.data('ckeditor-initializing')) return;
      $el.data('ckeditor-initializing', true);

      ClassicEditor.create(this, buildCkeditorConfig()).then(function (editor) {
        var height = $el.data('editor-height');
        if (height) {
          editor.editing.view.change(function (writer) {
            writer.setStyle('min-height', height + 'px', editor.editing.view.document.getRoot());
          });
        }
        $el.removeData('ckeditor-initializing').data('ckeditor-instance', editor);
      }).catch(function (error) {
        $el.removeData('ckeditor-initializing');
        if (window.console) console.error('CKEditor failed to initialize:', error);
      });
    });
  }

  function destroyRichText(root) {
    if (typeof window.CKEDITOR === 'undefined') return;
    $(root).find('.rich-text-editor').each(function () {
      var $el = $(this);
      var editor = $el.data('ckeditor-instance');
      if (editor) {
        editor.destroy();
        $el.removeData('ckeditor-instance');
      }
    });
  }

  window.FBRichText = {
    init: initRichText,
    destroy: destroyRichText
  };

  // ────────────────────────────────────────────────────────────
  // Repeatable row groups — itinerary days, FAQs, room types, photos, reviews.
  // Markup contract:
  // <div data-repeater>
  //   <div data-repeater-rows>...existing rows, each data-repeater-row...</div>
  //   <template data-repeater-template>...row markup using __INDEX__ placeholders...</template>
  //   <button type="button" data-repeater-add>Add row</button>
  //   each row needs a [data-repeater-remove] button
  // </div>
  // ────────────────────────────────────────────────────────────
  function renumberRepeaterRows($repeater) {
    $repeater.find('> [data-repeater-rows] > [data-repeater-row]').each(function (i) {
      $(this).find('[data-repeater-index]').first().text(i + 1);
    });
  }

  function initRepeater(root) {
    $(root).find('[data-repeater]').each(function () {
      var $repeater = $(this);
      if ($repeater.data('fbBound')) return;
      $repeater.data('fbBound', true);

      var $rows = $repeater.children('[data-repeater-rows]').first();
      var $template = $repeater.children('template[data-repeater-template]').first();
      var counter = $rows.children().length;

      $repeater.on('click', '[data-repeater-add]', function (e) {
        e.preventDefault();
        var index = counter++;
        var html = $template[0].innerHTML.split('__INDEX__').join(index);
        var $row = $(html);
        $rows.append($row);
        initImageUpload($row);
        initRichText($row);
        renumberRepeaterRows($repeater);
      });

      $repeater.on('click', '[data-repeater-remove]', function (e) {
        e.preventDefault();
        var $row = $(this).closest('[data-repeater-row]');
        var isSaved = !!$row.find('input[type="hidden"][name$="[id]"]').val();
        if (isSaved && !window.confirm('Remove this item? It will be deleted once you save.')) {
          return;
        }
        destroyRichText($row);
        $row.remove();
        renumberRepeaterRows($repeater);
      });

      renumberRepeaterRows($repeater);
    });
  }

  window.FBRepeater = {
    init: initRepeater
  };

  // Only one photo can be the cover — checking one unchecks the rest in the same repeater.
  $(document).on('change', '[data-repeater] input[type="checkbox"][name$="[is_cover]"]', function () {
    if (!this.checked) return;
    var $repeater = $(this).closest('[data-repeater]');
    $repeater.find('input[type="checkbox"][name$="[is_cover]"]').not(this).prop('checked', false);
  });

  // Holiday Package "Activities" tab — checking a card reveals its price/flag/note
  // fields; unchecking hides (but does not clear) them, so re-checking restores what
  // was there. A search box filters the cards by name.
  $(document).on('change', '.activity-attach-toggle', function () {
    var $card = $(this).closest('.activity-pick-card');
    $card.toggleClass('is-attached', this.checked);
    $card.find('.activity-extra-fields').toggle(this.checked);
  });

  // Holiday Package "Hotels" tab — same attach/reveal + search pattern as Activities above.
  $(document).on('change', '.hotel-attach-toggle', function () {
    var $card = $(this).closest('.hotel-pick-card');
    $card.toggleClass('is-attached', this.checked);
    $card.find('.hotel-extra-fields').toggle(this.checked);
  });

  $(document).on('keyup', '#hotelSearch', function () {
    var term = $(this).val().toLowerCase().trim();
    $('.hotel-card-col').each(function () {
      var matches = $(this).data('hotel-name').toString().indexOf(term) !== -1;
      $(this).toggle(matches);
    });
  });

  $(document).on('keyup', '#activitySearch', function () {
    var term = $(this).val().toLowerCase().trim();
    $('.activity-card-col').each(function () {
      var matches = $(this).data('activity-name').toString().indexOf(term) !== -1;
      $(this).toggle(matches);
    });
  });

  // Holiday Package "Basic" tab — Days is derived from Nights (days = nights + 1), the
  // day-wise itinerary/hotel/activity day pickers all key off this count. Days stays a
  // plain editable input (so a save with a mismatched value still round-trips and gets
  // caught by server-side validation) but auto-follows Nights on every change.
  $(document).on('input', '#nights', function () {
    var nights = parseInt($(this).val(), 10);
    if (isNaN(nights) || nights < 0) return;
    $('#days').val(nights + 1);
  });

  $(function () {
    initImageUpload(document);
    initRepeater(document);
    initRichText(document);
  });
})(jQuery);
