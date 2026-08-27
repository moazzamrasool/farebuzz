<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Forgot Password – FareBuzzer</title>
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
      width: 55%;
      flex-shrink: 0;
      position: relative;
      overflow: hidden;
      background:
        linear-gradient(160deg, rgba(5,14,36,0.72) 0%, rgba(10,29,74,0.82) 60%, rgba(13,45,115,0.90) 100%),
        url('https://images.unsplash.com/photo-1488085061387-422e29b40080?w=1400&q=80') center/cover no-repeat;
    }
    @media (min-width: 900px) { .auth-left { display: flex; flex-direction: column; } }

    .auth-left-inner {
      display: flex;
      flex-direction: column;
      height: 100%;
      padding: 48px 52px;
      color: #fff;
    }
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
      font-size: clamp(28px, 3.5vw, 44px); font-weight: 800; line-height: 1.15;
      letter-spacing: -1px; margin: 0;
    }
    .auth-left-headline span { color: #f47b20; }
    .auth-left-sub { font-size: 15px; color: rgba(255,255,255,0.7); line-height: 1.6; max-width: 380px; }

    .auth-left-stats { display: flex; gap: 32px; margin-top: 8px; }
    .stat-item { display: flex; flex-direction: column; gap: 2px; }
    .stat-num  { font-size: 22px; font-weight: 800; color: #fff; }
    .stat-lbl  { font-size: 11px; color: rgba(255,255,255,0.55); font-weight: 500; letter-spacing: 0.3px; }
    .auth-left-footer { font-size: 12px; color: rgba(255,255,255,0.4); }

    /* ── Right Form Panel ── */
    .auth-right {
      flex: 1; display: flex; align-items: center; justify-content: center;
      background: #fff; padding: 40px 24px; overflow-y: auto;
    }
    .auth-form-box { width: 100%; max-width: 400px; }

    .auth-mobile-logo {
      display: block; font-size: 24px; font-weight: 800; color: #005fcc;
      text-decoration: none; letter-spacing: -0.5px; margin-bottom: 28px;
    }
    .auth-mobile-logo span { color: #f47b20; }
    @media (min-width: 900px) { .auth-mobile-logo { display: none; } }

    .auth-title { font-size: 26px; font-weight: 800; color: #111; margin-bottom: 4px; letter-spacing: -0.5px; }
    .auth-subtitle { font-size: 14px; color: #666; margin-bottom: 28px; }

    /* Alerts */
    .auth-alert {
      border-radius: 10px; font-size: 13.5px; padding: 12px 14px;
      margin-bottom: 18px; display: flex; align-items: center; gap: 8px;
    }
    .auth-alert-error   { background: #fff5f5; border: 1px solid #fcc; color: #c0392b; }
    .auth-alert-success { background: #f0fff4; border: 1px solid #9ae6b4; color: #276749; }

    /* Fields */
    .auth-field { position: relative; margin-bottom: 16px; }
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

    /* Submit */
    .btn-auth {
      width: 100%; height: 50px; background: linear-gradient(135deg, #1352cc 0%, #0046d5 100%);
      color: #fff; border: none; border-radius: 10px; font-size: 15px; font-weight: 700;
      font-family: 'Inter', sans-serif; cursor: pointer; letter-spacing: 0.3px;
      transition: all 0.2s; display: flex; align-items: center; justify-content: center;
      gap: 8px; box-shadow: 0 4px 14px rgba(0,70,213,0.30);
    }
    .btn-auth:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,70,213,0.40); }
    .btn-auth:active { transform: translateY(0); }

    .auth-back-link { display: inline-flex; align-items: center; gap: 6px; font-size: 13.5px; color: #666; text-decoration: none; margin-bottom: 24px; font-weight: 500; }
    .auth-back-link:hover { color: #005fcc; }

    .auth-footer-link { text-align: center; margin-top: 24px; font-size: 14px; color: #666; }
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
          <i class="bi bi-shield-lock-fill"></i> Account Security
        </div>
        <h2 class="auth-left-headline">
          Reset Your<br><span>Password</span>
        </h2>
        <p class="auth-left-sub">
          No worries — it happens. Enter your registered email and we'll send you a link to get back into your account.
        </p>
        <div class="auth-left-stats">
          <div class="stat-item"><span class="stat-num">500+</span><span class="stat-lbl">Destinations</span></div>
          <div class="stat-item"><span class="stat-num">24/7</span><span class="stat-lbl">Support</span></div>
        </div>
      </div>
      <div class="auth-left-footer">© {{ date('Y') }} FareBuzzer. All rights reserved.</div>
    </div>
  </div>

  {{-- Right Form Panel --}}
  <div class="auth-right">
    <div class="auth-form-box">

      <a href="{{ route('home') }}" class="auth-mobile-logo">Fare<span>Buzzer</span></a>

      <a href="{{ route('user.login') }}" class="auth-back-link">
        <i class="bi bi-arrow-left"></i> Back to Login
      </a>

      <h1 class="auth-title">Forgot Password?</h1>
      <p class="auth-subtitle">Enter your email and we'll send you a reset link</p>

      @if(Session::has('error'))
        <div class="auth-alert auth-alert-error">
          <i class="bi bi-exclamation-circle-fill"></i> {{ Session::get('error') }}
        </div>
      @endif
      @if(Session::has('success'))
        <div class="auth-alert auth-alert-success">
          <i class="bi bi-check-circle-fill"></i> {{ Session::get('success') }}
        </div>
      @endif

      <form action="{{ route('user.password.email') }}" method="POST" novalidate>
        @csrf

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

        <button type="submit" class="btn-auth">
          <i class="bi bi-send-fill"></i> Send Reset Link
        </button>
      </form>

      <div class="auth-footer-link">
        Remember your password? <a href="{{ route('user.login') }}">Login</a>
      </div>

    </div>
  </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
