{{-- "Have a coupon code?" widget — reusable on both the package and hotel booking
     pages. $couponEndpoint is the per-page apply-coupon route. Must be rendered
     INSIDE the booking <form> so it can read the CSRF token + the form's own
     fields (room_type_id/adults/... or check_in_date/rooms_count/...) via
     `new FormData(form)` without duplicating field names here. --}}
<div class="bk-coupon-box" style="border:1.5px dashed #dbe2ea;border-radius:8px;padding:12px 14px;margin-bottom:16px;">
  <div id="couponIdleRow">
    <button type="button" id="couponToggleBtn" style="background:none;border:none;padding:0;color:var(--blue);font-weight:700;font-size:13px;cursor:pointer;">
      <i class="bi bi-tag"></i> Have a coupon code?
    </button>
  </div>

  <div id="couponInputRow" class="d-flex align-items-center mt-1" style="display:none !important;gap:8px;">
    <input type="text" id="couponCodeInput" class="bk-input" placeholder="Enter coupon code" style="text-transform:uppercase;flex:1;">
    <button type="button" id="couponApplyBtn" class="btn" style="background:var(--blue);color:#fff;border:none;border-radius:6px;padding:9px 16px;font-size:13px;font-weight:700;white-space:nowrap;">Apply</button>
  </div>

  <div id="couponAppliedRow" class="d-flex align-items-center justify-content-between" style="display:none;">
    <span style="color:#16a34a;font-weight:700;font-size:13px;"><i class="bi bi-check-circle-fill"></i> <span id="couponAppliedCode"></span> applied</span>
    <button type="button" id="couponRemoveBtn" style="background:none;border:none;color:#dc2626;font-size:12px;font-weight:600;cursor:pointer;">Remove</button>
  </div>

  <div id="couponMessage" style="font-size:12px;margin-top:6px;"></div>
  <input type="hidden" name="coupon_code" id="couponCodeHidden" value="">
</div>

<script>
  (function () {
    var box = document.currentScript.previousElementSibling;
    var form = box.closest('form');
    var endpoint = {!! json_encode($couponEndpoint) !!};

    var idleRow = box.querySelector('#couponIdleRow');
    var inputRow = box.querySelector('#couponInputRow');
    var appliedRow = box.querySelector('#couponAppliedRow');
    var input = box.querySelector('#couponCodeInput');
    var applyBtn = box.querySelector('#couponApplyBtn');
    var removeBtn = box.querySelector('#couponRemoveBtn');
    var appliedCodeEl = box.querySelector('#couponAppliedCode');
    var message = box.querySelector('#couponMessage');
    var hiddenCode = box.querySelector('#couponCodeHidden');

    function setMessage(text, isError) {
      message.textContent = text || '';
      message.style.color = isError ? '#dc2626' : '#16a34a';
    }

    function refreshSummary() {
      if (typeof window.recalcBookingSummary === 'function') window.recalcBookingSummary();
    }

    box.querySelector('#couponToggleBtn').addEventListener('click', function () {
      idleRow.style.display = 'none';
      inputRow.style.display = 'flex';
      input.focus();
    });

    applyBtn.addEventListener('click', function () {
      var code = (input.value || '').trim();
      if (!code) return;

      applyBtn.disabled = true;
      applyBtn.textContent = 'Applying...';
      setMessage('');

      var formData = new FormData(form);
      formData.set('code', code);

      fetch(endpoint, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: formData,
      })
        .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
        .then(function (result) {
          applyBtn.disabled = false;
          applyBtn.textContent = 'Apply';

          if (!result.ok || !result.data.valid) {
            window.appliedCouponDiscount = 0;
            setMessage(result.data.message || 'This coupon could not be applied.', true);
            refreshSummary();
            return;
          }

          window.appliedCouponDiscount = result.data.discount;
          hiddenCode.value = result.data.code;
          appliedCodeEl.textContent = result.data.code;
          inputRow.style.display = 'none';
          appliedRow.style.display = 'flex';
          setMessage('Coupon applied! You saved ₹' + Number(result.data.discount).toLocaleString('en-IN', { maximumFractionDigits: 2 }) + '.', false);
          refreshSummary();
        })
        .catch(function () {
          applyBtn.disabled = false;
          applyBtn.textContent = 'Apply';
          setMessage('Something went wrong. Please try again.', true);
        });
    });

    removeBtn.addEventListener('click', function () {
      window.appliedCouponDiscount = 0;
      hiddenCode.value = '';
      input.value = '';
      appliedRow.style.display = 'none';
      idleRow.style.display = 'block';
      setMessage('');
      refreshSummary();
    });
  })();
</script>
