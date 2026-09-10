@extends('layouts.landing')

@php
  $seo = \App\Support\Seo\SeoResolver::resolve(
    null,
    'Kashmir Tour Packages from Bangalore | 6D/5N',
    'Plan a 6-day Kashmir holiday from Bangalore with a clear itinerary, package inclusions and custom quote from Fare Buzzer Travel.',
    null,
    route('landing.kashmir-bangalore'),
  );
@endphp
@include('partials._seo_head', ['seo' => $seo])

@push('styles')
  <link rel="stylesheet" href="{{ asset('frontend/asset/css/landing-kashmir-bangalore.css') }}">
@endpush

@section('content')
 <div class="top">Bangalore departure <b>•</b> Srinagar arrival <b>•</b> 6 days / 5 nights</div>
  <header class="head">
    <a class="brand" href="#top"><img src="{{ asset('frontend/img/logo.png') }}" height="60px"/></a>
    <nav class="nav"><a href="#itinerary">Itinerary</a><a href="#inclusions">Inclusions</a><a href="#travel">From Bangalore</a></nav>
    <a class="call" href="tel:+918447843676">☎ Call Now</a>
  </header>

  <section class="hero" id="top"><div class="hero-grid shell">
    <div>
      <div class="crumb">Home / Packages / Kashmir from Bangalore</div>
      <span class="kicker">Kashmir, made clear</span>
      <h1>Kashmir Tour Package from Bangalore</h1>
      <p class="lead">Plan a 6-day Kashmir holiday from Bangalore with a practical Srinagar-based itinerary, clear inclusions and a quote shaped around your dates.</p>
      <div class="facts">
        <div><span>Starting from</span><strong>₹15,500<sup>*</sup></strong><small>per person</small></div>
        <div><span>Duration</span><strong>6D / 5N</strong><small>fixed plan, flexible dates</small></div>
        <div><span>Stay</span><strong>3-star</strong><small>hotel category</small></div>
      </div>
      <div class="chips"><span><b>✓</b> Hotel stays</span><span><b>✓</b> Breakfast &amp; dinner</span><span><b>✓</b> Local transfers</span><span class="air">✈ Airfare not included</span></div>
      <div class="actions"><a class="btn gold" href="tel:+918447843676">☎ Call Now</a><a class="btn wa" href="https://wa.me/918447843676" target="_blank">WhatsApp</a><a class="btn outline" href="#quote">Get Custom Quote →</a></div>
      <p class="fine">*Starting price only. Final quote varies by dates, availability, room choice and traveller count. No percentage-discount claim is applied.</p>
    </div>
    <aside class="card" id="quote">
      <div id="formwrap"><span class="kicker">Plan with a travel expert</span><h2>Get your custom quote</h2><p class="intro">Four details. No long form.</p>
        <div class="form-alert" id="form-alert"></div>
        <form class="form" id="quoteform" method="POST" action="{{ route('landing.kashmir-bangalore.enquiry') }}" novalidate>
        @csrf

        {{-- Honeypot: hidden from real visitors via CSS, left empty. A filled value fails validation server-side. --}}
        <label class="honeypot-field" aria-hidden="true">Website
            <input type="text" name="website" tabindex="-1" autocomplete="off">
        </label>

        <label>Name
            <input type="text" name="name" required placeholder="Your name">
            <span class="form-error" data-error-for="name"></span>
        </label>

        <label>Phone
            <input type="tel" name="phone" required placeholder="10-digit mobile number" pattern="[0-9+ -]{10,15}">
            <span class="form-error" data-error-for="phone"></span>
        </label>

        <label>Email <small style="font-weight:400;text-transform:none;letter-spacing:0;color:#8b9a9f">(optional)</small>
            <input type="email" name="email" placeholder="you@example.com">
            <span class="form-error" data-error-for="email"></span>
        </label>

        <div class="pair">

            <label>Travel month
                <select name="travel_month" required>
                    <option value="">Select</option>
                    <option>September 2026</option>
                    <option>October 2026</option>
                    <option>November 2026</option>
                    <option>December 2026</option>
                    <option>Later / Flexible</option>
                </select>
                <span class="form-error" data-error-for="travel_month"></span>
            </label>

            <label>Travellers
                <select name="travellers">
                    <option>1 person</option>
                    <option selected>2 people</option>
                    <option>3 people</option>
                    <option>4 people</option>
                    <option>5+ people</option>
                </select>
                <span class="form-error" data-error-for="travellers"></span>
            </label>

        </div>

        <button class="btn submit" type="submit">Request Callback →</button>

        <div class="callback">● Travel expert will call during business hours.</div>

    </form>
      </div>
      <div class="success" id="success"><div class="tick">✓</div><span class="kicker">Request received</span><h2>Thank you</h2><p class="section-copy">A travel expert will call you during business hours.</p></div>
      <div class="safe">Your final written quote should confirm price, hotel, transport and every inclusion before payment.</div>
    </aside>
  </div></section>

  <section class="strip"><div class="strip-grid shell"><div><strong>₹15,500</strong><span>consistent starting price</span></div><div><strong>No airfare</strong><span>excluded from base price</span></div><div><strong>Written scope</strong><span>inclusions before payment</span></div><div><strong>No fake discount</strong><span>transparent offer language</span></div></div></section>

  <section class="section ivory" id="itinerary"><div class="shell"><div class="section-head"><div><span class="kicker">Your route, day by day</span><h2>Six thoughtfully paced days in Kashmir</h2></div><p>A balanced starting plan with Srinagar as the arrival and departure point. Activities and local access remain subject to season, weather and road conditions.</p></div><div class="days">
    <article class="day"><div class="num"><small>Day</small>01</div><div><h3>Arrive in Srinagar</h3><p>Meet at Srinagar, transfer to the hotel and ease into the valley with time for a relaxed local experience.</p></div></article>
    <article class="day"><div class="num"><small>Day</small>02</div><div><h3>Gulmarg day trip</h3><p>A scenic drive to Gulmarg. Optional activities are paid directly unless added to your quote.</p></div></article>
    <article class="day"><div class="num"><small>Day</small>03</div><div><h3>Pahalgam stay</h3><p>Travel through the Kashmir countryside to Pahalgam and settle in for an overnight stay.</p></div></article>
    <article class="day"><div class="num"><small>Day</small>04</div><div><h3>Pahalgam to Srinagar</h3><p>A flexible morning in Pahalgam followed by the return to Srinagar for the night.</p></div></article>
    <article class="day"><div class="num"><small>Day</small>05</div><div><h3>Sonmarg day trip</h3><p>Visit Sonmarg, subject to weather and road conditions, then return to Srinagar.</p></div></article>
    <article class="day"><div class="num"><small>Day</small>06</div><div><h3>Departure</h3><p>Check out and transfer to the agreed Srinagar drop point for your onward journey.</p></div></article>
  </div></div></section>

  <section class="section route" id="travel"><div class="route-grid shell"><div class="route-copy"><span class="kicker">Bangalore to Srinagar</span><h2>Book flights after your ground plan is confirmed</h2><p class="section-copy">Search Bengaluru (BLR) to Srinagar (SXR) for your chosen dates and compare nonstop and one-stop options. Share the confirmed schedule so pickup and drop timing can be aligned.</p><div class="airline"><div><b>BLR</b><span>Bengaluru</span></div><div class="plane">✈</div><div><b>SXR</b><span>Srinagar</span></div></div></div><div class="route-notes"><h3>Before you book</h3><ul><li>Airfare is separate from the ₹15,500 starting price.</li><li>Keep a reasonable buffer between arrival and pickup.</li><li>Confirm baggage and change terms with the airline.</li><li>Request the exact Srinagar pickup point in writing.</li></ul></div></div></section>

  <section class="section" id="inclusions"><div class="shell"><div class="section-head"><div><span class="kicker">Know before you go</span><h2>What the base package covers</h2></div><p>Fare Buzzer should verify every item against the supplier quote before publishing.</p></div><div class="scope-grid"><article class="scope"><h3>✓ Included in the sample</h3><ul><li>5 nights in the stated 3-star hotel category</li><li>Breakfast and dinner as specified in the quote</li><li>Local transfers in the quoted vehicle category</li><li>Sightseeing route in the confirmed itinerary</li><li>Pickup and drop at the agreed Srinagar point</li></ul></article><article class="scope no"><h3>× Not included in the base price</h3><ul><li>Bangalore–Srinagar return airfare</li><li>Optional rides, activities and local union vehicles</li><li>Lunches, personal expenses and room upgrades</li><li>Travel insurance unless added in writing</li><li>Anything not expressly listed in the final scope</li></ul></article></div></div></section>

  <section class="section ivory"><div class="trust-grid shell"><div><span class="kicker">Trust must be provable</span><h2>No invented ratings. No unsupported promises.</h2><p class="section-copy">This sample avoids “Best Price Guarantee,” “Thousands of happy travellers,” “Free Cancellation” and “Destination experts” until Fare Buzzer can provide evidence and exact terms.</p></div><div class="evidence"><span class="pill">Verification pending</span><h3>Customer review area</h3><p>Publish reviews only after matching them to a genuine Google Business Profile, booking record or permissioned customer message.</p><div class="checkline">Reviewer identity or source link</div><div class="checkline">Travel month and booked package</div><div class="checkline">Permission to display the review</div></div></div></section>

  <section class="section"><div class="faq-grid shell"><div><span class="kicker">Good questions, clear answers</span><h2>Before you enquire</h2><p class="section-copy">These answers keep the offer clear from ad click to callback.</p></div><div class="faq">
    <article class="q open"><button type="button">Is airfare from Bangalore included? <b>−</b></button><p class="answer">No. The ₹15,500 starting price excludes Bangalore–Srinagar airfare.</p></article>
    <article class="q"><button type="button">Is ₹15,500 the final price for every date? <b>+</b></button><p class="answer">No. It is a per-person starting price. Final cost depends on dates, group size, availability and room type.</p></article>
    <article class="q"><button type="button">Can the itinerary be customised? <b>+</b></button><p class="answer">Yes. Share your month, traveller count and priorities, then confirm every change in the written quote.</p></article>
    <article class="q"><button type="button">When will I receive a callback? <b>+</b></button><p class="answer">A travel expert will call during business hours.</p></article>
  </div></div></section>

  <section class="bottom"><div class="bottom-grid shell"><div><span class="kicker">Your Kashmir plan starts here</span><h2>Tell us your month. We’ll shape the details.</h2><p>Get the itinerary, hotel category, price and exclusions confirmed in one written quote.</p></div><div class="actions"><a class="btn gold" href="tel:+918447843676">☎ Call Now</a><a class="btn outline" href="https://wa.me/918447843676">WhatsApp</a></div></div></section>
  <footer class="foot"><div class="foot-grid shell"><a class="brand" href="#top"><img src="{{ asset('frontend/img/logo.png') }}" height="60px"/></a><p></p></div></footer>
  <div class="sticky"><a href="tel:+918447843676">☎ &nbsp; Call Now</a><a href="https://wa.me/918447843676">WhatsApp</a></div>
