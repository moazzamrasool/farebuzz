<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, follow">
  <title>Site Unavailable</title>
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Segoe UI', Inter, Arial, sans-serif;
      background: linear-gradient(135deg, #050e24 0%, #0a1d4a 55%, #0d2d73 100%);
      color: #fff;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }
    .card {
      max-width: 480px;
      width: 100%;
      text-align: center;
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      padding: 48px 32px;
    }
    .card img {
      max-width: 160px;
      margin-bottom: 24px;
    }
    .card h1 {
      font-size: 22px;
      margin: 0 0 12px;
      font-weight: 700;
    }
    .card p {
      font-size: 15px;
      line-height: 1.6;
      color: #c7cede;
      margin: 0 0 8px;
    }
    .badge {
      display: inline-block;
      margin-top: 20px;
      padding: 6px 16px;
      border-radius: 999px;
      background: rgba(244, 123, 32, 0.15);
      color: #f47b20;
      font-size: 13px;
      font-weight: 600;
      letter-spacing: 0.02em;
    }
    .support {
      margin-top: 24px;
      font-size: 13px;
      color: #8b93ac;
    }
    .support a {
      color: #6fa8ff;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="card">
    <img src="{{ asset('frontend/img/logo.png') }}" alt="Logo">
    <h1>Site Temporarily Unavailable</h1>
    <p>This site is currently offline for maintenance. Please check back soon.</p>
    <span class="badge">We'll be back shortly</span>
    @php($supportEmail = config('mail.from.address'))
    @if($supportEmail)
      <p class="support">Need help now? Contact <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></p>
    @endif
  </div>
</body>
</html>
