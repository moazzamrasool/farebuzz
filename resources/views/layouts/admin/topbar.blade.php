  <nav class="main-header navbar navbar-expand crm-topbar">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ route('crm.dashboard') }}" class="nav-link">Dashboard</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#">
          @if(Auth::guard('admin')->user())
            <span class="crm-avatar-badge mr-2">{{ strtoupper(substr(Auth::guard('admin')->user()->name, 0, 1)) }}</span>
            <span class="d-none d-md-inline">{{ Auth::guard('admin')->user()->name }}</span>
          @endif
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <a href="{{ route('crm.logout') }}" class="dropdown-item">
            <i class="fa fa-sign-out-alt mr-2" aria-hidden="true"></i> Logout
          </a>
        </div>
      </li>
    </ul>
  </nav>
