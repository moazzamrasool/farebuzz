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
      // The `cms-*` entries below are the design-block vocabulary used by the CMS page
      // templates (hero/cards/callout/etc — see public/frontend/asset/css/style.css and
      // resources/views/admin/cms-pages/_form.blade.php's "Insert template" picker).
      // Without an explicit allow rule, GeneralHtmlSupport's schema strips any tag/class
      // it doesn't recognise the moment content is loaded BACK into the editor — so a
      // template pasted via Source, saved, then reopened would silently lose its markup
      // on the very next save. `classes: true` accepts any class value on that tag so
      // new cms- block types can be added to the CSS without touching this list.
      htmlSupport: {
        allow: [
          { name: 'p', classes: ['line-tight', 'line-loose'] },
          { name: 'span', classes: ['letter-tight', 'letter-wide'] },
          { name: /^(div|section)$/, classes: true, styles: true, attributes: true },
          { name: 'a', classes: true, styles: true, attributes: { href: true, target: true, rel: true } },
          { name: /^(h[1-6]|ul|ol|li|table|thead|tbody|tr|th|td|figure|figcaption|small|strong|em|details|summary)$/, classes: true, styles: true }
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
        e.stopPropagation();
        var index = counter++;
        var html = $template[0].innerHTML.split('__INDEX__').join(index);
        var $row = $(html);
        $rows.append($row);
        initImageUpload($row);
        initRichText($row);
        initRepeater($row);
        renumberRepeaterRows($repeater);
      });

      $repeater.on('click', '[data-repeater-remove]', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $row = $(this).closest('[data-repeater-row]');
        var isSaved = !!$row.find('input[type="hidden"][name$="[id]"]').first().val();
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

  // Room Type select on the Hotels tab — render the picked room's full details
  // (prices, bed type, occupancy, size, meal plan) below the dropdown, since an
  // <option> can't carry rich markup itself.
  function renderRoomTypeDetail($select) {
    var $opt = $select.find('option:selected');
    var $detail = $select.closest('.form-group').find('.room-type-detail');
    if (!$opt.val()) {
      $detail.empty();
      return;
    }
    var parts = [];
    var price = parseFloat($opt.data('price'));
    var discounted = $opt.data('discounted-price');
    if (discounted !== '' && discounted !== undefined && discounted !== null) {
      parts.push('₹' + parseFloat(discounted).toLocaleString('en-IN') + ' <s class="text-muted">₹' + price.toLocaleString('en-IN') + '</s>');
    } else {
      parts.push('₹' + price.toLocaleString('en-IN'));
    }
    if ($opt.data('bed-type')) parts.push($opt.data('bed-type'));
    var occupancy = $opt.data('adults') + ' Adult' + ($opt.data('adults') == 1 ? '' : 's');
    if ($opt.data('children')) occupancy += ', ' + $opt.data('children') + ' Child' + ($opt.data('children') == 1 ? '' : 'ren');
    parts.push(occupancy);
    if ($opt.data('size')) parts.push($opt.data('size') + ' sqft');
    if ($opt.data('meal-plan')) parts.push($opt.data('meal-plan'));
    $detail.html(parts.join(' &middot; '));
  }

  $(document).on('change', '.room-type-select', function () {
    renderRoomTypeDetail($(this));
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

  // Holiday Package "Basic" tab — Nights/Days, Hotel Category and Meals are all derived
  // server-side (HolidayPackage::applyDerivedFields()); this is just a live preview so the
  // admin can see what will be saved. The server always recomputes on save and is the
  // source of truth — this preview is never submitted for nights/days, and only used for
  // hotel_category/meals when their Override checkbox is off.
  function recomputeDerivedPreview() {
    var $itineraryRows = $('#itineraryRepeater [data-repeater-rows] > [data-repeater-row]');
    var dayCount = $itineraryRows.length;

    var $nightsDays = $('#nightsDaysPreview');
    if ($nightsDays.length) {
      $nightsDays.val(dayCount > 0
        ? (dayCount - 1) + ' Nights / ' + dayCount + ' Days — based on ' + dayCount + ' itinerary day' + (dayCount === 1 ? '' : 's')
        : 'Will be calculated from itinerary');
    }

    var stars = [];
    $('.hotel-attach-toggle:checked').each(function () {
      var rating = parseInt($(this).data('star-rating'), 10);
      if (rating > 0 && stars.indexOf(rating) === -1) stars.push(rating);
    });
    stars.sort(function (a, b) { return a - b; });

    var hotelCategoryText;
    if (!stars.length) {
      hotelCategoryText = 'Will be calculated from attached hotels';
    } else if (stars.length === 1) {
      hotelCategoryText = stars[0] + ' Star Hotels';
    } else if (stars.length === 2) {
      hotelCategoryText = stars[0] + ' & ' + stars[1] + ' Star Hotels';
    } else {
      hotelCategoryText = stars[0] + ' to ' + stars[stars.length - 1] + ' Star Hotels';
    }
    $('#hotel_category_display').val(hotelCategoryText);

    var counts = { breakfast: 0, lunch: 0, dinner: 0 };
    $itineraryRows.each(function () {
      $(this).find('input[type="checkbox"][name$="[meal_tags][]"]:checked').each(function () {
        if (counts.hasOwnProperty(this.value)) counts[this.value]++;
      });
    });

    var mealsText;
    var totalMeals = counts.breakfast + counts.lunch + counts.dinner;
    if (dayCount === 0 || totalMeals === 0) {
      mealsText = 'Will be calculated from itinerary';
    } else {
      var extras = [];
      ['lunch', 'dinner'].forEach(function (meal) {
        if (counts[meal] > 0) {
          var label = meal.charAt(0).toUpperCase() + meal.slice(1);
          extras.push(counts[meal] + ' ' + label + (counts[meal] > 1 ? 's' : ''));
        }
      });

      // A day-1 arrival with no breakfast tag doesn't break "daily" breakfast —
      // mirrors HolidayPackage::deriveMeals() server-side.
      var $firstDayCheckboxes = $itineraryRows.first().find('input[type="checkbox"][name$="[meal_tags][]"]');
      var firstDayHasBreakfast = $firstDayCheckboxes.filter('[value="breakfast"]:checked').length > 0;
      var expectedBreakfastDays = firstDayHasBreakfast ? dayCount : dayCount - 1;

      if (expectedBreakfastDays > 0 && counts.breakfast === expectedBreakfastDays) {
        if (!extras.length) {
          mealsText = 'Daily Breakfast';
        } else if (extras.length === 1) {
          mealsText = 'Daily Breakfast + ' + extras[0];
        } else {
          var last = extras.pop();
          mealsText = 'Daily Breakfast, ' + extras.join(', ') + ' & ' + last;
        }
      } else {
        var parts = [];
        if (counts.breakfast > 0) parts.push(counts.breakfast + ' Breakfast' + (counts.breakfast > 1 ? 's' : ''));
        parts = parts.concat(extras);
        mealsText = parts.join(', ');
      }
    }
    $('#meals_display').val(mealsText);
  }

  window.FBRecomputeDerivedPreview = recomputeDerivedPreview;

  // Toggling "Override" swaps the read-only derived preview for a plain editable input.
  $(document).on('change', '#hotel_category_overridden', function () {
    $('#hotel_category_display').toggle(!this.checked);
    $('#hotel_category').toggle(this.checked);
  });
  $(document).on('change', '#meals_overridden', function () {
    $('#meals_display').toggle(!this.checked);
    $('#meals').toggle(this.checked);
  });

  // Recompute whenever anything the preview depends on changes: itinerary rows added/
  // removed, meal tags toggled, or a hotel attached/detached.
  $(document).on('click', '#itineraryRepeater [data-repeater-add], #itineraryRepeater [data-repeater-remove]', function () {
    setTimeout(recomputeDerivedPreview, 0);
  });
  $(document).on('change', '#itineraryRepeater input[type="checkbox"][name$="[meal_tags][]"], .hotel-attach-toggle', recomputeDerivedPreview);

  $(function () {
    initImageUpload(document);
    initRepeater(document);
    initRichText(document);
    $('.room-type-select').each(function () {
      renderRoomTypeDetail($(this));
    });
    recomputeDerivedPreview();
  });
})(jQuery);
