<nav class="navbar-top">
  <div class="navbar-inner">
    <a class="nav-logo" href="#"><img src="{{ asset('frontend/img/logo.png') }}" alt="FareBuzzer"></a>
    <div class="nav-links">
      <a href="#">India Packages</a>
      <a href="#">International Packages</a>
      <a href="#">Activities</a>
      <a href="#">Mice</a>
    </div>
    <div class="nav-right">
      <span class="nav-search-icon"><i class="bi bi-search"></i></span>
      <a href="#" class="btn-login">Login</a>
      <button class="nav-hamburger" onclick="document.getElementById('mobileMenu').classList.toggle('open')" aria-label="Menu">
        <i class="bi bi-list"></i>
      </button>
    </div>
  </div>
  <div class="mobile-menu" id="mobileMenu">
    <a href="#">India Packages</a>
    <a href="#">International Packages</a>
    <a href="#">Activities</a>
    <a href="#">Mice</a>
    <a href="#">Login</a>
  </div>
</nav>
