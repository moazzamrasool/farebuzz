<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Redirecting to PayU…</title>
  <style>
    body { font-family:'Inter',Arial,sans-serif; background:#f5f5f5; display:flex; align-items:center; justify-content:center; height:100vh; margin:0; }
    .box { text-align:center; }
    .spinner { width:40px; height:40px; border:4px solid #e0e0e0; border-top-color:#005fcc; border-radius:50%; margin:0 auto 16px; animation:spin 0.8s linear infinite; }
    @keyframes spin { to { transform:rotate(360deg); } }
  </style>
</head>
<body>
  <div class="box">
    <div class="spinner"></div>
    <p>Redirecting you to PayU to complete your payment…</p>
  </div>

  {{-- Standard PayU redirect pattern: auto-submit a hidden form to their checkout URL. --}}
  <form action="{{ $url }}" method="POST" id="payu-form">
    @foreach($params as $key => $value)
      <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
  </form>
  <script>document.getElementById('payu-form').submit();</script>
</body>
</html>
