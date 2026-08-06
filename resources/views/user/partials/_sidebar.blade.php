@php $u = auth()->user(); @endphp
<aside class="dash-sidebar">
  <div class="dash-brand">Fare<span>Buzzer</span></div>

  <div class="dash-user">
    <div class="avatar">
      @if($u->avatar)
        <img src="{{ asset('storage/'.$u->avatar) }}" alt="{{ $u->name }}">
      @else
        {{ strtoupper(substr($u->name, 0, 1)) }}
      @endif
    </div>
    <div>
      <div class="name">{{ $u->name }}</div>
      <div class="email">{{ $u->email }}</div>
    </div>
  </div>

  <ul class="dash-nav">
    <li><a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}"><i class="bi bi-grid"></i> Dashboard</a></li>
    <li><a href="{{ route('user.bookings.index') }}" class="{{ request()->routeIs('user.bookings.*') ? 'active' : '' }}"><i class="bi bi-briefcase"></i> My Bookings</a></li>
    <li><a href="{{ route('user.enquiries.index') }}" class="{{ request()->routeIs('user.enquiries.*') ? 'active' : '' }}"><i class="bi bi-chat-dots"></i> My Enquiries</a></li>
    <li><a href="{{ route('user.trips.index') }}" class="{{ request()->routeIs('user.trips.*') ? 'active' : '' }}"><i class="bi bi-airplane"></i> My Trips</a></li>
    <li><a href="{{ route('user.profile.edit') }}" class="{{ request()->routeIs('user.profile.*') ? 'active' : '' }}"><i class="bi bi-person"></i> My Profile</a></li>
    <li class="logout"><a href="{{ route('user.logout') }}"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
  </ul>
</aside>
