<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign Up – FareBuzzer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('frontend/asset/css/style.css') }}"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; }
    html, body { height: 100%; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }

    .auth-wrapper { min-height: 100vh; display: flex; }

    /* ── Left Image Panel ── */
    .auth-left {
      display: none;
      width: 52%;
      flex-shrink: 0;
      position: relative;
      overflow: hidden;
      background:
        linear-gradient(160deg, rgba(5,14,36,0.68) 0%, rgba(13,45,115,0.80) 55%, rgba(244,123,32,0.25) 100%),
        url('https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=1400&q=80') center/cover no-repeat;
    }
    @media (min-width: 900px) { .auth-left { display: flex; flex-direction: column; } }

    .auth-left-inner { display: flex; flex-direction: column; height: 100%; padding: 48px 52px; color: #fff; }
    .auth-brand { display: inline-flex; align-items: center; text-decoration: none; background: #fff; padding: 10px 18px; border-radius: 12px; }
    .auth-brand img { height: 32px; width: auto; display: block; }

    .auth-left-body { flex: 1; display: flex; flex-direction: column; justify-content: center; gap: 20px; }

    .auth-left-tag {
      display: inline-flex; align-items: center; gap: 8px;
      background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.22);
      border-radius: 24px; padding: 6px 16px; font-size: 12px; font-weight: 600;
      letter-spacing: 0.5px; color: rgba(255,255,255,0.85); width: fit-content;
    }
    .auth-left-headline {
      font-size: clamp(26px, 3.2vw, 42px); font-weight: 800; line-height: 1.15;
      letter-spacing: -1px; margin: 0;
    }
    .auth-left-headline span { color: #f47b20; }
    .auth-left-sub { font-size: 15px; color: rgba(255,255,255,0.7); line-height: 1.6; max-width: 380px; }

    .auth-benefits { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 12px; }
    .auth-benefits li { display: flex; align-items: center; gap: 12px; font-size: 14px; color: rgba(255,255,255,0.85); font-weight: 500; }
    .benefit-icon {
      width: 32px; height: 32px; border-radius: 50%;
      background: rgba(244,123,32,0.25); border: 1px solid rgba(244,123,32,0.4);
      display: flex; align-items: center; justify-content: center;
      font-size: 14px; color: #f47b20; flex-shrink: 0;
    }
    .auth-left-footer { font-size: 12px; color: rgba(255,255,255,0.4); }

    /* ── Right Form Panel ── */
    .auth-right {
      flex: 1; display: flex; align-items: center; justify-content: center;
      background: #fff; padding: 40px 24px; overflow-y: auto;
    }
    .auth-form-box { width: 100%; max-width: 420px; }

    .auth-mobile-logo {
      display: block; font-size: 24px; font-weight: 800; color: #005fcc;
      text-decoration: none; letter-spacing: -0.5px; margin-bottom: 28px;
    }
    .auth-mobile-logo span { color: #f47b20; }
    @media (min-width: 900px) { .auth-mobile-logo { display: none; } }

    .auth-title { font-size: 26px; font-weight: 800; color: #111; margin-bottom: 4px; letter-spacing: -0.5px; }
    .auth-subtitle { font-size: 14px; color: #666; margin-bottom: 24px; }

    /* Alerts */
    .auth-alert {
      border-radius: 10px; font-size: 13.5px; padding: 12px 14px;
      margin-bottom: 18px; display: flex; align-items: center; gap: 8px;
    }
    .auth-alert-error   { background: #fff5f5; border: 1px solid #fcc; color: #c0392b; }
    .auth-alert-success { background: #f0fff4; border: 1px solid #9ae6b4; color: #276749; }

    /* Fields */
    .auth-field { position: relative; margin-bottom: 14px; }
    .auth-field .field-icon {
      position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
      color: #aaa; font-size: 16px; pointer-events: none;
    }
    .auth-field input {
      width: 100%; height: 50px; border: 1.5px solid #e7ebf3; border-radius: 10px;
      padding: 0 14px 0 42px; font-size: 14px; font-family: 'Inter', sans-serif;
      color: #222; background: #fafbff; transition: border-color 0.2s, box-shadow 0.2s; outline: none;
    }
    .auth-field input:focus { border-color: #005fcc; background: #fff; box-shadow: 0 0 0 3px rgba(0,95,204,0.10); }
    .auth-field input.is-invalid { border-color: #e74c3c; }
    .auth-field .field-err { font-size: 12px; color: #e74c3c; margin-top: 5px; display: flex; align-items: center; gap: 4px; }

    .password-hint { font-size: 11.5px; color: #999; margin-top: -6px; margin-bottom: 10px; padding-left: 2px; }

    /* Submit */
    .btn-auth {
      width: 100%; height: 50px; background: linear-gradient(135deg, #1352cc 0%, #0046d5 100%);
      color: #fff; border: none; border-radius: 10px; font-size: 15px; font-weight: 700;
      font-family: 'Inter', sans-serif; cursor: pointer; letter-spacing: 0.3px;
      transition: all 0.2s; display: flex; align-items: center; justify-content: center;
      gap: 8px; box-shadow: 0 4px 14px rgba(0,70,213,0.30); margin-top: 4px;
    }
    .btn-auth:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,70,213,0.40); }
    .btn-auth:active { transform: translateY(0); }

    /* Divider */
    .auth-divider {
      display: flex; align-items: center; gap: 12px; margin: 20px 0;
      color: #bbb; font-size: 12px; font-weight: 500;
    }
    .auth-divider::before, .auth-divider::after { content: ''; flex: 1; height: 1px; background: #e7ebf3; }

    /* Social */
    .social-btns { display: flex; gap: 10px; }
    .btn-social {
      flex: 1; height: 44px; border-radius: 10px; border: 1.5px solid #e7ebf3;
      background: #fff; display: flex; align-items: center; justify-content: center;
      gap: 7px; font-size: 13px; font-weight: 600; font-family: 'Inter', sans-serif;
      color: #333; cursor: pointer; transition: all 0.18s; text-decoration: none;
    }
    .btn-social:hover { background: #f8f9ff; border-color: #c5d0ee; color: #111; transform: translateY(-1px); }
    .btn-social i { font-size: 17px; }
    .btn-social.google   i { color: #db4437; }
    .btn-social.facebook i { color: #1877f2; }
    .btn-social.apple    i { color: #111; }

    .auth-terms {
      font-size: 11.5px; color: #999; text-align: center; margin-top: 14px; line-height: 1.5;
    }
    .auth-terms a { color: #005fcc; text-decoration: none; }
    .auth-terms a:hover { text-decoration: underline; }

    .auth-footer-link { text-align: center; margin-top: 16px; font-size: 14px; color: #666; }
    .auth-footer-link a { color: #005fcc; font-weight: 600; text-decoration: none; }
    .auth-footer-link a:hover { text-decoration: underline; }
  </style>
</head>
<body>
<div class="auth-wrapper">

  {{-- Left Image Panel --}}
  <div class="auth-left">
    <div class="auth-left-inner">
      <a href="{{ route('home') }}" class="auth-brand"><img src="{{ asset('frontend/img/logo.png') }}" alt="FareBuzzer"></a>
      <div class="auth-left-body">
        <div class="auth-left-tag">
          <i class="bi bi-stars"></i> Join Millions of Travellers
        </div>
        <h2 class="auth-left-headline">
          Start Your <span>Journey</span><br>Today
        </h2>
        <p class="auth-left-sub">
          Create your account and enjoy exclusive deals, faster bookings, and personalized travel recommendations.
        </p>
        <ul class="auth-benefits">
          <li><span class="benefit-icon"><i class="bi bi-tag-fill"></i></span> Members-only exclusive discounts</li>
          <li><span class="benefit-icon"><i class="bi bi-lightning-fill"></i></span> Instant booking confirmation</li>
          <li><span class="benefit-icon"><i class="bi bi-headset"></i></span> 24/7 dedicated travel support</li>
          <li><span class="benefit-icon"><i class="bi bi-shield-check-fill"></i></span> Secure & encrypted transactions</li>
        </ul>
      </div>
      <div class="auth-left-footer">© {{ date('Y') }} FareBuzzer. All rights reserved.</div>
    </div>
  </div>

  {{-- Right Form Panel --}}
  <div class="auth-right">
    <div class="auth-form-box">

      <a href="{{ route('home') }}" class="auth-mobile-logo">Fare<span>Buzzer</span></a>

      <h1 class="auth-title">Create Account</h1>
      <p class="auth-subtitle">Register for free — no credit card required</p>

      @if(Session::has('error'))
        <div class="auth-alert auth-alert-error">
          <i class="bi bi-exclamation-circle-fill"></i> {{ Session::get('error') }}
        </div>
      @endif

      <form action="{{ route('user.processRegister') }}" method="POST" novalidate>
        @csrf

        <div class="auth-field">
          <i class="bi bi-person field-icon"></i>
          <input type="text" name="username" placeholder="Full Name"
            value="{{ old('username') }}"
            class="{{ $errors->has('username') ? 'is-invalid' : '' }}"
            autocomplete="name"/>
          @error('username')
            <div class="field-err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
          @enderror
        </div>

        <div class="auth-field">
          <i class="bi bi-envelope field-icon"></i>
          <input type="email" name="email" placeholder="Email Address"
            value="{{ old('email') }}"
            class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
            autocomplete="email"/>
          @error('email')
            <div class="field-err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
          @enderror
        </div>

        <div class="auth-field">
          <i class="bi bi-lock field-icon"></i>
          <input type="password" name="password" placeholder="Create Password"
            class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
            autocomplete="new-password"/>
          @error('password')
            <div class="field-err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
          @enderror
        </div>
        <p class="password-hint"><i class="bi bi-info-circle"></i> Minimum 5 characters</p>

        <div class="auth-field">
          <i class="bi bi-lock-fill field-icon"></i>
          <input type="password" name="password_confirmation" placeholder="Confirm Password"
            class="{{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
            autocomplete="new-password"/>
          @error('password_confirmation')
            <div class="field-err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
          @enderror
        </div>

        <button type="submit" class="btn-auth">
          <i class="bi bi-person-plus-fill"></i> Create Account
        </button>
      </form>

      <div class="auth-divider">Or sign up with</div>

      <div class="social-btns">
        <a href="{{ route('social.redirect', 'google') }}" class="btn-social google">
          <i class="bi bi-google"></i> Google
        </a>
        <a href="{{ route('social.redirect', 'facebook') }}" class="btn-social facebook">
          <i class="bi bi-facebook"></i> Facebook
        </a>
        <a href="{{ route('social.redirect', 'apple') }}" class="btn-social apple">
          <i class="bi bi-apple"></i> Apple
        </a>
      </div>

      <p class="auth-terms">
        By signing up, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
      </p>

      <div class="auth-footer-link">
        Already have an account? <a href="{{ route('user.login') }}">Login</a>
      </div>

    </div>
  </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