@endsection

@push('scripts')
<script>
  (function () {
    var form     = document.getElementById('quoteform');
    var formwrap = document.getElementById('formwrap');
    var success  = document.getElementById('success');
    var alertBox = document.getElementById('form-alert');
    var submitBtn = form.querySelector('.submit');
    var submitLabel = submitBtn.textContent;

    function clearErrors() {
      alertBox.classList.remove('show');
      alertBox.textContent = '';
      form.querySelectorAll('.form-error').forEach(function (el) {
        el.classList.remove('show');
        el.textContent = '';
      });
    }

    function showFieldErrors(errors) {
      Object.keys(errors).forEach(function (field) {
        var el = form.querySelector('[data-error-for="' + field + '"]');
        if (el) {
          el.textContent = errors[field][0];
          el.classList.add('show');
        }
      });
    }

    function showAlert(message) {
      alertBox.textContent = message;
      alertBox.classList.add('show');
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      clearErrors();
      submitBtn.disabled = true;
      submitBtn.textContent = 'Sending...';

      fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
        },
        body: new FormData(form),
      })
        .then(function (response) {
          return response.json().then(function (data) {
            return { status: response.status, data: data };
          });
        })
        .then(function (result) {
          if (result.status === 200) {
            formwrap.style.display = 'none';
            success.style.display = 'flex';
            // TODO: fire a conversion event here (GTM dataLayer.push / Meta Pixel fbq('track', 'Lead')).
          } else if (result.status === 422) {
            showFieldErrors(result.data.errors || {});
            showAlert('Please check the highlighted fields.');
          } else {
            showAlert('Something went wrong. Please call us instead.');
          }
        })
        .catch(function () {
          showAlert('Something went wrong. Please check your connection and try again.');
        })
        .finally(function () {
          submitBtn.disabled = false;
          submitBtn.textContent = submitLabel;
        });
    });

    document.querySelectorAll('.q button').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var item = btn.parentElement;
        var was = item.classList.contains('open');
        document.querySelectorAll('.q').forEach(function (q) {
          q.classList.remove('open');
          q.querySelector('b').textContent = '+';
        });
        if (!was) {
          item.classList.add('open');
          btn.querySelector('b').textContent = '−';
        }
      });
    });
  })();
</script>
@endpush
