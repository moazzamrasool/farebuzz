<!DOCTYPE html>
<html lang="en">
@include('layouts.head')
<body>
@include('layouts.partials.tracking_body_open')
<style>
  :root { --blue:#005fcc; --orange:#f47b20; }
  body { font-family:'Inter',sans-serif; background:#f5f5f5; margin:0; }
  .dash-shell { display:flex; min-height:100vh; }

  .dash-sidebar { width:250px; background:#fff; border-right:1px solid #eee; flex-shrink:0; position:sticky; top:0; height:100vh; overflow-y:auto; }
  .dash-brand { padding:22px 24px; font-size:20px; font-weight:800; border-bottom:1px solid #f0f0f0; }
  .dash-brand span { color:var(--orange); }
  .dash-user { padding:16px 24px; border-bottom:1px solid #f0f0f0; display:flex; align-items:center; gap:10px; }
  .dash-user .avatar { width:38px; height:38px; border-radius:50%; background:#f0f6ff; color:var(--blue); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:15px; overflow:hidden; }
  .dash-user .avatar img { width:100%; height:100%; object-fit:cover; }
  .dash-user .name { font-size:13px; font-weight:700; color:#111; }
  .dash-user .email { font-size:11px; color:#888; }

  .dash-nav { padding:14px 12px; list-style:none; margin:0; }
  .dash-nav li { margin-bottom:2px; }
  .dash-nav a { display:flex; align-items:center; gap:12px; padding:11px 14px; border-radius:10px; color:#444; text-decoration:none; font-size:14px; font-weight:600; transition:background .15s,color .15s; }
  .dash-nav a i { width:18px; text-align:center; color:#999; }
  .dash-nav a:hover { background:#f5f8ff; color:var(--blue); }
  .dash-nav a.active { background:var(--blue); color:#fff; }
  .dash-nav a.active i { color:#fff; }
  .dash-nav .logout a { color:#dc2626; }
  .dash-nav .logout a i { color:#dc2626; }

  .dash-main { flex:1; padding:28px 32px; min-width:0; }
  .dash-mobile-bar { display:none; }

  /* Reusable dashboard building blocks — shared by every user/*.blade.php page */
  .dash-page-title { font-size:22px; font-weight:800; margin-bottom:20px; }
  .dash-card { background:#fff; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.06); padding:24px; margin-bottom:20px; }
  .stat-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:18px; margin-bottom:24px; }
  .stat-card { background:#fff; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.06); padding:20px 22px; }
  .stat-card .icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:19px; margin-bottom:10px; }
  .stat-card .value { font-size:26px; font-weight:800; color:#111; }
  .stat-card .label { font-size:12px; color:#888; font-weight:600; text-transform:uppercase; letter-spacing:.4px; }

  .dash-table { width:100%; border-collapse:collapse; }
  .dash-table th { text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:.4px; color:#999; padding:10px 12px; border-bottom:2px solid #f0f0f0; }
  .dash-table td { padding:12px; font-size:13px; border-bottom:1px solid #f5f5f5; vertical-align:middle; }
  .badge-status { display:inline-block; padding:3px 12px; border-radius:20px; font-size:11px; font-weight:700; }
  .badge-green { background:#dcfce7; color:#16a34a; }
  .badge-amber { background:#fef3c7; color:#b45309; }
  .badge-red { background:#fee2e2; color:#dc2626; }
  .badge-gray { background:#f1f1f1; color:#666; }
  .badge-blue { background:#dbeafe; color:#1d4ed8; }
  .empty-state { text-align:center; padding:40px 20px; color:#999; font-size:14px; }

  @media (max-width: 991px) {
    .dash-sidebar { position:fixed; left:-260px; top:0; z-index:1050; box-shadow:0 0 20px rgba(0,0,0,.15); transition:left .2s; }
    .dash-sidebar.open { left:0; }
    .dash-mobile-bar { display:flex; align-items:center; justify-content:space-between; background:#fff; padding:14px 18px; border-bottom:1px solid #eee; position:sticky; top:0; z-index:1040; }
    .dash-mobile-bar .brand { font-weight:800; }
    .dash-main { padding:18px; }
    .dash-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:1045; }
    .dash-backdrop.show { display:block; }
  }
</style>

<div class="dash-mobile-bar">
  <span class="brand">Fare<span style="color:var(--orange);">Buzzer</span> Dashboard</span>
  <button class="btn btn-sm btn-outline-primary" id="dash-menu-btn"><i class="bi bi-list"></i></button>
</div>
<div class="dash-backdrop" id="dash-backdrop"></div>

<div class="dash-shell">
  @include('user.partials._sidebar')

  <main class="dash-main">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @yield('content')
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  var menuBtn = document.getElementById('dash-menu-btn');
  var sidebar = document.querySelector('.dash-sidebar');
  var backdrop = document.getElementById('dash-backdrop');
  function toggleSidebar() { sidebar.classList.toggle('open'); backdrop.classList.toggle('show'); }
  if (menuBtn) menuBtn.addEventListener('click', toggleSidebar);
  if (backdrop) backdrop.addEventListener('click', toggleSidebar);
</script>
@stack('scripts')
@include('layouts.partials.tracking_footer')
</body>
</html>
