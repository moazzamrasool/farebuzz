@php
  $banner = $section->activeItems('banner')->first();
  $firstEnabledTab = optional($searchTabs->firstWhere('enabled', true))->tab_key ?? optional($searchTabs->first())->tab_key;
  $tabIcons = [
      'flights' => 'bi-airplane-fill', 'hotels' => 'bi-building-fill', 'homestays' => 'bi-house-heart-fill',
      'holidays' => 'bi-briefcase-fill', 'trains' => 'bi-train-front-fill', 'buses' => 'bi-bus-front-fill',
      'cabs' => 'bi-taxi-front-fill', 'activities' => 'bi-compass-fill',
  ];
@endphp
<section class="hero-section">
  <div class="hero-bg" @if($banner?->image) style="background-image:url('{{ \App\Support\MediaUrl::resolve($banner->image) }}')" @endif></div>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <h1 class="hero-title">{{ $section->heading }}</h1>
    <p class="hero-subtitle">{{ $section->subheading }}</p>
    <div class="search-card">

      <!-- TABS -->
      <div class="search-tabs" id="searchTabs">
        @foreach($searchTabs as $tab)
          <button
            class="{{ $tab->tab_key === $firstEnabledTab ? 'active' : '' }} {{ !$tab->enabled ? 'tab-disabled' : '' }}"
            data-tab="{{ $tab->tab_key }}"
            data-enabled="{{ $tab->enabled ? '1' : '0' }}"
            @if(!$tab->enabled) disabled aria-disabled="true" tabindex="-1" @endif
          >
            <span class="tab-icon"><i class="bi {{ $tabIcons[$tab->tab_key] ?? 'bi-compass-fill' }}"></i></span>
            {{ $tab->label }}
            @if(!$tab->enabled && $tab->badge_text)
              <span class="tab-badge">{{ $tab->badge_text }}</span>
            @endif
          </button>
        @endforeach
      </div>

      <!-- ════ FLIGHTS ════ -->
      <div class="tab-panel {{ $firstEnabledTab === 'flights' ? 'active' : '' }}" id="panel-flights">

        <!-- Trip Type Selection (Top) -->
        <div class="trip-type-container d-flex align-items-center justify-content-between flex-wrap gap-2 px-1 pb-3">
          <div class="trip-type-options">
            <label class="trip-type-label">
              <input type="radio" name="fl-trip" id="fl-trip-oneway" checked/>
              <span class="trip-type-custom"></span> One Way
            </label>
            <label class="trip-type-label">
              <input type="radio" name="fl-trip" id="fl-trip-roundtrip"/>
              <span class="trip-type-custom"></span> Round Trip
            </label>
            <label class="trip-type-label">
              <input type="radio" name="fl-trip" id="fl-trip-multicity"/>
              <span class="trip-type-custom"></span> Multi-city
            </label>
          </div>
          <span class="text-muted d-none d-sm-inline" style="font-size: 13px; font-weight: 500;">Book International and Domestic Flights</span>
        </div>

        <!-- Main Search Inputs Row -->
        <div class="mmt-search-group">
          <!-- From -->
          <div class="mmt-field-cell flex-grow-1">
            <span class="mmt-label">From</span>
            <input type="text" id="fl-from" class="mmt-input-main" value="Delhi" placeholder="Enter City"/>
            <span class="mmt-sub" id="fl-from-sub">DEL, Delhi Airport India</span>
          </div>

          <!-- Swap Button -->
          <div class="mmt-swap-cell">
            <button class="mmt-swap-btn" id="swapFlights" title="Swap cities">
              <i class="bi bi-arrow-left-right"></i>
            </button>
          </div>

          <!-- To -->
          <div class="mmt-field-cell flex-grow-1">
            <span class="mmt-label">To</span>
            <input type="text" id="fl-to" class="mmt-input-main" value="Bengaluru" placeholder="Enter City"/>
            <span class="mmt-sub" id="fl-to-sub">BLR, Bengaluru International Airport</span>
          </div>

          <!-- Departure -->
          <div class="mmt-field-cell date-cell">
            <span class="mmt-label">Departure <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="date-display-wrap">
              <span class="mmt-input-val" id="fl-depart-val">Select Date</span>
              <span class="mmt-sub" id="fl-depart-day">Saturday</span>
            </div>
            <input type="date" id="fl-depart" class="mmt-date-hidden"/>
          </div>

          <!-- Return -->
          <div class="mmt-field-cell date-cell disabled-cell" id="fl-return-cell">
            <span class="mmt-label">Return <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="date-display-wrap">
              <span class="mmt-input-val" id="fl-return-val">Tap to add return date</span>
              <span class="mmt-sub" id="fl-return-day">for bigger discounts</span>
            </div>
            <input type="date" id="fl-return" class="mmt-date-hidden"/>
          </div>

          <!-- Travellers & Class -->
          <div class="mmt-field-cell select-cell">
            <span class="mmt-label">Travellers &amp; Class <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="select-display-wrap">
              <span class="mmt-input-val" id="fl-travellers-val">1 Traveller</span>
              <span class="mmt-sub" id="fl-travellers-sub">Economy/Premium Economy</span>
            </div>
            <select id="fl-travellers" class="mmt-select-hidden">
              <option value="1 Traveller &middot; Economy/Premium Economy" selected>1 Traveller &middot; Economy/Premium Economy</option>
              <option value="2 Travellers &middot; Economy/Premium Economy">2 Travellers &middot; Economy/Premium Economy</option>
              <option value="1 Traveller &middot; Business">1 Traveller &middot; Business</option>
              <option value="2 Travellers &middot; Business">2 Travellers &middot; Business</option>
              <option value="1 Traveller &middot; First Class">1 Traveller &middot; First Class</option>
            </select>
          </div>
        </div>

        <!-- Special Fares Row -->
        <div class="special-fares-container">
          <span class="special-fares-title">SPECIAL<br/>FARES:</span>
          <div class="special-fares-list">
            <div class="special-fare-pill active">
              <span class="fare-title">Regular</span>
              <span class="fare-desc">Regular fares</span>
            </div>
            <div class="special-fare-pill">
              <span class="fare-title">Student</span>
              <span class="fare-desc">Extra discounts/baggage</span>
            </div>
            <div class="special-fare-pill">
              <span class="fare-title">Armed Forces</span>
              <span class="fare-desc">Up to ₹ 600 off</span>
            </div>
            <div class="special-fare-pill">
              <span class="fare-title">Have a GST number? <span class="badge-new">new</span></span>
              <span class="fare-desc">Upto 10% Extra Savings!</span>
            </div>
            <div class="special-fare-pill">
              <span class="fare-title">Senior Citizen</span>
              <span class="fare-desc">Up to ₹ 600 off</span>
            </div>
            <div class="special-fare-pill">
              <span class="fare-title">Doctor and Nurses</span>
              <span class="fare-desc">Up to ₹ 600 off</span>
            </div>
          </div>
        </div>

        <!-- Price Drop Protection Banner -->
        <div class="price-drop-banner">
          <div class="d-flex align-items-center gap-2">
            <input type="checkbox" id="chk-price-drop" class="form-check-input mt-0" style="width:16px;height:16px;"/>
            <label for="chk-price-drop" style="font-size:12px; font-weight:600; cursor:pointer;" class="mb-0">
              Add Price Drop Protection <span class="text-muted fw-normal">If the price drops, we'll refund the difference.</span>
              <a href="#" class="text-primary text-decoration-none ms-1">View Details</a>
            </label>
          </div>
          <div class="d-flex align-items-center gap-3">
            <i class="bi bi-shield-fill-check text-primary" style="font-size: 20px;"></i>
            <button class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2 px-3 py-1.5" style="border-radius:20px; font-weight:700; font-size:11px;">
              <i class="bi bi-ticket-perforated"></i> Flight Tracker
            </button>
          </div>
        </div>

        <!-- Centered floating search button -->
        <div class="btn-search-container">
          <button class="btn-search"><i class="bi bi-search"></i> Search</button>
        </div>
      </div>

      <!-- ════ HOTELS ════ -->
      <div class="tab-panel {{ $firstEnabledTab === 'hotels' ? 'active' : '' }}" id="panel-hotels">
        <form method="GET" action="{{ route('hotels.index') }}">
        <div class="mmt-search-group">
          <!-- City/Area -->
          <div class="mmt-field-cell flex-grow-2 fb-autocomplete">
            <span class="mmt-label">City / Area / Hotel Name</span>
            <input type="text" id="ht-city" name="destination" class="mmt-input-main" value="Goa" placeholder="Enter City, Area or Hotel"
                   data-autocomplete="location" data-hidden-target="ht-destination-id" autocomplete="off"/>
            <span class="mmt-sub" id="ht-city-sub">Goa, India</span>
            <input type="hidden" id="ht-destination-id" name="destination_id">
            <div class="fb-autocomplete-menu"></div>
          </div>

          <!-- Check-in -->
          <div class="mmt-field-cell date-cell">
            <span class="mmt-label">Check-in <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="date-display-wrap">
              <span class="mmt-input-val" id="ht-checkin-val">Select Date</span>
              <span class="mmt-sub" id="ht-checkin-day">Select Day</span>
            </div>
            <input type="date" id="ht-checkin" name="checkin" class="mmt-date-hidden" oninput="calcNights()"/>
          </div>

          <!-- Check-out -->
          <div class="mmt-field-cell date-cell" style="position:relative;">
            <span class="nights-badge" id="nightsBadge"></span>
            <span class="mmt-label">Check-out <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="date-display-wrap">
              <span class="mmt-input-val" id="ht-checkout-val">Select Date</span>
              <span class="mmt-sub" id="ht-checkout-day">Select Day</span>
            </div>
            <input type="date" id="ht-checkout" name="checkout" class="mmt-date-hidden" oninput="calcNights()"/>
          </div>

          <!-- Guests & Rooms -->
          <div class="mmt-field-cell select-cell">
            <span class="mmt-label">Guests &amp; Rooms <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="select-display-wrap">
              <span class="mmt-input-val" id="ht-rooms-val">2 Guests</span>
              <span class="mmt-sub" id="ht-rooms-sub">1 Room</span>
            </div>
            <select id="ht-rooms" name="rooms" class="mmt-select-hidden">
              <option value="2-1" selected>2 Guests &middot; 1 Room</option>
              <option value="1-1">1 Guest &middot; 1 Room</option>
              <option value="3-1">3 Guests &middot; 1 Room</option>
              <option value="4-2">4 Guests &middot; 2 Rooms</option>
              <option value="6-3">6 Guests &middot; 3 Rooms</option>
            </select>
          </div>
        </div>

        <!-- Centered floating search button -->
        <div class="btn-search-container">
          <button type="submit" class="btn-search"><i class="bi bi-search"></i> Search</button>
        </div>
        </form>
      </div>

      <!-- ════ HOMESTAYS ════ -->
      <div class="tab-panel {{ $firstEnabledTab === 'homestays' ? 'active' : '' }}" id="panel-homestays">
        <div class="mmt-search-group">
          <!-- Location -->
          <div class="mmt-field-cell flex-grow-2">
            <span class="mmt-label">Location</span>
            <input type="text" id="hs-loc" class="mmt-input-main" value="Coorg" placeholder="Enter City, Town or Area"/>
            <span class="mmt-sub" id="hs-loc-sub">Coorg, Karnataka</span>
          </div>

          <!-- Check-in -->
          <div class="mmt-field-cell date-cell">
            <span class="mmt-label">Check-in <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="date-display-wrap">
              <span class="mmt-input-val" id="hs-checkin-val">Select Date</span>
              <span class="mmt-sub" id="hs-checkin-day">Select Day</span>
            </div>
            <input type="date" id="hs-checkin" class="mmt-date-hidden"/>
          </div>

          <!-- Check-out -->
          <div class="mmt-field-cell date-cell">
            <span class="mmt-label">Check-out <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="date-display-wrap">
              <span class="mmt-input-val" id="hs-checkout-val">Select Date</span>
              <span class="mmt-sub" id="hs-checkout-day">Select Day</span>
            </div>
            <input type="date" id="hs-checkout" class="mmt-date-hidden"/>
          </div>

          <!-- Guests -->
          <div class="mmt-field-cell select-cell">
            <span class="mmt-label">Guests <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="select-display-wrap">
              <span class="mmt-input-val" id="hs-guests-val">1 Guest</span>
              <span class="mmt-sub" id="hs-guests-sub">No. of Guests</span>
            </div>
            <select id="hs-guests" class="mmt-select-hidden">
              <option value="1 Guest" selected>1 Guest</option>
              <option value="2 Guests">2 Guests</option>
              <option value="3 Guests">3 Guests</option>
              <option value="4 Guests">4 Guests</option>
              <option value="5+ Guests">5+ Guests</option>
            </select>
          </div>

          <!-- Property Type -->
          <div class="mmt-field-cell select-cell">
            <span class="mmt-label">Property Type <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="select-display-wrap">
              <span class="mmt-input-val" id="hs-type-val">Homestay</span>
              <span class="mmt-sub" id="hs-type-sub">Type of stay</span>
            </div>
            <select id="hs-type" class="mmt-select-hidden">
              <option value="Homestay" selected>Homestay</option>
              <option value="Villa">Villa</option>
              <option value="Cottage">Cottage</option>
              <option value="Farmhouse">Farmhouse</option>
            </select>
          </div>
        </div>

        <!-- Centered floating search button -->
        <div class="btn-search-container">
          <button class="btn-search"><i class="bi bi-search"></i> Search</button>
        </div>
      </div>

      <!-- ════ HOLIDAY PACKAGES ════ -->
      <div class="tab-panel {{ $firstEnabledTab === 'holidays' ? 'active' : '' }}" id="panel-holidays">
        <form method="GET" action="{{ route('packages.search') }}">
        <div class="mmt-search-group">
          <!-- From City — no departure-city filter exists yet on packages; captured for later use, not filtered on -->
          <div class="mmt-field-cell flex-grow-1 fb-autocomplete">
            <span class="mmt-label">From City</span>
            <input type="text" id="hp-from" name="from_city" class="mmt-input-main" value="Mumbai" placeholder="Your departure city"
                   data-autocomplete="location" data-hidden-target="hp-from-id" autocomplete="off"/>
            <span class="mmt-sub" id="hp-from-sub">Your city</span>
            <input type="hidden" id="hp-from-id" name="from_city_id">
            <div class="fb-autocomplete-menu"></div>
          </div>

          <!-- Destination -->
          <div class="mmt-field-cell flex-grow-2 fb-autocomplete">
            <span class="mmt-label">Destination</span>
            <input type="text" id="hp-dest" name="destination" class="mmt-input-main" value="Bali" placeholder="Where to?"
                   data-autocomplete="location" data-hidden-target="hp-dest-id" autocomplete="off"/>
            <span class="mmt-sub" id="hp-dest-sub">Bali, Indonesia</span>
            <input type="hidden" id="hp-dest-id" name="destination_id">
            <div class="fb-autocomplete-menu"></div>
          </div>

          <!-- Travel Month -->
          <div class="mmt-field-cell date-cell">
            <span class="mmt-label">Travel Month <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="date-display-wrap">
              <span class="mmt-input-val" id="hp-month-val">Select Month</span>
              <span class="mmt-sub" id="hp-month-sub">Pick a month</span>
            </div>
            <input type="month" id="hp-month" name="month" class="mmt-date-hidden"/>
          </div>

          <!-- Travellers -->
          <div class="mmt-field-cell select-cell">
            <span class="mmt-label">Travellers <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="select-display-wrap">
              <span class="mmt-input-val" id="hp-travellers-val">1 Traveller</span>
              <span class="mmt-sub" id="hp-travellers-sub">Group size</span>
            </div>
            <select id="hp-travellers" name="travellers" class="mmt-select-hidden">
              <option value="1" selected>1 Traveller</option>
              <option value="2">2 Travellers</option>
              <option value="3">3 Travellers</option>
              <option value="4">4 Travellers</option>
              <option value="5">5+ Travellers</option>
            </select>
          </div>

          <!-- Budget per Person -->
          <div class="mmt-field-cell select-cell">
            <span class="mmt-label">Budget per Person <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="select-display-wrap">
              <span class="mmt-input-val" id="hp-budget-val">Under ₹25,000</span>
              <span class="mmt-sub" id="hp-budget-sub">Per person budget</span>
            </div>
            <select id="hp-budget" name="budget" class="mmt-select-hidden">
              <option value="under_25k" selected>Under ₹25,000</option>
              <option value="25k_50k">₹25k – ₹50k</option>
              <option value="50k_1l">₹50k – ₹1 Lakh</option>
              <option value="1l_2l">₹1L – ₹2 Lakh</option>
              <option value="2l_plus">₹2 Lakh+</option>
            </select>
          </div>
        </div>

        <!-- Centered floating search button -->
        <div class="btn-search-container">
          <button type="submit" class="btn-search"><i class="bi bi-search"></i> Search</button>
        </div>
        </form>
      </div>

      <!-- ════ ACTIVITIES ════ -->
      <div class="tab-panel {{ $firstEnabledTab === 'activities' ? 'active' : '' }}" id="panel-activities">
        <form method="GET" action="{{ route('activities.search') }}">
        <div class="mmt-search-group">
          <!-- Destination -->
          <div class="mmt-field-cell flex-grow-2 fb-autocomplete">
            <span class="mmt-label">Destination</span>
            <input type="text" id="ac-dest" name="destination" class="mmt-input-main" value="Goa" placeholder="Where do you want to explore?"
                   data-autocomplete="location" data-hidden-target="ac-dest-id" autocomplete="off"/>
            <span class="mmt-sub" id="ac-dest-sub">Goa, India</span>
            <input type="hidden" id="ac-dest-id" name="destination_id">
            <div class="fb-autocomplete-menu"></div>
          </div>

          <!-- Category -->
          <div class="mmt-field-cell select-cell">
            <span class="mmt-label">Category <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="select-display-wrap">
              <span class="mmt-input-val" id="ac-category-val">All Categories</span>
              <span class="mmt-sub" id="ac-category-sub">Type of experience</span>
            </div>
            <select id="ac-category" name="activity_category_id" class="mmt-select-hidden">
              <option value="" selected>All Categories</option>
              @foreach(\App\Models\ActivityCategory::forSite()->where('status', 'active')->orderBy('sort_order')->orderBy('name')->get() as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <!-- Centered floating search button -->
        <div class="btn-search-container">
          <button type="submit" class="btn-search"><i class="bi bi-search"></i> Search</button>
        </div>
        </form>
      </div>

      <!-- ════ TRAINS ════ -->
      <div class="tab-panel {{ $firstEnabledTab === 'trains' ? 'active' : '' }}" id="panel-trains">
        <!-- Trip Type Selection (Top) -->
        <div class="trip-type-container d-flex align-items-center justify-content-between flex-wrap gap-2 px-1 pb-3">
          <div class="trip-type-options">
            <label class="trip-type-label">
              <input type="radio" name="tr-trip" id="tr-trip-oneway" checked/>
              <span class="trip-type-custom"></span> One Way
            </label>
            <label class="trip-type-label">
              <input type="radio" name="tr-trip" id="tr-trip-roundtrip"/>
              <span class="trip-type-custom"></span> Round Trip
            </label>
          </div>
          <span class="text-muted d-none d-sm-inline" style="font-size: 13px; font-weight: 500;">Book Train Tickets</span>
        </div>

        <div class="mmt-search-group">
          <!-- From Station -->
          <div class="mmt-field-cell flex-grow-1">
            <span class="mmt-label">From Station</span>
            <input type="text" id="tr-from" class="mmt-input-main" value="Delhi" placeholder="Station or code"/>
            <span class="mmt-sub" id="tr-from-sub">NDLS, New Delhi Railway Station</span>
          </div>

          <!-- Swap Button -->
          <div class="mmt-swap-cell">
            <button class="mmt-swap-btn" id="swapTrains" title="Swap stations">
              <i class="bi bi-arrow-left-right"></i>
            </button>
          </div>

          <!-- To Station -->
          <div class="mmt-field-cell flex-grow-1">
            <span class="mmt-label">To Station</span>
            <input type="text" id="tr-to" class="mmt-input-main" value="Mumbai" placeholder="Station or code"/>
            <span class="mmt-sub" id="tr-to-sub">BCT, Mumbai Central Railway Station</span>
          </div>

          <!-- Journey Date -->
          <div class="mmt-field-cell date-cell">
            <span class="mmt-label">Journey Date <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="date-display-wrap">
              <span class="mmt-input-val" id="tr-date-val">Select Date</span>
              <span class="mmt-sub" id="tr-date-day">Select Day</span>
            </div>
            <input type="date" id="tr-date" class="mmt-date-hidden"/>
          </div>

          <!-- Class -->
          <div class="mmt-field-cell select-cell">
            <span class="mmt-label">Class <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="select-display-wrap">
              <span class="mmt-input-val" id="tr-class-val">Sleeper (SL)</span>
              <span class="mmt-sub" id="tr-class-sub">Travel class</span>
            </div>
            <select id="tr-class" class="mmt-select-hidden">
              <option value="Sleeper (SL)" selected>Sleeper (SL)</option>
              <option value="AC 3 Tier (3A)">AC 3 Tier (3A)</option>
              <option value="AC 2 Tier (2A)">AC 2 Tier (2A)</option>
              <option value="AC First (1A)">AC First (1A)</option>
              <option value="Chair Car (CC)">Chair Car (CC)</option>
            </select>
          </div>

          <!-- Quota -->
          <div class="mmt-field-cell select-cell">
            <span class="mmt-label">Quota <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="select-display-wrap">
              <span class="mmt-input-val" id="tr-quota-val">General</span>
              <span class="mmt-sub" id="tr-quota-sub">Booking quota</span>
            </div>
            <select id="tr-quota" class="mmt-select-hidden">
              <option value="General" selected>General</option>
              <option value="Tatkal">Tatkal</option>
              <option value="Ladies">Ladies</option>
              <option value="Senior Citizen">Senior Citizen</option>
            </select>
          </div>
        </div>

        <!-- Centered floating search button -->
        <div class="btn-search-container">
          <button class="btn-search"><i class="bi bi-search"></i> Search</button>
        </div>
      </div>

      <!-- ════ BUSES ════ -->
      <div class="tab-panel {{ $firstEnabledTab === 'buses' ? 'active' : '' }}" id="panel-buses">
        <div class="mmt-search-group">
          <!-- From City -->
          <div class="mmt-field-cell flex-grow-1">
            <span class="mmt-label">From City</span>
            <input type="text" id="bs-from" class="mmt-input-main" value="Bangalore" placeholder="Departure city"/>
            <span class="mmt-sub" id="bs-from-sub">Kempegowda Bus Station</span>
          </div>

          <!-- Swap Button -->
          <div class="mmt-swap-cell">
            <button class="mmt-swap-btn" id="swapBuses" title="Swap cities">
              <i class="bi bi-arrow-left-right"></i>
            </button>
          </div>

          <!-- To City -->
          <div class="mmt-field-cell flex-grow-1">
            <span class="mmt-label">To City</span>
            <input type="text" id="bs-to" class="mmt-input-main" value="Chennai" placeholder="Destination city"/>
            <span class="mmt-sub" id="bs-to-sub">Koyambedu Bus Terminus</span>
          </div>

          <!-- Journey Date -->
          <div class="mmt-field-cell date-cell">
            <span class="mmt-label">Journey Date <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="date-display-wrap">
              <span class="mmt-input-val" id="bs-date-val">Select Date</span>
              <span class="mmt-sub" id="bs-date-day">Select Day</span>
            </div>
            <input type="date" id="bs-date" class="mmt-date-hidden"/>
          </div>

          <!-- Bus Type -->
          <div class="mmt-field-cell select-cell">
            <span class="mmt-label">Bus Type <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="select-display-wrap">
              <span class="mmt-input-val" id="bs-type-val">All Bus Types</span>
              <span class="mmt-sub" id="bs-type-sub">Select bus type</span>
            </div>
            <select id="bs-type" class="mmt-select-hidden">
              <option value="All Bus Types" selected>All Bus Types</option>
              <option value="AC Sleeper">AC Sleeper</option>
              <option value="Non-AC Sleeper">Non-AC Sleeper</option>
              <option value="Volvo AC">Volvo AC</option>
              <option value="Seater">Seater</option>
              <option value="AC Seater">AC Seater</option>
            </select>
          </div>
        </div>

        <!-- Centered floating search button -->
        <div class="btn-search-container">
          <button class="btn-search"><i class="bi bi-search"></i> Search</button>
        </div>
      </div>

      <!-- ════ CABS ════ -->
      <div class="tab-panel {{ $firstEnabledTab === 'cabs' ? 'active' : '' }}" id="panel-cabs">
        <!-- Trip Type Selection (Top) -->
        <div class="trip-type-container d-flex align-items-center justify-content-between flex-wrap gap-2 px-1 pb-3">
          <div class="trip-type-options">
            <label class="trip-type-label">
              <input type="radio" name="cb-trip" id="cb-trip-oneway" checked/>
              <span class="trip-type-custom"></span> One Way
            </label>
            <label class="trip-type-label">
              <input type="radio" name="cb-trip" id="cb-trip-roundtrip"/>
              <span class="trip-type-custom"></span> Round Trip
            </label>
            <label class="trip-type-label">
              <input type="radio" name="cb-trip" id="cb-trip-airport"/>
              <span class="trip-type-custom"></span> Airport Transfer
            </label>
            <label class="trip-type-label">
              <input type="radio" name="cb-trip" id="cb-trip-rental"/>
              <span class="trip-type-custom"></span> Hourly Rental
            </label>
          </div>
          <span class="text-muted d-none d-sm-inline" style="font-size: 13px; font-weight: 500;">Book Cab Tickets</span>
        </div>

        <div class="mmt-search-group">
          <!-- Pickup Address -->
          <div class="mmt-field-cell flex-grow-1">
            <span class="mmt-label">Pickup Address</span>
            <input type="text" id="cb-pickup" class="mmt-input-main" value="Delhi" placeholder="Enter Pickup Address"/>
            <span class="mmt-sub" id="cb-pickup-sub">Connaught Place, New Delhi</span>
          </div>

          <!-- Drop Address -->
          <div class="mmt-field-cell flex-grow-1">
            <span class="mmt-label">Drop Address</span>
            <input type="text" id="cb-drop" class="mmt-input-main" value="IGI Airport" placeholder="Enter Drop Address"/>
            <span class="mmt-sub" id="cb-drop-sub">Indira Gandhi International Airport, Delhi</span>
          </div>

          <!-- Pickup Date -->
          <div class="mmt-field-cell date-cell">
            <span class="mmt-label">Pickup Date <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="date-display-wrap">
              <span class="mmt-input-val" id="cb-date-val">Select Date</span>
              <span class="mmt-sub" id="cb-date-day">Select Day</span>
            </div>
            <input type="date" id="cb-date" class="mmt-date-hidden"/>
          </div>

          <!-- Pickup Time -->
          <div class="mmt-field-cell date-cell">
            <span class="mmt-label">Pickup Time <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="date-display-wrap">
              <span class="mmt-input-val" id="cb-time-val">09:00 AM</span>
              <span class="mmt-sub" id="cb-time-sub">Select Time</span>
            </div>
            <input type="time" id="cb-time" class="mmt-date-hidden" value="09:00"/>
          </div>

          <!-- Cab Type -->
          <div class="mmt-field-cell select-cell">
            <span class="mmt-label">Cab Type <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="select-display-wrap">
              <span class="mmt-input-val" id="cb-type-val">Sedan</span>
              <span class="mmt-sub" id="cb-type-sub">Vehicle category</span>
            </div>
            <select id="cb-type" class="mmt-select-hidden">
              <option value="Hatchback">Hatchback</option>
              <option value="Sedan" selected>Sedan</option>
              <option value="SUV">SUV</option>
              <option value="Luxury">Luxury</option>
              <option value="Minivan">Minivan</option>
            </select>
          </div>
        </div>

        <!-- Centered floating search button -->
        <div class="btn-search-container">
          <button class="btn-search"><i class="bi bi-search"></i> Search</button>
        </div>
      </div>

    </div><!-- /.search-card -->
  </div><!-- /.hero-content -->
</section>
