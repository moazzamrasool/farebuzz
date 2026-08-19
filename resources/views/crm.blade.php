@extends('layouts.app')

@php
  $seo = \App\Support\Seo\SeoResolver::resolve(
    null,
    'FareBuzzer CRM – Travel Business Management Software',
    'Run your travel business from one CRM — packages, bookings, enquiries and customers, all in one place.',
    null,
    route('crm.landing'),
  );
@endphp
@include('partials._seo_head', ['seo' => $seo])

@section('content')
<style>
  .crm-hero {
    padding: 70px 0;
    background: linear-gradient(135deg, #050e24 0%, #0a1d4a 55%, #0d2d73 100%);
    color: #fff;
  }
  .crm-hero-tag {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.22);
    border-radius: 24px; padding: 6px 16px; font-size: 12px; font-weight: 600;
    letter-spacing: 0.5px; color: rgba(255,255,255,0.85); margin-bottom: 18px;
  }
  .crm-hero-title { font-size: clamp(28px, 4vw, 46px); font-weight: 800; line-height: 1.15; letter-spacing: -1px; margin-bottom: 16px; }
  .crm-hero-sub { font-size: 16px; color: rgba(255,255,255,0.75); line-height: 1.6; max-width: 520px; margin-bottom: 28px; }
  .btn-crm-primary {
    display: inline-flex; align-items: center; gap: 8px;
    background: linear-gradient(135deg, #1352cc 0%, #0046d5 100%);
    color: #fff; border: none; border-radius: 10px; padding: 14px 28px;
    font-size: 15px; font-weight: 700; text-decoration: none; letter-spacing: 0.3px;
    box-shadow: 0 4px 14px rgba(0,70,213,0.35); transition: all 0.2s;
  }
  .btn-crm-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,70,213,0.45); color: #fff; }
  .crm-hero-card {
    background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15);
    border-radius: 18px; padding: 32px; backdrop-filter: blur(6px);
  }
  .crm-hero-card ul { list-style: none; margin: 0; padding: 0; }
  .crm-hero-card li { display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.1); font-size: 14px; color: rgba(255,255,255,0.85); }
  .crm-hero-card li:last-child { border-bottom: none; }
  .crm-hero-card li i { color: #f47b20; font-size: 18px; }

  .crm-stat { padding: 12px; }
  .crm-stat h3 { font-size: 32px; font-weight: 800; color: #0046d5; margin-bottom: 4px; }
  .crm-stat p { font-size: 13px; color: #666; font-weight: 500; margin: 0; }

  .crm-step { text-align: center; padding: 8px; }
  .crm-step-num {
    width: 52px; height: 52px; border-radius: 50%; background: #eef3ff; color: #0046d5;
    display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800;
    margin: 0 auto 16px;
  }
  .crm-step h6 { font-size: 16px; font-weight: 700; color: #111; margin-bottom: 6px; }
  .crm-step p { font-size: 13px; color: #666; line-height: 1.5; margin: 0; }

  .crm-form-card { background: #fff; border-radius: 18px; padding: 40px; box-shadow: 0 4px 30px rgba(0,0,0,0.06); }
  .crm-form-card .form-control, .crm-form-card .form-select {
    height: 48px; border: 1.5px solid #e7ebf3; border-radius: 10px; font-size: 14px;
    background: #fafbff;
  }
  .crm-form-card textarea.form-control { height: auto; }
  .crm-form-card .form-control:focus, .crm-form-card .form-select:focus {
    border-color: #005fcc; box-shadow: 0 0 0 3px rgba(0,95,204,0.10);
  }
  .crm-form-card label { font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px; }

  .crm-form-alert {
    display: none; border-radius: 10px; font-size: 13.5px; padding: 12px 14px;
    margin-bottom: 18px; align-items: center; gap: 8px;
  }
  .crm-form-alert.show { display: flex; }
  .crm-form-alert-success { background: #f0fff4; border: 1px solid #9ae6b4; color: #276749; }
  .crm-form-alert-error   { background: #fff5f5; border: 1px solid #fcc; color: #c0392b; }
</style>

{{-- ══════════ HERO ══════════ --}}
<section class="crm-hero">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-7">
        <span class="crm-hero-tag"><i class="bi bi-stars"></i> Built for Travel Businesses</span>
        <h1 class="crm-hero-title">One CRM to Run Your Entire Travel Business</h1>
        <p class="crm-hero-sub">
          Leads, bookings, customers and reports — all in one powerful dashboard built for travel
          agencies, tour operators and airlines. Request access below and our team will get you onboarded.
        </p>
        <a href="#enquiry-form" class="btn-crm-primary">Request Access <i class="bi bi-arrow-right"></i></a>
      </div>
      <div class="col-lg-5">
        <div class="crm-hero-card">
          <ul>
            <li><i class="bi bi-check-circle-fill"></i> Centralized lead &amp; enquiry management</li>
            <li><i class="bi bi-check-circle-fill"></i> Built to scale across multiple businesses</li>
            <li><i class="bi bi-check-circle-fill"></i> Simple approval-based onboarding</li>
            <li><i class="bi bi-check-circle-fill"></i> Your own unique business ID &amp; workspace</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ══════════ STATS ══════════ --}}
<section class="section-pad">
  <div class="container">
    <div class="row text-center g-4">
      <div class="col-6 col-md-3"><div class="crm-stat"><h3>500+</h3><p>Businesses Onboard</p></div></div>
      <div class="col-6 col-md-3"><div class="crm-stat"><h3>2M+</h3><p>Leads Managed</p></div></div>
      <div class="col-6 col-md-3"><div class="crm-stat"><h3>99.9%</h3><p>Uptime</p></div></div>
      <div class="col-6 col-md-3"><div class="crm-stat"><h3>24/7</h3><p>Support</p></div></div>
    </div>
  </div>
</section>

{{-- ══════════ FEATURES ══════════ --}}
<section class="section-pad" style="background:#f8f9fc;">
  <div class="container">
    <h2 class="section-title text-center mb-5">Everything Your Business Needs</h2>
    <div class="row g-3">
      <div class="col-6 col-md-4">
        <div class="pro-card">
          <div class="pro-card-header">
            <h6><i class="bi bi-people-fill" style="color:#0046d5;"></i> Lead Management</h6>
            <p>Capture, track and follow up on every enquiry from a single inbox.</p>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-4">
        <div class="pro-card">
          <div class="pro-card-header">
            <h6><i class="bi bi-calendar-check-fill" style="color:#047857;"></i> Booking Management</h6>
            <p>Manage flights, hotels, holidays and more from one dashboard.</p>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-4">
        <div class="pro-card">
          <div class="pro-card-header">
            <h6><i class="bi bi-diagram-3-fill" style="color:#7c3aed;"></i> Multi-Business Ready</h6>
            <p>Built to support multiple independent businesses on one platform.</p>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-4">
        <div class="pro-card">
          <div class="pro-card-header">
            <h6><i class="bi bi-graph-up-arrow" style="color:#c2410c;"></i> Reports &amp; Analytics</h6>
            <p>Track performance with real-time dashboards and insights.</p>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-4">
        <div class="pro-card">
          <div class="pro-card-header">
            <h6><i class="bi bi-shield-lock-fill" style="color:#be123c;"></i> Secure &amp; Reliable</h6>
            <p>Your business data stays isolated, safe and always available.</p>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-4">
        <div class="pro-card">
          <div class="pro-card-header">
            <h6><i class="bi bi-headset" style="color:#0f766e;"></i> Dedicated Support</h6>
            <p>Our team is on call to help you get the most out of the CRM.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ══════════ HOW IT WORKS ══════════ --}}
<section class="section-pad">
  <div class="container">
    <h2 class="section-title text-center mb-5">How It Works</h2>
    <div class="row g-4">
      <div class="col-6 col-md-3">
        <div class="crm-step">
          <div class="crm-step-num">1</div>
          <h6>Submit Enquiry</h6>
          <p>Tell us about your business using the form below.</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="crm-step">
          <div class="crm-step-num">2</div>
          <h6>Our Team Reviews</h6>
          <p>We review every request to keep the platform quality high.</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="crm-step">
          <div class="crm-step-num">3</div>
          <h6>Get Approved</h6>
          <p>Once approved, your business is set up on the CRM.</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="crm-step">
          <div class="crm-step-num">4</div>
          <h6>Go Live</h6>
          <p>Receive your unique Business ID and connect your website.</p>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ══════════ ENQUIRY FORM ══════════ --}}
<section class="section-pad" id="enquiry-form" style="background:#f8f9fc;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="crm-form-card">
          <h2 class="section-title text-center">Request CRM Access</h2>
          <p class="text-center text-muted mb-4">Fill the form below and our team will reach out to onboard your business.</p>

          <div id="crm-form-alert" class="crm-form-alert"></div>

          <form id="crm-enquiry-form">
            @csrf
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="business_name">Business Name</label>
                <input type="text" class="form-control" id="business_name" name="business_name" placeholder="e.g. Skyline Travels" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="owner_name">Owner Name</label>
                <input type="text" class="form-control" id="owner_name" name="owner_name" placeholder="Your full name" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="email">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="you@business.com" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="phone">Phone Number</label>
                <input type="text" class="form-control" id="phone" name="phone" maxlength="20" placeholder="Phone number" required>
              </div>
              <div class="col-md-12 mb-3">
                <label for="business_type">Business Type</label>
                <select class="form-select" id="business_type" name="business_type">
                  <option value="">Select business type</option>
                  <option value="Travel Agency">Travel Agency</option>
                  <option value="Airline">Airline</option>
                  <option value="Tour Operator">Tour Operator</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div class="col-md-12 mb-3">
                <label for="message">Message</label>
                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Tell us a bit about your business and requirements"></textarea>
              </div>
              <div class="col-md-12">
                <button type="submit" class="btn-crm-primary w-100 justify-content-center">
                  <i class="bi bi-send-fill"></i> Submit Enquiry
                </button>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>

<script>
  (function () {
    var form  = document.getElementById('crm-enquiry-form');
    var alert = document.getElementById('crm-form-alert');

    function showAlert(type, message) {
      alert.textContent = message;
      alert.className = 'crm-form-alert show crm-form-alert-' + type;
      alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    form.addEventListener('submit', function (event) {
      event.preventDefault();

      fetch('{{ route('crm.enquiry.store') }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
        body: new FormData(form),
      })
        .then(function (response) {
          return response.json().then(function (data) {
            return { ok: response.ok, data: data };
          });
        })
        .then(function (result) {
          if (result.ok) {
            form.reset();
            showAlert('success', result.data.success);
          } else {
            var firstError = result.data.errors ? Object.values(result.data.errors)[0][0] : 'Something went wrong. Please try again later.';
            showAlert('error', firstError);
          }
        })
        .catch(function () {
          showAlert('error', 'Something went wrong. Please try again later.');
        });
    });
  })();
</script>
@endsection
