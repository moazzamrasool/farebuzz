/* FareBuzz CRM — "Generate with AI" for the Holiday Package builder.
 * Talks to crm.holiday-packages.ai-generate (App\Http\Controllers\Admin\AiPackageController)
 * and pre-fills the existing form/repeaters — nothing here saves anything, the admin
 * still reviews and clicks Create/Update normally.
 */
(function ($) {
  'use strict';

  function csrfHeaders() {
    return { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') };
  }

  function normalize(text) {
    return (text || '').toString().toLowerCase().replace(/[^a-z0-9]+/g, ' ').trim();
  }

  function escapeHtml(text) {
    return $('<div>').text(text === null || text === undefined ? '' : text).html();
  }

  // Polls until fn() returns a truthy value — used to wait for the CKEditor instance a
  // freshly-cloned repeater row gets asynchronously (public/admin/custom.js initRichText()).
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

  function renumber($repeater) {
    $repeater.find('> [data-repeater-rows] > [data-repeater-row]').each(function (i) {
      $(this).find('[data-repeater-index]').first().text(i + 1);
    });
  }

  // Empties a repeater (itinerary/FAQ) so the AI draft replaces stale rows instead of
  // appending after them — confirms first so a regenerate never silently wipes work.
  function clearRepeater($repeater) {
    var $rows = $repeater.children('[data-repeater-rows]').first();
    var $existing = $rows.children('[data-repeater-row]');
    if (!$existing.length) return true;
    if (!window.confirm('This will replace the existing rows in this section with the AI draft. Continue?')) {
      return false;
    }
    $existing.each(function () { window.FBRichText.destroy($(this)); });
    $rows.empty();
    return true;
  }

  function addRow($repeater) {
    $repeater.children('[data-repeater-add]').trigger('click');
    return $repeater.children('[data-repeater-rows]').children('[data-repeater-row]').last();
  }

  function fillItinerary(days) {
    var $repeater = $('#itineraryRepeater');
    if (!$repeater.length || !clearRepeater($repeater)) return;

    (days || []).forEach(function (day, i) {
      var $row = addRow($repeater);
      $row.find('input[name$="[day_number]"]').val(day.day_number || (i + 1));
      $row.find('input[name$="[title]"]').val(day.title || '');
      $row.find('input[name$="[route_summary]"]').val(day.route_summary || '');
      $row.find('textarea[name$="[bullet_points]"]').val((day.bullet_points || []).join('\n'));
      (day.meal_tags || []).forEach(function (meal) {
        $row.find('input[type="checkbox"][name$="[meal_tags][]"][value="' + meal + '"]').prop('checked', true);
      });
      setRichText($row.find('textarea.rich-text-editor'), day.detail_html);
    });
    renumber($repeater);
  }

  function fillFaqs(faqs) {
    var $repeater = $('#faqRepeater');
    if (!$repeater.length || !clearRepeater($repeater)) return;

    (faqs || []).forEach(function (faq) {
      var $row = addRow($repeater);
      $row.find('input[name$="[question]"]').val(faq.question || '');
      setRichText($row.find('textarea.rich-text-editor'), faq.answer);
    });
    renumber($repeater);
  }

  // Checks any inclusion/exclusion/activity checkbox whose label matches an
  // AI-suggested name; returns the names that had no master-data match.
  function matchCheckboxes(names, checkboxSelector) {
    var unmatched = [];
    var $boxes = $(checkboxSelector);

    (names || []).forEach(function (name) {
      var norm = normalize(name);
      var matched = null;

      $boxes.each(function () {
        var labelText = normalize($('label[for="' + this.id + '"]').text());
        if (labelText && (labelText === norm || labelText.indexOf(norm) !== -1 || norm.indexOf(labelText) !== -1)) {
          matched = this;
          return false;
        }
      });

      if (matched) {
        $(matched).prop('checked', true).trigger('change');
      } else {
        unmatched.push(name);
      }
    });

    return unmatched;
  }

  function addCustomLines(repeaterSelector, names) {
    var $repeater = $(repeaterSelector);
    if (!$repeater.length) return;
    (names || []).forEach(function (name) {
      var $row = addRow($repeater);
      $row.find('input[type="text"]').val(name);
    });
    renumber($repeater);
  }

  function renderActivitySuggestions(names) {
    var $box = $('#aiActivitySuggestions');
    if (!$box.length) return;
    if (!names || !names.length) { $box.empty(); return; }

    var items = names.map(function (n) { return '<li>' + escapeHtml(n) + '</li>'; }).join('');
    $box.html(
      '<div class="alert alert-info mt-2 mb-0">' +
      '<strong>AI suggested (not in master data — add manually if wanted):</strong>' +
      '<ul class="mb-0">' + items + '</ul></div>'
    );
  }

  function showPriceEstimateBanner() {
    if ($('#aiPriceEstimateBanner').length) return;
    $('#tab-pricing .pkg-section-heading').after(
      '<div id="aiPriceEstimateBanner" class="alert alert-warning">' +
      '<i class="fas fa-magic mr-1"></i>Prices below are AI estimates — please confirm before saving.</div>'
    );
  }

  // The banner disappears the moment the admin deliberately edits either price field —
  // it should never linger once a real number has been entered.
  $(document).on('input', '#price, #discounted_price', function () {
    $('#aiPriceEstimateBanner').remove();
  });

  function applyDraft(draft) {
    $('#title').val(draft.title || '');
    if (draft.slug) $('#slug').val(draft.slug);
    $('#hotel_category').val(draft.hotel_category || '');
    $('#meals').val(draft.meals || '');
    $('#language').val(draft.language || '');

    var overviewHtml = draft.short_description
      ? '<p><strong>' + escapeHtml(draft.short_description) + '</strong></p>' + (draft.overview_html || '')
      : (draft.overview_html || '');
    setRichText($('#overview'), overviewHtml);

    if (draft.price_estimate) {
      if (draft.price_estimate.price) $('#price').val(draft.price_estimate.price);
      if (draft.price_estimate.discounted_price) $('#discounted_price').val(draft.price_estimate.discounted_price);
      showPriceEstimateBanner();
    }

    addCustomLines('#customInclusionsRepeater', matchCheckboxes(draft.inclusions, 'input[name="inclusion_feature_ids[]"]'));
    addCustomLines('#customExclusionsRepeater', matchCheckboxes(draft.exclusions, 'input[name="exclusion_feature_ids[]"]'));
    renderActivitySuggestions(matchCheckboxes(draft.activities, 'input[name="activity_ids[]"]'));

    fillItinerary(draft.itinerary);
    fillFaqs(draft.faqs);
  }

  // ── Generate modal (only present when the AI feature is enabled) ──────────────
  $(function () {
    var $modal = $('#aiGeneratePackageModal');
    if (!$modal.length || !window.FB_AI_GENERATE_URL) return;

    var $btn = $('#aiGenerateSubmitBtn');
    var $error = $modal.find('.ai-generate-error');

    function setLoading(isLoading) {
      $btn.prop('disabled', isLoading);
      $btn.find('.btn-label').toggleClass('d-none', isLoading);
      $btn.find('.spinner-border').toggleClass('d-none', !isLoading);
    }

    $btn.on('click', function () {
      $error.addClass('d-none').text('');

      var nights = parseInt($('#ai_nights').val(), 10);
      var payload = {
        destination: $('#ai_destination').val(),
        nights: isNaN(nights) ? 0 : nights,
        theme: $('#ai_theme').val(),
        budget: $('#ai_budget').val(),
        traveller_type: $('#ai_traveller_type').val(),
        extra_instructions: $('#ai_extra_instructions').val()
      };

      if (!payload.destination) {
        $error.removeClass('d-none').text('Please enter a destination.');
        return;
      }

      setLoading(true);

      $.ajax({
        url: window.FB_AI_GENERATE_URL,
        type: 'POST',
        data: payload,
        headers: csrfHeaders(),
        dataType: 'json',
        success: function (response) {
          if (!response.success) {
            $error.removeClass('d-none').text(response.message || 'Something went wrong.');
            return;
          }

          // Nights drives Days via the existing #nights listener in custom.js.
          $('#nights').val(payload.nights).trigger('input');

          applyDraft(response.data);
          $modal.modal('hide');
          if (typeof toastr !== 'undefined') {
            toastr.success('AI draft applied — review each tab before saving.');
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

  // ── Company-owner "AI Generate enabled" switch ─────────────────────────────────
  // Wired independently of the modal above so it still works when the feature is
  // currently OFF (the modal/button aren't rendered then, but the switch is).
  $(document).on('change', '#aiFeatureToggle', function () {
    var $toggle = $(this);
    if (!window.FB_AI_SETTINGS_TOGGLE_URL) return;

    $.ajax({
      url: window.FB_AI_SETTINGS_TOGGLE_URL,
      type: 'POST',
      data: { enabled: $toggle.is(':checked') ? 1 : 0 },
      headers: csrfHeaders(),
      dataType: 'json',
      success: function () {
        if (typeof toastr !== 'undefined') toastr.success('Saved — reload the page to see the change.');
      },
      error: function () {
        $toggle.prop('checked', !$toggle.is(':checked'));
        if (typeof toastr !== 'undefined') toastr.error('Could not update the setting.');
      }
    });
  });
})(jQuery);
