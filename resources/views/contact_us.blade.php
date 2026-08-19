@extends('layouts.app')

@php
  $seo = \App\Support\Seo\SeoResolver::resolve(
    $contactPage,
    'Contact Us – FareBuzzer',
    'Get in touch with FareBuzzer Travel — reach out for holiday packages, hotel bookings and travel support.',
    null,
    route('contact_us'),
  );
@endphp
@include('partials._seo_head', ['seo' => $seo])

@push('styles')
<style>
  :root { --fb-blue:#005fcc; --fb-orange:#f47b20; }

  .contact-hero {
    background: linear-gradient(135deg, #050e24 0%, #0a1d4a 55%, #0d2d73 100%);
    padding: 64px 0 100px;
    position: relative;
    overflow: hidden;
  }
  .contact-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url('https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?w=1800&q=80') center/cover no-repeat;
    opacity: 0.15;
  }
  .contact-hero-inner { position: relative; z-index: 2; text-align: center; }
  .contact-hero h1 { color: #fff; font-size: clamp(26px, 4vw, 40px); font-weight: 800; letter-spacing: -0.5px; margin-bottom: 10px; }
  .contact-hero h1 span { color: var(--fb-orange); }
  .contact-hero p { color: rgba(255,255,255,0.7); font-size: 14px; max-width: 560px; margin: 0 auto; }

  .contact-wrap { margin-top: -64px; position: relative; z-index: 3; padding-bottom: 64px; }

  .contact-info-card {
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    display: flex;
    gap: 14px;
    align-items: flex-start;
    box-shadow: 0 4px 16px rgba(0,0,0,0.05);
    border: 1px solid #eef1f8;
    margin-bottom: 16px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .contact-info-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.09); }
  .contact-info-icon {
    width: 46px; height: 46px; border-radius: 12px;
    background: #f0f6ff; color: var(--fb-blue);
    display: flex; align-items: center; justify-content: center;
    font-size: 19px; flex-shrink: 0;
  }
  .contact-info-card h6 { font-size: 13px; font-weight: 700; color: #111; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 4px; }
  .contact-info-card p, .contact-info-card a { font-size: 14px; color: #444; margin: 0; text-decoration: none; line-height: 1.5; }
  .contact-info-card a:hover { color: var(--fb-blue); }

  .contact-form-card {
    background: #fff;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 16px 48px rgba(0,0,0,0.08);
    border: 1px solid #eef1f8;
    height: 100%;
  }
  .contact-form-card h4 { font-weight: 800; font-size: 22px; color: #111; margin-bottom: 6px; }
  .contact-form-card .sub { color: #666; font-size: 13px; margin-bottom: 22px; }

  .cf-label { font-size: 11px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block; }
  .cf-control {
    border: 1.5px solid #e2e6f0; border-radius: 10px; padding: 12px 14px;
    font-size: 14px; width: 100%; outline: none; background: #fbfbfd;
    transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
  }
  .cf-control:focus { border-color: var(--fb-blue); box-shadow: 0 0 0 3px rgba(0,95,204,0.1); background: #fff; }
  textarea.cf-control { resize: vertical; min-height: 110px; }

  .btn-contact-submit {
    background: linear-gradient(135deg, #0d5cff 0%, #0046d5 100%);
    color: #fff; border: none; border-radius: 10px;
    padding: 13px 20px; font-size: 15px; font-weight: 700;
    width: 100%; cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    box-shadow: 0 6px 20px rgba(0,70,213,0.25);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .btn-contact-submit:hover { transform: translateY(-2px); box-shadow: 0 10px 26px rgba(0,70,213,0.35); }
  .btn-contact-submit:active { transform: translateY(0); }

  .contact-map-card {
    border-radius: 20px; overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,0.06);
    border: 1px solid #eef1f8;
  }
  .contact-map-card iframe { display: block; width: 100%; }

  @media (max-width: 767px) {
    .contact-wrap { margin-top: -44px; }
    .contact-form-card { padding: 22px; }
    .contact-hero { padding: 48px 0 80px; }
  }
</style>
@endpush

@section('content')

<div class="contact-hero">
  <div class="container contact-hero-inner">
    <h1>Get In <span>Touch</span></h1>
    <p>Have a question about flights, hotels or holiday packages? Our travel experts at FareBuzzer are here to help you plan your next trip.</p>
  </div>
</div>

<div class="contact-wrap">
  <div class="container">
    <div class="row g-4 align-items-stretch">
      <div class="col-lg-5">

        @if($contactPage->address)
        <div class="contact-info-card">
          <div class="contact-info-icon"><i class="bi bi-geo-alt-fill"></i></div>
          <div>
            <h6>Address</h6>
            <p>{{ $contactPage->address }}</p>
          </div>
        </div>
        @endif

        @if($contactPage->phone)
        <div class="contact-info-card">
          <div class="contact-info-icon"><i class="bi bi-telephone-fill"></i></div>
          <div>
            <h6>Phone</h6>
            <a href="tel:{{ $contactPage->phone }}">{{ $contactPage->phone }}</a>
          </div>
        </div>
        @endif

        <div class="contact-info-card">
          <div class="contact-info-icon"><i class="bi bi-envelope-fill"></i></div>
          <div>
            <h6>Email Id</h6>
            <a href="mailto:{{ $contactPage->email ?: 'info@farebuzzertravel.com' }}">{{ $contactPage->email ?: 'info@farebuzzertravel.com' }}</a>
          </div>
        </div>

        @if($contactPage->support_hours)
        <div class="contact-info-card">
          <div class="contact-info-icon"><i class="bi bi-clock-fill"></i></div>
          <div>
            <h6>Support Hours</h6>
            <p>{{ $contactPage->support_hours }}</p>
          </div>
        </div>
        @endif

      </div>

      <div class="col-lg-7">
        <div class="contact-form-card">
          <h4>Send us a Message</h4>
          <p class="sub">Fill out the form below and our team will get back to you within 24 hours.</p>
          <form id="contact_form">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label class="cf-label">Name</label>
                <input type="text" class="cf-control" name="name" id="name" required placeholder="Your full name">
              </div>
              <div class="col-md-6">
                <label class="cf-label">Email Id</label>
                <input type="email" class="cf-control" name="email" id="email" required placeholder="you@example.com">
              </div>
              <div class="col-md-12">
                <label class="cf-label">Phone Number</label>
                <input type="tel" class="cf-control" name="phone" id="phone" required placeholder="10-digit mobile number" maxlength="10" inputmode="numeric" pattern="[0-9]{10}">
              </div>
              <div class="col-md-12">
                <label class="cf-label">Message</label>
                <textarea id="message" required name="message" class="cf-control" placeholder="Tell us how we can help..." rows="4"></textarea>
              </div>
              <input type="hidden" name="form_type" value="contact_us">
              <div class="col-md-12">
                <button type="submit" value="submit" name="submit" class="btn-contact-submit mt-2">
                  <i class="bi bi-send-fill"></i> Submit
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    @if($contactPage->resolvedMapEmbedUrl())
    <div class="row mt-4">
      <div class="col-12">
        <div class="contact-map-card">
          <iframe src="{{ $contactPage->resolvedMapEmbedUrl() }}" height="420" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </div>
    @endif

  </div>
</div>

<script>
     $('#contact_form').submit((event)=>{

            event.preventDefault();
            let formData = new FormData($('#contact_form')[0]);
            $.ajax({
                type: 'POST',
                url: '{{route('submit_contact_form')}}',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                success: function(response){
                    $('#contact_form')[0].reset();
                    Swal.fire(
                        'Thank you!',
                        'Your query has been submitted successfully. We will get back to you soon.',
                        'success'
                    )
                },
                error: function(error){
                    Swal.fire(
                        'Oops!',
                        'Something went wrong. Please try again later.',
                        'error'
                    )
                }
            });
        })
</script>
@endsection
