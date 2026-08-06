  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('crm.dashboard') }}" class="brand-link crm-brand-link">
      <span class="crm-brand-text">Fare<span>Buzzer</span> <small>CRM</small></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          @if(Auth::guard('admin')->user())
            <span class="crm-avatar-badge crm-avatar-badge-lg">{{ strtoupper(substr(Auth::guard('admin')->user()->name, 0, 1)) }}</span>
          @endif
        </div>
        <div class="info">
          @if(Auth::guard('admin')->user())
            <a href="#" class="d-block">{{ Auth::guard('admin')->user()->name }}</a>
          @endif
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

          @php $currentAdmin = Auth::guard('admin')->user(); @endphp

          <li class="nav-item menu-open">
            <a href="{{ route('crm.dashboard') }}" class="nav-link {{ request()->is('crm/dashboard') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          @if($currentAdmin?->isSuperAdmin())
            <li class="nav-item">
              <a href="{{ route('crm.enquiries.index') }}" class="nav-link {{ request()->is('crm/enquiries*') ? 'active' : '' }}">
                <i class="fa fa-briefcase" aria-hidden="true"></i>
                <p>Business Enquiries</p>
              </a>
            </li>
          @endif

          {{-- Leads/CRM — deliberately outside the "Master Data" @unless(isSuperAdmin) block
               below, since Super Admin can see every tenant's leads (see routes/admin.php:
               these routes sit outside admin.company on purpose). --}}
          @if($currentAdmin?->isSuperAdmin() || $currentAdmin?->isAdmin() || $currentAdmin?->can('leads.view'))
            @php $leadsActive = request()->is('crm/package-enquiries*', 'crm/follow-ups*', 'crm/message-templates*', 'crm/leads-dashboard*', 'crm/whatsapp-settings*'); @endphp
            <li class="nav-item {{ $leadsActive ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ $leadsActive ? 'active' : '' }}">
                <i class="fa fa-address-book" aria-hidden="true"></i>
                <p>
                  Leads
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ route('crm.package-enquiries.index') }}" class="nav-link {{ request()->is('crm/package-enquiries*') ? 'active' : '' }}">
                    <i class="fa fa-envelope-open-text nav-icon" aria-hidden="true"></i>
                    <p>All Leads</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('crm.follow-ups.due') }}" class="nav-link {{ request()->is('crm/follow-ups*') ? 'active' : '' }}">
                    <i class="fa fa-bell nav-icon" aria-hidden="true"></i>
                    <p>Follow-ups Due</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('crm.leads.dashboard') }}" class="nav-link {{ request()->is('crm/leads-dashboard*') ? 'active' : '' }}">
                    <i class="fa fa-chart-pie nav-icon" aria-hidden="true"></i>
                    <p>CRM Dashboard</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('crm.message-templates.index') }}" class="nav-link {{ request()->is('crm/message-templates*') ? 'active' : '' }}">
                    <i class="fa fa-file-alt nav-icon" aria-hidden="true"></i>
                    <p>Message Templates</p>
                  </a>
                </li>
                @if($currentAdmin?->isAdmin())
                <li class="nav-item">
                  <a href="{{ route('crm.whatsapp-settings.edit') }}" class="nav-link {{ request()->is('crm/whatsapp-settings*') ? 'active' : '' }}">
                    <i class="fab fa-whatsapp nav-icon" aria-hidden="true"></i>
                    <p>WhatsApp Bot</p>
                  </a>
                </li>
                @endif
              </ul>
            </li>
          @endif

          @unless($currentAdmin?->isSuperAdmin())
            @php $masterDataActive = request()->is('crm/travel-categories*', 'crm/destinations*', 'crm/holiday-packages*', 'crm/packages*', 'crm/activities*', 'crm/amenities*', 'crm/hotels*', 'crm/hotel-reviews*', 'crm/inclusions*', 'crm/exclusions*', 'crm/bookings*'); @endphp
            <li class="nav-item {{ $masterDataActive ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ $masterDataActive ? 'active' : '' }}">
                <i class="fa fa-layer-group" aria-hidden="true"></i>
                <p>
                  Master Data
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ route('crm.travel-categories.index') }}" class="nav-link {{ request()->is('crm/travel-categories*') ? 'active' : '' }}">
                    <i class="fa fa-tags nav-icon" aria-hidden="true"></i>
                    <p>Categories</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('crm.destinations.index') }}" class="nav-link {{ request()->is('crm/destinations*') ? 'active' : '' }}">
                    <i class="fa fa-map-marker-alt nav-icon" aria-hidden="true"></i>
                    <p>Destinations</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('crm.activities.index') }}" class="nav-link {{ request()->is('crm/activities*') ? 'active' : '' }}">
                    <i class="fa fa-hiking nav-icon" aria-hidden="true"></i>
                    <p>Activities</p>
                  </a>
                </li>
                @if($currentAdmin?->isAdmin() || $currentAdmin?->can('amenities.view'))
                <li class="nav-item">
                  <a href="{{ route('crm.amenities.index') }}" class="nav-link {{ request()->is('crm/amenities*') ? 'active' : '' }}">
                    <i class="fa fa-wifi nav-icon" aria-hidden="true"></i>
                    <p>Amenities</p>
                  </a>
                </li>
                @endif
                @if($currentAdmin?->isAdmin() || $currentAdmin?->can('inclusions.view'))
                <li class="nav-item">
                  <a href="{{ route('crm.inclusions.index') }}" class="nav-link {{ request()->is('crm/inclusions*') ? 'active' : '' }}">
                    <i class="fa fa-check-circle nav-icon" aria-hidden="true"></i>
                    <p>Inclusions</p>
                  </a>
                </li>
                @endif
                @if($currentAdmin?->isAdmin() || $currentAdmin?->can('exclusions.view'))
                <li class="nav-item">
                  <a href="{{ route('crm.exclusions.index') }}" class="nav-link {{ request()->is('crm/exclusions*') ? 'active' : '' }}">
                    <i class="fa fa-times-circle nav-icon" aria-hidden="true"></i>
                    <p>Exclusions</p>
                  </a>
                </li>
                @endif
                @if($currentAdmin?->isAdmin() || $currentAdmin?->can('hotels.view'))
                <li class="nav-item">
                  <a href="{{ route('crm.hotels.index') }}" class="nav-link {{ request()->is('crm/hotels*') && !request()->is('crm/hotel-reviews*') ? 'active' : '' }}">
                    <i class="fa fa-hotel nav-icon" aria-hidden="true"></i>
                    <p>Hotels</p>
                  </a>
                </li>
                @endif
                @if($currentAdmin?->isAdmin() || $currentAdmin?->can('hotel-reviews.view'))
                <li class="nav-item">
                  <a href="{{ route('crm.hotel-reviews.index') }}" class="nav-link {{ request()->is('crm/hotel-reviews*') ? 'active' : '' }}">
                    <i class="fa fa-star nav-icon" aria-hidden="true"></i>
                    <p>Hotel Reviews</p>
                  </a>
                </li>
                @endif
                @if($currentAdmin?->isAdmin() || $currentAdmin?->can('holiday-packages.view'))
                <li class="nav-item">
                  <a href="{{ route('crm.holiday-packages.index') }}" class="nav-link {{ request()->is('crm/holiday-packages*') ? 'active' : '' }}">
                    <i class="fa fa-suitcase-rolling nav-icon" aria-hidden="true"></i>
                    <p>Holiday Packages</p>
                  </a>
                </li>
                @endif
                @if($currentAdmin?->isAdmin() || $currentAdmin?->can('bookings.view'))
                <li class="nav-item">
                  <a href="{{ route('crm.bookings.index') }}" class="nav-link {{ request()->is('crm/bookings*') ? 'active' : '' }}">
                    <i class="fa fa-ticket-alt nav-icon" aria-hidden="true"></i>
                    <p>Bookings</p>
                  </a>
                </li>
                @endif
              </ul>
            </li>
          @endunless

          @unless($currentAdmin?->isSuperAdmin())
            @php $homepageCmsActive = request()->is('crm/homepage-sections*', 'crm/cms-pages*', 'crm/blogs*', 'crm/navbar-menu*', 'crm/about-page*', 'crm/coupons*', 'crm/seo-settings*', 'crm/tracking-scripts*'); @endphp
            <li class="nav-item {{ $homepageCmsActive ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ $homepageCmsActive ? 'active' : '' }}">
                <i class="fa fa-home nav-icon" aria-hidden="true"></i>
                <p>
                  Homepage / CMS
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                @if($currentAdmin?->isAdmin() || $currentAdmin?->can('homepage-sections.view'))
                <li class="nav-item">
                  <a href="{{ route('crm.homepage-sections.index') }}" class="nav-link {{ request()->is('crm/homepage-sections*') ? 'active' : '' }}">
                    <i class="fa fa-th-large nav-icon" aria-hidden="true"></i>
                    <p>Homepage Sections</p>
                  </a>
                </li>
                @endif
                @if($currentAdmin?->isAdmin() || $currentAdmin?->can('coupons.view'))
                <li class="nav-item">
                  <a href="{{ route('crm.coupons.index') }}" class="nav-link {{ request()->is('crm/coupons*') ? 'active' : '' }}">
                    <i class="fa fa-tags nav-icon" aria-hidden="true"></i>
                    <p>Coupons / Offers</p>
                  </a>
                </li>
                @endif
                @if($currentAdmin?->isAdmin() || $currentAdmin?->can('about-page.view'))
                <li class="nav-item">
                  <a href="{{ route('crm.about-page.edit') }}" class="nav-link {{ request()->is('crm/about-page*') ? 'active' : '' }}">
                    <i class="fa fa-info-circle nav-icon" aria-hidden="true"></i>
                    <p>About Us</p>
                  </a>
                </li>
                @endif
                @if($currentAdmin?->isAdmin() || $currentAdmin?->can('cms-pages.view'))
                <li class="nav-item">
                  <a href="{{ route('crm.cms-pages.index') }}" class="nav-link {{ request()->is('crm/cms-pages*') ? 'active' : '' }}">
                    <i class="fa fa-file-alt nav-icon" aria-hidden="true"></i>
                    <p>CMS Pages</p>
                  </a>
                </li>
                @endif
                @if($currentAdmin?->isAdmin() || $currentAdmin?->can('blog.view'))
                <li class="nav-item">
                  <a href="{{ route('crm.blogs.index') }}" class="nav-link {{ request()->is('crm/blogs*') ? 'active' : '' }}">
                    <i class="fa fa-blog nav-icon" aria-hidden="true"></i>
                    <p>Blog</p>
                  </a>
                </li>
                @endif
                @if($currentAdmin?->isAdmin() || $currentAdmin?->can('navbar-menu.view'))
                <li class="nav-item">
                  <a href="{{ route('crm.navbar-menu.index') }}" class="nav-link {{ request()->is('crm/navbar-menu*') ? 'active' : '' }}">
                    <i class="fa fa-bars nav-icon" aria-hidden="true"></i>
                    <p>Navbar Menu</p>
                  </a>
                </li>
                @endif
                @if($currentAdmin?->isAdmin() || $currentAdmin?->can('settings.view'))
                <li class="nav-item">
                  <a href="{{ route('crm.seo-settings.edit') }}" class="nav-link {{ request()->is('crm/seo-settings*') ? 'active' : '' }}">
                    <i class="fa fa-search-dollar nav-icon" aria-hidden="true"></i>
                    <p>SEO Settings</p>
                  </a>
                </li>
                @endif
                @if($currentAdmin?->isAdmin())
                <li class="nav-item">
                  <a href="{{ route('crm.tracking-scripts.edit') }}" class="nav-link {{ request()->is('crm/tracking-scripts*') ? 'active' : '' }}">
                    <i class="fa fa-code nav-icon" aria-hidden="true"></i>
                    <p>Tracking &amp; Scripts</p>
                  </a>
                </li>
                @endif
              </ul>
            </li>
          @endunless

          @if($currentAdmin?->isSuperAdmin())
            <li class="nav-item">
              <a href="{{ route('crm.admins.index') }}" class="nav-link {{ request()->is('crm/admins*') ? 'active' : '' }}">
                <i class="fa fa-building" aria-hidden="true"></i>
                <p>Companies</p>
              </a>
            </li>
          @endif

          @if($currentAdmin?->isAdmin() || $currentAdmin?->can('roles.view'))
            <li class="nav-item">
              <a href="{{ route('crm.roles.index') }}" class="nav-link {{ request()->is('crm/roles*') ? 'active' : '' }}">
                <i class="fa fa-user-shield" aria-hidden="true"></i>
                <p>Roles &amp; Permissions</p>
              </a>
            </li>
          @endif

          @if($currentAdmin?->isAdmin() || $currentAdmin?->can('users.view'))
            <li class="nav-item">
              <a href="{{ route('crm.users.index') }}" class="nav-link {{ request()->is('crm/users*') ? 'active' : '' }}">
                <i class="fa fa-users" aria-hidden="true"></i>
                <p>Users</p>
              </a>
            </li>
          @endif

          @if($currentAdmin?->isAdmin() || $currentAdmin?->can('customers.view'))
            <li class="nav-item">
              <a href="{{ route('crm.customers.index') }}" class="nav-link {{ request()->is('crm/customers*') ? 'active' : '' }}">
                <i class="fa fa-address-book" aria-hidden="true"></i>
                <p>Customers</p>
              </a>
            </li>
          @endif

          <li class="nav-item">
            <a href="{{ route('crm.logout') }}" class="nav-link">
              <i class="fa fa-sign-out-alt" aria-hidden="true"></i>
              <p>Logout</p>
            </a>
          </li>

        </ul>
      </nav>
    </div>
  </aside>
