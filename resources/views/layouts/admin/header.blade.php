@php
  // The public navbar — CRM-manageable via /crm/navbar-menu (Homepage / CMS group).
  // Top-level items with children render as a hover dropdown; everything else is a
  // plain link. forSite() scopes to the current tenant, same convention as the footer.
  $navItems = \App\Models\NavbarMenuItem::forSite()->active()->topLevel()
    ->with(['children' => fn ($q) => $q->active()->orderBy('sort_order')])
    ->orderBy('sort_order')->get();
@endphp
<nav class="navbar-top">
  <div class="navbar-inner">
    <a class="nav-logo" href="{{ route('home') }}"><img src="{{ asset('frontend/img/logo.png') }}" alt="FareBuzzer"></a>
    <div class="nav-links">
      @foreach($navItems as $item)
        @if($item->children->isNotEmpty())
          <div class="nav-item-dropdown">
            <a href="{{ $item->resolvedUrl }}" class="{{ $item->isCurrent ? 'active' : '' }} has-children"
              @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif>{{ $item->label }}</a>
            <div class="nav-dropdown-menu">
              @foreach($item->children as $child)
                <a href="{{ $child->resolvedUrl }}" class="{{ $child->isCurrent ? 'active' : '' }}"
                  @if($child->open_in_new_tab) target="_blank" rel="noopener" @endif>{{ $child->label }}</a>
              @endforeach
            </div>
          </div>
        @else
          <a href="{{ $item->resolvedUrl }}" class="{{ $item->isCurrent ? 'active' : '' }}"
            @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif>{{ $item->label }}</a>
        @endif
      @endforeach
    </div>
    <div class="nav-right">
      <a href="{{ route('home') }}#searchTabs" class="nav-search-icon"><i class="bi bi-search"></i></a>
      <a href="{{ route('user.login') }}" class="btn-login">Login</a>
      <button class="nav-hamburger" onclick="document.getElementById('mobileMenu').classList.toggle('open')" aria-label="Menu">
        <i class="bi bi-list"></i>
      </button>
    </div>
  </div>
  <div class="mobile-menu" id="mobileMenu">
    @foreach($navItems as $item)
      <a href="{{ $item->resolvedUrl }}" class="{{ $item->isCurrent ? 'active' : '' }}"
        @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif>{{ $item->label }}</a>
      @foreach($item->children as $child)
        <a href="{{ $child->resolvedUrl }}" class="mobile-submenu-item {{ $child->isCurrent ? 'active' : '' }}"
          @if($child->open_in_new_tab) target="_blank" rel="noopener" @endif>{{ $child->label }}</a>
      @endforeach
    @endforeach
    <a href="{{ route('user.login') }}">Login</a>
  </div>
</nav>
