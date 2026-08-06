<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>FareBuzz – Your Journey Starts Here</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="./asset/css/style.css"/>
</head>
<body>

<!-- ══════════ NAVBAR ══════════ -->
<nav class="navbar-top">
  <div class="navbar-inner">
    <a class="nav-logo" href="#">Fare<span>Buzz</span></a>
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

<!-- ══════════ HERO + SEARCH ══════════ -->
<section class="hero-section">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <h1 class="hero-title">Explore Beautiful Destinations at the Best Prices</h1>
    <p class="hero-subtitle">Flights · Hotels · Trains · Buses · Cabs &amp; More — All in One Place</p>
    <div class="search-card">

      <!-- TABS -->
      <div class="search-tabs" id="searchTabs">
        <button class="active" data-tab="flights"><span class="tab-icon"><i class="bi bi-airplane-fill"></i></span> Flights</button>
        <button data-tab="hotels"><span class="tab-icon"><i class="bi bi-building-fill"></i></span> Hotels</button>
        <button data-tab="homestays"><span class="tab-icon"><i class="bi bi-house-heart-fill"></i></span> Homestays</button>
        <button data-tab="holidays"><span class="tab-icon"><i class="bi bi-briefcase-fill"></i></span> Holiday Packages</button>
        <button data-tab="trains"><span class="tab-icon"><i class="bi bi-train-front-fill"></i></span> Trains</button>
        <button data-tab="buses"><span class="tab-icon"><i class="bi bi-bus-front-fill"></i></span> Buses</button>
        <button data-tab="cabs"><span class="tab-icon"><i class="bi bi-taxi-front-fill"></i></span> Cabs</button>
      </div>

      <!-- ════ FLIGHTS ════ -->
      <div class="tab-panel active" id="panel-flights">

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
      <div class="tab-panel" id="panel-hotels">
        <div class="mmt-search-group">
          <!-- City/Area -->
          <div class="mmt-field-cell flex-grow-2">
            <span class="mmt-label">City / Area / Hotel Name</span>
            <input type="text" id="ht-city" class="mmt-input-main" value="Goa" placeholder="Enter City, Area or Hotel"/>
            <span class="mmt-sub" id="ht-city-sub">Goa, India</span>
          </div>

          <!-- Check-in -->
          <div class="mmt-field-cell date-cell">
            <span class="mmt-label">Check-in <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="date-display-wrap">
              <span class="mmt-input-val" id="ht-checkin-val">Select Date</span>
              <span class="mmt-sub" id="ht-checkin-day">Select Day</span>
            </div>
            <input type="date" id="ht-checkin" class="mmt-date-hidden" oninput="calcNights()"/>
          </div>

          <!-- Check-out -->
          <div class="mmt-field-cell date-cell" style="position:relative;">
            <span class="nights-badge" id="nightsBadge"></span>
            <span class="mmt-label">Check-out <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="date-display-wrap">
              <span class="mmt-input-val" id="ht-checkout-val">Select Date</span>
              <span class="mmt-sub" id="ht-checkout-day">Select Day</span>
            </div>
            <input type="date" id="ht-checkout" class="mmt-date-hidden" oninput="calcNights()"/>
          </div>

          <!-- Guests & Rooms -->
          <div class="mmt-field-cell select-cell">
            <span class="mmt-label">Guests &amp; Rooms <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="select-display-wrap">
              <span class="mmt-input-val" id="ht-rooms-val">2 Guests</span>
              <span class="mmt-sub" id="ht-rooms-sub">1 Room</span>
            </div>
            <select id="ht-rooms" class="mmt-select-hidden">
              <option value="2 Guests &middot; 1 Room" selected>2 Guests &middot; 1 Room</option>
              <option value="1 Guest &middot; 1 Room">1 Guest &middot; 1 Room</option>
              <option value="3 Guests &middot; 1 Room">3 Guests &middot; 1 Room</option>
              <option value="4 Guests &middot; 2 Rooms">4 Guests &middot; 2 Rooms</option>
              <option value="6 Guests &middot; 3 Rooms">6 Guests &middot; 3 Rooms</option>
            </select>
          </div>
        </div>

        <!-- Centered floating search button -->
        <div class="btn-search-container">
          <button class="btn-search"><i class="bi bi-search"></i> Search</button>
        </div>
      </div>

      <!-- ════ HOMESTAYS ════ -->
      <div class="tab-panel" id="panel-homestays">
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
      <div class="tab-panel" id="panel-holidays">
        <div class="mmt-search-group">
          <!-- From City -->
          <div class="mmt-field-cell flex-grow-1">
            <span class="mmt-label">From City</span>
            <input type="text" id="hp-from" class="mmt-input-main" value="Mumbai" placeholder="Your departure city"/>
            <span class="mmt-sub" id="hp-from-sub">Your city</span>
          </div>

          <!-- Destination -->
          <div class="mmt-field-cell flex-grow-2">
            <span class="mmt-label">Destination</span>
            <input type="text" id="hp-dest" class="mmt-input-main" value="Bali" placeholder="Where to?"/>
            <span class="mmt-sub" id="hp-dest-sub">Bali, Indonesia</span>
          </div>

          <!-- Travel Month -->
          <div class="mmt-field-cell date-cell">
            <span class="mmt-label">Travel Month <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="date-display-wrap">
              <span class="mmt-input-val" id="hp-month-val">Select Month</span>
              <span class="mmt-sub" id="hp-month-sub">Pick a month</span>
            </div>
            <input type="month" id="hp-month" class="mmt-date-hidden"/>
          </div>

          <!-- Travellers -->
          <div class="mmt-field-cell select-cell">
            <span class="mmt-label">Travellers <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="select-display-wrap">
              <span class="mmt-input-val" id="hp-travellers-val">1 Traveller</span>
              <span class="mmt-sub" id="hp-travellers-sub">Group size</span>
            </div>
            <select id="hp-travellers" class="mmt-select-hidden">
              <option value="1 Traveller" selected>1 Traveller</option>
              <option value="2 Travellers">2 Travellers</option>
              <option value="3 Travellers">3 Travellers</option>
              <option value="4 Travellers">4 Travellers</option>
              <option value="5+ Travellers">5+ Travellers</option>
            </select>
          </div>

          <!-- Budget per Person -->
          <div class="mmt-field-cell select-cell">
            <span class="mmt-label">Budget per Person <i class="bi bi-chevron-down caret-icon"></i></span>
            <div class="select-display-wrap">
              <span class="mmt-input-val" id="hp-budget-val">Under ₹25,000</span>
              <span class="mmt-sub" id="hp-budget-sub">Per person budget</span>
            </div>
            <select id="hp-budget" class="mmt-select-hidden">
              <option value="Under ₹25,000" selected>Under ₹25,000</option>
              <option value="₹25k – ₹50k">₹25k – ₹50k</option>
              <option value="₹50k – ₹1 Lakh">₹50k – ₹1 Lakh</option>
              <option value="₹1L – ₹2 Lakh">₹1L – ₹2 Lakh</option>
              <option value="₹2 Lakh+">₹2 Lakh+</option>
            </select>
          </div>
        </div>

        <!-- Centered floating search button -->
        <div class="btn-search-container">
          <button class="btn-search"><i class="bi bi-search"></i> Search</button>
        </div>
      </div>

      <!-- ════ TRAINS ════ -->
      <!-- ════ TRAINS ════ -->
      <div class="tab-panel" id="panel-trains">
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
      <!-- ════ BUSES ════ -->
      <div class="tab-panel" id="panel-buses">
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
      <!-- ════ CABS ════ -->
      <div class="tab-panel" id="panel-cabs">
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
<!-- ══════════ OFFERS ══════════ -->
<section class="bg-white-section section-pad">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
      <div class="d-flex align-items-center gap-4 flex-wrap">
        <h2 class="section-title mb-0" style="font-size: 24px; font-weight: 800; color: #111;">Offers</h2>
        <div class="offers-tabs">
          <button class="active">All Offers</button>
          <button>Flights</button>
          <button>Hotels</button>
          <button>Holidays</button>
          <button>Trains</button>
          <button>Cabs</button>
        </div>
      </div>
      <div class="d-flex align-items-center gap-3">
        <a href="#" class="view-all-link text-primary fw-bold text-decoration-none" style="font-size: 13px;">VIEW ALL &rarr;</a>
        <div class="carousel-nav" style="display: flex; gap: 8px;">
          <button class="carousel-btn" id="offers-prev" style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid #dde3f0; background: #fff; display: flex; align-items: center; justify-content: center; color: #0084ff;"><i class="bi bi-chevron-left"></i></button>
          <button class="carousel-btn" id="offers-next" style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid #dde3f0; background: #fff; display: flex; align-items: center; justify-content: center; color: #0084ff;"><i class="bi bi-chevron-right"></i></button>
        </div>
      </div>
    </div>
    <div class="offers-grid-scroll" id="offersGrid">
      <!-- Card 1 -->
      <div class="offer-card">
        <div class="offer-img-wrap">
          <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&q=60" alt="Summer offer"/>
        </div>
        <div class="card-body">
          <div class="offer-header-row">
            <span class="offer-tag">INTL FLIGHTS</span>
            <span class="offer-tc">T&amp;C'S APPLY</span>
          </div>
          <div>
            <h6 class="offer-title">For Your Summer Trips: Get Up to 50% OFF*</h6>
            <div class="offer-accent-line"></div>
            <p class="offer-desc">on Flights, Stays, Packages &amp; More.</p>
          </div>
          <div class="offer-footer-row">
            <a href="#" class="btn-offer">BOOK NOW</a>
          </div>
        </div>
      </div>

      <!-- Card 2 (In mockup, Row 2 Col 1: FOR THE PERFECT STAYS IN THE HILLS) -->
      <div class="offer-card">
        <div class="offer-img-wrap">
          <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=400&q=60" alt="Hills stays"/>
        </div>
        <div class="card-body">
          <div class="offer-header-row">
            <span class="offer-tag">DOM HOTELS</span>
            <span class="offer-tc">T&amp;C'S APPLY</span>
          </div>
          <div>
            <h6 class="offer-title">FOR THE PERFECT STAYS IN THE HILLS:</h6>
            <div class="offer-accent-line"></div>
            <p class="offer-desc">Book DLS Hotels @ Up to 40% OFF*</p>
          </div>
          <div class="offer-footer-row">
            <a href="#" class="btn-offer">BOOK NOW</a>
          </div>
        </div>
      </div>

      <!-- Card 3 (In mockup, Row 1 Col 2: Grab Up to 40% OFF*) -->
      <div class="offer-card">
        <div class="offer-img-wrap">
          <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=400&q=60" alt="Stays offer"/>
        </div>
        <div class="card-body">
          <div class="offer-header-row">
            <span class="offer-tag">DOM HOTELS</span>
            <span class="offer-tc">T&amp;C'S APPLY</span>
          </div>
          <div>
            <h6 class="offer-title">Grab Up to 40% OFF*</h6>
            <p class="offer-desc">on Fateh Collection Hotels &amp; Resorts and enjoy heritage stays!</p>
          </div>
          <div class="offer-footer-row">
            <a href="#" class="btn-offer">VIEW DETAILS</a>
          </div>
        </div>
      </div>

      <!-- Card 4 (In mockup, Row 2 Col 2: LIVE NOW: Great Connections Fest by IndiGo) -->
      <div class="offer-card">
        <div class="offer-img-wrap">
          <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=400&q=60" alt="IndiGo flights"/>
        </div>
        <div class="card-body">
          <div class="offer-header-row">
            <span class="offer-tag">DOM FLIGHTS</span>
            <span class="offer-tc">T&amp;C'S APPLY</span>
          </div>
          <div>
            <h6 class="offer-title">LIVE NOW: Great Connections Fest by IndiGo</h6>
            <div class="offer-accent-line"></div>
            <p class="offer-desc">With Connecting Flights Starting @ ₹3,999* &amp; More!</p>
          </div>
          <div class="offer-footer-row">
            <a href="#" class="btn-offer">BOOK NOW</a>
          </div>
        </div>
      </div>

      <!-- Card 5 (In mockup, Row 1 Col 3: Summit Hotels) -->
      <div class="offer-card">
        <div class="offer-img-wrap">
          <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=400&q=60" alt="Summit Hotels"/>
        </div>
        <div class="card-body">
          <div class="offer-header-row">
            <span class="offer-tag">DOM HOTELS</span>
            <span class="offer-tc">T&amp;C'S APPLY</span>
          </div>
          <div>
            <h6 class="offer-title">ENJOY SUMMIT HOTELS:</h6>
            <div class="offer-accent-line"></div>
            <p class="offer-desc">Book Summit Hotels &amp; Resorts @ Up to 30% OFF*</p>
          </div>
          <div class="offer-footer-row">
            <a href="#" class="btn-offer">VIEW DETAILS</a>
          </div>
        </div>
      </div>

      <!-- Card 6 (In mockup, Row 2 Col 3: OYO Rooms) -->
      <div class="offer-card">
        <div class="offer-img-wrap">
          <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&q=60" alt="OYO Rooms"/>
        </div>
        <div class="card-body">
          <div class="offer-header-row">
            <span class="offer-tag">DOM HOTELS</span>
            <span class="offer-tc">T&amp;C'S APPLY</span>
          </div>
          <div>
            <h6 class="offer-title">Today's Deals on OYO Rooms</h6>
            <div class="offer-accent-line"></div>
            <p class="offer-desc">Up to 40% OFF on select OYO-Serviced Hotels.</p>
          </div>
          <div class="offer-footer-row">
            <a href="#" class="btn-offer">BOOK NOW</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════ FOR TRAVEL PROS ══════════ -->
<section class="pros-section section-pad">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h2 class="section-title mb-0">For travel pros</h2>
      <div class="carousel-nav">
        <button class="carousel-btn"><i class="bi bi-chevron-left"></i></button>
        <button class="carousel-btn"><i class="bi bi-chevron-right"></i></button>
      </div>
    </div>
    <div class="row g-3">
      <div class="col-6 col-md-3">
        <div class="pro-card">
          <div class="pro-card-header">
            <h6>Plan with AI</h6>
            <p>Get travel questions answered</p>
          </div>
          <img src="img/pro_ai.png" alt="Plan with AI"/>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="pro-card">
          <div class="pro-card-header">
            <h6>Best Time to Travel</h6>
            <p>Know when to save</p>
          </div>
          <img src="img/pro_time.png" alt="Best Time to Travel"/>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="pro-card">
          <div class="pro-card-header">
            <h6>Explore</h6>
            <p>See destinations on your budget</p>
          </div>
          <img src="img/pro_explore.png" alt="Explore"/>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="pro-card">
          <div class="pro-card-header">
            <h6>Trips</h6>
            <p>Keep all your plans in one place</p>
          </div>
          <img src="img/pro_trips.png" alt="Trips"/>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════ FLAGSHIP HOTELS ══════════ -->
<section class="flagship-section section-pad">
  <div class="container">
    <div class="flagship-inner">
      <div class="flagship-title-block">
        <h3>Flagship Hotel<br/>Stores on<br/><span class="logo-fare">Fare</span><span class="logo-buzz">Buzz</span></h3>
      </div>
      <div class="flagship-cards-grid">
        <!-- Card 1 -->
        <div class="hotel-store-card">
          <div class="logo-overlay">
            <img src="img/itc_logo.png" alt="ITC Hotels Logo"/>
          </div>
          <img src="img/itc_bg.png" alt="ITC Hotels" class="card-bg-img"/>
          <div class="gradient-overlay"></div>
          <div class="card-name-overlay">
            <h6>ITC Hotels Limited</h6>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="hotel-store-card">
          <div class="logo-overlay">
            <img src="img/sterling_logo.png" alt="Sterling Hotels Logo"/>
          </div>
          <img src="img/sterling_bg.png" alt="Sterling Hotels &amp; Resorts" class="card-bg-img"/>
          <div class="gradient-overlay"></div>
          <div class="card-name-overlay">
            <h6>Sterling Hotels &amp; Resorts</h6>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="hotel-store-card">
          <div class="logo-overlay">
            <img src="img/cgh_logo.png" alt="CGH Earth Logo"/>
          </div>
          <img src="img/cgh_bg.png" alt="CGH Earth Experience Hotels" class="card-bg-img"/>
          <div class="gradient-overlay"></div>
          <div class="card-name-overlay">
            <h6>CGH Earth Experience Hotels</h6>
          </div>
        </div>

        <!-- Card 4 -->
        <div class="hotel-store-card">
          <div class="logo-overlay">
            <img src="img/royal_logo.png" alt="Royal Orchid Logo"/>
          </div>
          <img src="img/royal_bg.png" alt="Royal Orchid Hotels" class="card-bg-img"/>
          <div class="gradient-overlay"></div>
          <div class="card-name-overlay">
            <h6>Royal Orchid Hotels</h6>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════ EXPLORE POPULAR DESTINATIONS ══════════ -->
<section class="bg-white-section section-pad">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
      <h2 class="section-title mb-0">Explore stays in popular destinations</h2>
    </div>
    <div class="dest-tabs mb-3">
      <button class="active">Beach</button>
      <button>Culture</button>
      <button>Ski</button>
      <button>Family</button>
      <button>Wellness and Relaxation</button>
    </div>
    <div class="dest-scroll-wrapper">
      <!-- Scroll buttons -->
      <button class="dest-scroll-btn dest-prev" id="dest-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="dest-scroll-btn dest-next" id="dest-next"><i class="bi bi-chevron-right"></i></button>

      <div class="dest-grid-scroll" id="destGrid">
        <!-- Busan -->
        <div class="dest-card">
          <img src="img/dest_busan.png" alt="Busan"/>
          <div class="dest-card-body">
            <h6>Busan</h6>
            <p>South Korea</p>
          </div>
        </div>

        <!-- Pattaya -->
        <div class="dest-card">
          <img src="img/dest_pattaya.png" alt="Pattaya"/>
          <div class="dest-card-body">
            <h6>Pattaya</h6>
            <p>Chonburi Province, Thailand</p>
          </div>
        </div>

        <!-- Da Nang -->
        <div class="dest-card">
          <img src="img/dest_danang.png" alt="Da Nang"/>
          <div class="dest-card-body">
            <h6>Da Nang</h6>
            <p>Da Nang Municipality, Vietnam</p>
          </div>
        </div>

        <!-- Puri -->
        <div class="dest-card">
          <img src="img/dest_puri.png" alt="Puri"/>
          <div class="dest-card-body">
            <h6>Puri</h6>
            <p>Odisha, India</p>
          </div>
        </div>

        <!-- Ubud -->
        <div class="dest-card">
          <img src="img/dest_ubud.png" alt="Ubud"/>
          <div class="dest-card-body">
            <h6>Ubud</h6>
            <p>Bali, Indonesia</p>
          </div>
        </div>

        <!-- Goa -->
        <div class="dest-card">
          <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&q=60" alt="Goa"/>
          <div class="dest-card-body">
            <h6>Goa</h6>
            <p>India</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════ HOMES GUESTS LOVE ══════════ -->
<section class="section-pad" style="background:#f8f8f8;">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h2 class="section-title mb-0">Homes guests love</h2>
    </div>

    <div class="homes-scroll-wrapper">
      <!-- Scroll buttons -->
      <button class="homes-scroll-btn homes-prev" id="homes-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="homes-scroll-btn homes-next" id="homes-next"><i class="bi bi-chevron-right"></i></button>

      <div class="homes-grid-scroll" id="homesGrid">
        <!-- Card 1 -->
        <div class="acc-card">
          <div class="acc-img-wrap">
            <img src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=400&q=60" alt="Hotel Comfort Stay"/>
            <button class="wishlist-btn"><i class="bi bi-heart"></i></button>
          </div>
          <div class="acc-card-body">
            <div class="acc-badge-row">
              <span class="acc-type">Guest accommodation</span>
              <span class="acc-stars">
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
              </span>
              <span class="genius-badge">Genius</span>
            </div>
            <h6 class="acc-title">Hotel Comfort Stay With RR Group - Main Bazar New Delhi</h6>
            <div class="acc-location">New Delhi, India</div>
            <div class="acc-rating-row">
              <div class="acc-rating-badge">9.0</div>
              <div class="acc-rating-text">
                <span class="acc-rating-label">Superb</span>
                <span class="acc-rating-count">9 reviews</span>
              </div>
            </div>
            <div class="acc-distance">
              <i class="bi bi-geo-alt-fill"></i>
              <span>1 km from centre</span>
            </div>
            <div class="acc-price-row">
              <span class="acc-price-label">Starting from</span>
              <span class="acc-price-old">₹2,069</span>
              <span class="acc-price-new">₹1,035</span>
            </div>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="acc-card">
          <div class="acc-img-wrap">
            <img src="https://images.unsplash.com/photo-1582719508461-905c673771fd?w=400&q=60" alt="Newly Built Guest House"/>
            <button class="wishlist-btn"><i class="bi bi-heart"></i></button>
          </div>
          <div class="acc-card-body">
            <div class="acc-badge-row">
              <span class="acc-type">Guest accommodation</span>
              <span class="acc-stars">
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
              </span>
            </div>
            <h6 class="acc-title">Newly Built Guest House Sapphire</h6>
            <div class="acc-location">New Delhi, India</div>
            <div class="acc-rating-row">
              <div class="acc-rating-badge">8.1</div>
              <div class="acc-rating-text">
                <span class="acc-rating-label">Very good</span>
                <span class="acc-rating-count">8 reviews</span>
              </div>
            </div>
            <div class="acc-distance">
              <i class="bi bi-geo-alt-fill"></i>
              <span>1 km from centre</span>
            </div>
            <div class="acc-price-row">
              <span class="acc-price-label">Starting from</span>
              <span class="acc-price-old">₹1,121</span>
              <span class="acc-price-new">₹896</span>
            </div>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="acc-card">
          <div class="acc-img-wrap">
            <img src="https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=400&q=60" alt="Orania B & B"/>
            <button class="wishlist-btn"><i class="bi bi-heart"></i></button>
          </div>
          <div class="acc-card-body">
            <div class="acc-badge-row">
              <span class="acc-type">Bed and Breakfast</span>
              <span class="acc-stars">
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
              </span>
              <span class="genius-badge">Genius</span>
            </div>
            <h6 class="acc-title">Orania B &amp; B by Atsar</h6>
            <div class="acc-location">New Delhi, India</div>
            <div class="acc-rating-row">
              <div class="acc-rating-badge">8.3</div>
              <div class="acc-rating-text">
                <span class="acc-rating-label">Very good</span>
                <span class="acc-rating-count">189 reviews</span>
              </div>
            </div>
            <div class="acc-distance">
              <i class="bi bi-geo-alt-fill"></i>
              <span>9.3 km from centre</span>
            </div>
            <div class="acc-price-row">
              <span class="acc-price-label">Starting from</span>
              <span class="acc-price-new">₹3,585</span>
            </div>
          </div>
        </div>

        <!-- Card 4 -->
        <div class="acc-card">
          <div class="acc-img-wrap">
            <img src="https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=400&q=60" alt="Avatar Living"/>
            <button class="wishlist-btn"><i class="bi bi-heart"></i></button>
          </div>
          <div class="acc-card-body">
            <div class="acc-badge-row">
              <span class="acc-type">Bed and Breakfast</span>
              <span class="acc-stars">
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
              </span>
              <span class="genius-badge">Genius</span>
            </div>
            <h6 class="acc-title">Avatar Living @Safdarjung Enclave</h6>
            <div class="acc-location">New Delhi, India</div>
            <div class="acc-rating-row">
              <div class="acc-rating-badge">8.5</div>
              <div class="acc-rating-text">
                <span class="acc-rating-label">Very good</span>
                <span class="acc-rating-count">697 reviews</span>
              </div>
            </div>
            <div class="acc-distance">
              <i class="bi bi-geo-alt-fill"></i>
              <span>8.4 km from centre</span>
            </div>
            <div class="acc-price-row">
              <span class="acc-price-label">Starting from</span>
              <span class="acc-price-old">₹3,636</span>
              <span class="acc-price-new">₹2,981</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════ TOP UNIQUE PROPERTIES ══════════ -->
<section class="bg-white-section section-pad">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-1">
      <h2 class="section-title mb-0">Stay at our top unique properties</h2>
    </div>
    <p class="text-muted mb-3" style="font-size:13px;">From castles and villas to boats and igloos, we've got it all</p>

    <div class="unique-scroll-wrapper">
      <!-- Scroll buttons -->
      <button class="unique-scroll-btn unique-prev" id="unique-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="unique-scroll-btn unique-next" id="unique-next"><i class="bi bi-chevron-right"></i></button>

      <div class="unique-grid-scroll" id="uniqueGrid">
        <!-- Card 1 -->
        <div class="acc-card">
          <div class="acc-img-wrap">
            <img src="https://images.unsplash.com/photo-1583037189850-1921ae7c6c22?w=400&q=60" alt="Taj Fisherman's Cove"/>
            <button class="wishlist-btn"><i class="bi bi-heart"></i></button>
          </div>
          <div class="acc-card-body">
            <div class="acc-badge-row">
              <span class="acc-type">Hotel</span>
              <span class="acc-stars">
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
              </span>
            </div>
            <h6 class="acc-title">Taj Fisherman's Cove Resort &amp; Spa, Chennai</h6>
            <div class="acc-location">Mahabalipuram, India</div>
            <div class="acc-rating-row">
              <div class="acc-rating-badge">8.2</div>
              <div class="acc-rating-text">
                <span class="acc-rating-label">Very good</span>
                <span class="acc-rating-count">704 reviews</span>
              </div>
            </div>
            <div class="acc-price-row">
              <span class="acc-price-label">Starting from</span>
              <span class="acc-price-new">₹12,500</span>
            </div>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="acc-card">
          <div class="acc-img-wrap">
            <img src="https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=400&q=60" alt="Radisson Blu Resort"/>
            <button class="wishlist-btn"><i class="bi bi-heart"></i></button>
          </div>
          <div class="acc-card-body">
            <div class="acc-badge-row">
              <span class="acc-type">Resort</span>
              <span class="acc-stars">
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
              </span>
            </div>
            <h6 class="acc-title">Radisson Blu Resort Temple Bay Mamallapuram</h6>
            <div class="acc-location">Mahabalipuram, India</div>
            <div class="acc-rating-row">
              <div class="acc-rating-badge">8.5</div>
              <div class="acc-rating-text">
                <span class="acc-rating-label">Very good</span>
                <span class="acc-rating-count">852 reviews</span>
              </div>
            </div>
            <div class="acc-price-row">
              <span class="acc-price-label">Starting from</span>
              <span class="acc-price-new">₹14,500</span>
            </div>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="acc-card">
          <div class="acc-img-wrap">
            <img src="https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=400&q=60" alt="Grand Hyatt Goa"/>
            <button class="wishlist-btn"><i class="bi bi-heart"></i></button>
          </div>
          <div class="acc-card-body">
            <div class="acc-badge-row">
              <span class="acc-type">Hotel</span>
              <span class="acc-stars">
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
              </span>
            </div>
            <h6 class="acc-title">Grand Hyatt Goa</h6>
            <div class="acc-location">Panaji, India</div>
            <div class="acc-rating-row">
              <div class="acc-rating-badge">8.8</div>
              <div class="acc-rating-text">
                <span class="acc-rating-label">Fabulous</span>
                <span class="acc-rating-count">809 reviews</span>
              </div>
            </div>
            <div class="acc-price-row">
              <span class="acc-price-label">Starting from</span>
              <span class="acc-price-new">₹13,620</span>
            </div>
          </div>
        </div>

        <!-- Card 4 -->
        <div class="acc-card">
          <div class="acc-img-wrap">
            <img src="https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=400&q=60" alt="Heritage Madurai"/>
            <button class="wishlist-btn"><i class="bi bi-heart"></i></button>
          </div>
          <div class="acc-card-body">
            <div class="acc-badge-row">
              <span class="acc-type">Resort</span>
              <span class="acc-stars">
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
                <span class="badge-star-icon"><i class="bi bi-star-fill"></i></span>
              </span>
            </div>
            <h6 class="acc-title">Heritage Madurai</h6>
            <div class="acc-location">Madurai, India</div>
            <div class="acc-rating-row">
              <div class="acc-rating-badge">8.9</div>
              <div class="acc-rating-text">
                <span class="acc-rating-label">Fabulous</span>
                <span class="acc-rating-count">1,220 reviews</span>
              </div>
            </div>
            <div class="acc-price-row">
              <span class="acc-price-label">Starting from</span>
              <span class="acc-price-old">₹7,839</span>
              <span class="acc-price-new">₹7,055</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════ TRENDING DESTINATIONS ══════════ -->
<section class="section-pad" style="background:#f5f5f5;">
  <div class="container">
    <h2 class="section-title mb-3">Trending destinations</h2>
    <!-- Row 1: 2 Columns -->
    <div class="row g-3 mb-3">
      <div class="col-12 col-md-6">
        <div class="trending-card">
          <img src="https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800&q=70" alt="Singapore"/>
          <h5 class="trending-title">Singapore <img src="https://flagcdn.com/w40/sg.png" class="flag-icon" alt="Singapore"/></h5>
        </div>
      </div>
      <div class="col-12 col-md-6">
        <div class="trending-card">
          <img src="https://images.unsplash.com/photo-1582510003544-4d00b7f74220?w=800&q=70" alt="Chennai"/>
          <h5 class="trending-title">Chennai <img src="https://flagcdn.com/w40/in.png" class="flag-icon" alt="Chennai"/></h5>
        </div>
      </div>
    </div>
    <!-- Row 2: 3 Columns -->
    <div class="row g-3">
      <div class="col-12 col-md-4">
        <div class="trending-card small">
          <img src="https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=600&q=70" alt="Bengaluru"/>
          <h5 class="trending-title">Bengaluru <img src="https://flagcdn.com/w40/in.png" class="flag-icon" alt="Bengaluru"/></h5>
        </div>
      </div>
      <div class="col-12 col-md-4">
        <div class="trending-card small">
          <img src="https://images.unsplash.com/photo-1529655683826-aba9b3e77383?w=600&q=70" alt="London"/>
          <h5 class="trending-title">London <img src="https://flagcdn.com/w40/gb.png" class="flag-icon" alt="London"/></h5>
        </div>
      </div>
      <div class="col-12 col-md-4">
        <div class="trending-card small">
          <img src="https://images.unsplash.com/photo-1528127269322-539801943592?w=600&q=70" alt="Bangkok"/>
          <h5 class="trending-title">Bangkok <img src="https://flagcdn.com/w40/th.png" class="flag-icon" alt="Bangkok"/></h5>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════ DEALS FOR THE WEEKEND ══════════ -->
<section class="bg-white-section section-pad">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-1">
      <h2 class="section-title mb-0">Deals for the weekend</h2>
    </div>
    <p class="text-muted mb-3" style="font-size:13px;">Save on stays for 22 May - 24 May</p>

    <div class="deals-scroll-wrapper">
      <!-- Scroll buttons -->
      <button class="deals-scroll-btn deals-prev" id="deals-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="deals-scroll-btn deals-next" id="deals-next"><i class="bi bi-chevron-right"></i></button>

      <div class="deals-grid-scroll" id="dealsGrid">
        <!-- Card 1 -->
        <div class="acc-card">
          <div class="acc-img-wrap">
            <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=400&q=60" alt="The Radiant Near Delhi"/>
            <button class="wishlist-btn"><i class="bi bi-heart"></i></button>
          </div>
          <div class="acc-card-body">
            <div class="acc-badge-row">
              <span class="genius-badge">Genius</span>
            </div>
            <h6 class="acc-title">The Radiant Near Delhi International Airport</h6>
            <div class="acc-location">New Delhi, India</div>
            <div class="acc-rating-row">
              <div class="acc-rating-badge">9.2</div>
              <div class="acc-rating-text">
                <span class="acc-rating-label">Superb</span>
                <span class="acc-rating-count">54 reviews</span>
              </div>
            </div>
            <div class="acc-price-row">
              <span class="acc-price-label">2 nights</span>
              <span class="acc-price-old">₹15,765</span>
              <span class="acc-price-new">₹3,941</span>
            </div>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="acc-card">
          <div class="acc-img-wrap">
            <img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=400&q=60" alt="Umaid Bhawan"/>
            <button class="wishlist-btn"><i class="bi bi-heart"></i></button>
          </div>
          <div class="acc-card-body">
            <div class="acc-badge-row">
              <span class="genius-badge">Genius</span>
            </div>
            <h6 class="acc-title">Umaid Bhawan - A Heritage Style Boutique Hotel</h6>
            <div class="acc-location">Jaipur, India</div>
            <div class="acc-rating-row">
              <div class="acc-rating-badge">8.8</div>
              <div class="acc-rating-text">
                <span class="acc-rating-label">Fabulous</span>
                <span class="acc-rating-count">1,900 reviews</span>
              </div>
            </div>
            <div class="acc-price-row">
              <span class="acc-price-label">2 nights</span>
              <span class="acc-price-old">₹16,999</span>
              <span class="acc-price-new">₹12,579</span>
            </div>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="acc-card">
          <div class="acc-img-wrap">
            <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&q=60" alt="Limewood Stay"/>
            <button class="wishlist-btn"><i class="bi bi-heart"></i></button>
          </div>
          <div class="acc-card-body">
            <div class="acc-badge-row">
              <span class="genius-badge">Genius</span>
            </div>
            <h6 class="acc-title">Limewood Stay - Executive Huda City Center</h6>
            <div class="acc-location">Gurgaon, India</div>
            <div class="acc-rating-row">
              <div class="acc-rating-badge">8.5</div>
              <div class="acc-rating-text">
                <span class="acc-rating-label">Very good</span>
                <span class="acc-rating-count">785 reviews</span>
              </div>
            </div>
            <span class="acc-deal-badge">Getaway Deal</span>
            <div class="acc-price-row">
              <span class="acc-price-label">2 nights</span>
              <span class="acc-price-old">₹10,400</span>
              <span class="acc-price-new">₹3,952</span>
            </div>
          </div>
        </div>

        <!-- Card 4 -->
        <div class="acc-card">
          <div class="acc-img-wrap">
            <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=400&q=60" alt="Hotel Lime Pride"/>
            <button class="wishlist-btn"><i class="bi bi-heart"></i></button>
          </div>
          <div class="acc-card-body">
            <div class="acc-badge-row">
              <span class="genius-badge">Genius</span>
            </div>
            <h6 class="acc-title">Hotel Lime Pride By GK Group Near IGI Delhi Airport</h6>
            <div class="acc-location">New Delhi, India</div>
            <div class="acc-rating-row">
              <div class="acc-rating-badge">8.7</div>
              <div class="acc-rating-text">
                <span class="acc-rating-label">Fabulous</span>
                <span class="acc-rating-count">196 reviews</span>
              </div>
            </div>
            <span class="acc-deal-badge">Limited-time Deal</span>
            <div class="acc-price-row">
              <span class="acc-price-label">2 nights</span>
              <span class="acc-price-old">₹11,190</span>
              <span class="acc-price-new">₹2,352</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════ QUICK & EASY TRIP PLANNER ══════════ -->
<section class="planner-section section-pad" style="background:#f8f8f8;">
  <div class="container">
    <h2 class="section-title mb-1">Quick and easy trip planner</h2>
    <p class="text-muted mb-3" style="font-size:13px;">Pick a vibe and explore the top destinations in India</p>

    <div class="planner-tabs">
      <button class="active">Historical Tours</button>
      <button>Adventure &amp; Exploration</button>
      <button>Historical Expeditions</button>
      <button>Wildlife &amp; Nature</button>
      <button>Shopping</button>
      <button>Beach Trips</button>
      <button>More <i class="bi bi-chevron-down" style="font-size: 11px;"></i></button>
    </div>

    <div class="planner-scroll-wrapper">
      <!-- Scroll buttons -->
      <button class="planner-scroll-btn planner-prev" id="planner-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="planner-scroll-btn planner-next" id="planner-next"><i class="bi bi-chevron-right"></i></button>

      <div class="planner-grid-scroll" id="plannerGrid">
        <!-- Card 1 -->
        <div class="city-card">
          <img src="https://images.unsplash.com/photo-1587474260584-136574528ed5?w=300&q=60" alt="New Delhi"/>
          <h6>New Delhi</h6>
          <small>2.6 km away</small>
        </div>

        <!-- Card 2 -->
        <div class="city-card">
          <img src="https://images.unsplash.com/photo-1564507592333-c60657eea523?w=300&q=60" alt="Agra"/>
          <h6>Agra</h6>
          <small>181 km away</small>
        </div>

        <!-- Card 3 -->
        <div class="city-card">
          <img src="https://images.unsplash.com/photo-1477587458883-47145ed31b20?w=300&q=60" alt="Jaipur"/>
          <h6>Jaipur</h6>
          <small>238 km away</small>
        </div>

        <!-- Card 4 -->
        <div class="city-card">
          <img src="https://images.unsplash.com/photo-1609920658906-8223bd289001?w=300&q=60" alt="Lucknow"/>
          <h6>Lucknow</h6>
          <small>416 km away</small>
        </div>

        <!-- Card 5 -->
        <div class="city-card">
          <img src="https://images.unsplash.com/photo-1601921004897-b7d582836990?w=300&q=60" alt="Bhopal"/>
          <h6>Bhopal</h6>
          <small>599 km away</small>
        </div>

        <!-- Card 6 -->
        <div class="city-card">
          <img src="https://images.unsplash.com/photo-1561361058-c24cecae35ca?w=300&q=60" alt="Varanasi"/>
          <h6>Varanasi</h6>
          <small>680 km away</small>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════ EXPLORE INDIA ══════════ -->
<section class="bg-white-section section-pad">
  <div class="container">
    <h2 class="section-title mb-1">Explore India</h2>
    <p class="text-muted mb-3" style="font-size:13px;">These popular destinations have a lot to offer</p>

    <div class="explore-scroll-wrapper">
      <!-- Scroll buttons -->
      <button class="explore-scroll-btn explore-prev" id="explore-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="explore-scroll-btn explore-next" id="explore-next"><i class="bi bi-chevron-right"></i></button>

      <div class="explore-grid-scroll" id="exploreGrid">
        <!-- Card 1 -->
        <div class="explore-card">
          <img src="https://images.unsplash.com/photo-1582510003544-4d00b7f74220?w=300&q=60" alt="Chennai"/>
          <div class="card-body">
            <h6>Chennai</h6>
            <small>1,404 properties</small>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="explore-card">
          <img src="https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=300&q=60" alt="Bengaluru"/>
          <div class="card-body">
            <h6>Bengaluru</h6>
            <small>3,344 properties</small>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="explore-card">
          <img src="https://images.unsplash.com/photo-1600093463592-8e36ae95ef56?w=300&q=60" alt="Hyderabad"/>
          <div class="card-body">
            <h6>Hyderabad</h6>
            <small>2,027 properties</small>
          </div>
        </div>

        <!-- Card 4 -->
        <div class="explore-card">
          <img src="https://images.unsplash.com/photo-1558431382-27e303142255?w=300&q=60" alt="Kolkata"/>
          <div class="card-body">
            <h6>Kolkata</h6>
            <small>920 properties</small>
          </div>
        </div>

        <!-- Card 5 -->
        <div class="explore-card">
          <img src="https://images.unsplash.com/photo-1597010373246-4f2adaca4541?w=300&q=60" alt="Ayodhya"/>
          <div class="card-body">
            <h6>Ayodhya</h6>
            <small>977 properties</small>
          </div>
        </div>

        <!-- Card 6 -->
        <div class="explore-card">
          <img src="https://images.unsplash.com/photo-1529253355930-ddbe423a2ac7?w=300&q=60" alt="Mumbai"/>
          <div class="card-body">
            <h6>Mumbai</h6>
            <small>1,813 properties</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════ BROWSE BY PROPERTY TYPE ══════════ -->
<section class="section-pad" style="background:#f5f5f5;">
  <div class="container">
    <h2 class="section-title mb-3">Browse by property type</h2>

    <div class="browse-scroll-wrapper">
      <!-- Scroll buttons -->
      <button class="browse-scroll-btn browse-prev" id="browse-prev" style="display: none;"><i class="bi bi-chevron-left"></i></button>
      <button class="browse-scroll-btn browse-next" id="browse-next"><i class="bi bi-chevron-right"></i></button>

      <div class="browse-grid-scroll" id="browseGrid">
        <!-- Card 1 -->
        <div class="property-card">
          <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=400&q=60" alt="Hotels"/>
          <div class="property-card-label">Hotels</div>
        </div>

        <!-- Card 2 -->
        <div class="property-card">
          <img src="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=400&q=60" alt="Apartments"/>
          <div class="property-card-label">Apartments</div>
        </div>

        <!-- Card 3 -->
        <div class="property-card">
          <img src="https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=400&q=60" alt="Resorts"/>
          <div class="property-card-label">Resorts</div>
        </div>

        <!-- Card 4 -->
        <div class="property-card">
          <img src="https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=400&q=60" alt="Villas"/>
          <div class="property-card-label">Villas</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════ RADIANT SRI LANKA ══════════ -->
<section class="section-pad" style="background:#f5f5f5;">
  <div class="container">
    <div class="srilanka-box">
      <div class="mb-4">
        <h2 class="section-title mb-1" style="font-size: 24px; font-weight: 800; margin-top: 0;">Radiant Sri Lanka</h2>
        <p class="text-muted mb-0" style="font-size: 14px;">Ancient Wonders &amp; Tea Hills</p>
      </div>
      <div class="row g-3">
        <!-- Card 1 -->
        <div class="col-6 col-md-4">
          <div class="srilanka-dest-card overlay-style">
            <img src="https://images.unsplash.com/photo-1588598126483-2476d5318f75?w=600&q=70" alt="Sigiriya"/>
            <div class="srilanka-dest-body">
              <h6>Sigiriya Rock View</h6>
              <p>The best seat in Sri Lanka</p>
            </div>
          </div>
        </div>
        <!-- Card 2 -->
        <div class="col-6 col-md-4">
          <div class="srilanka-dest-card">
            <img src="https://images.unsplash.com/photo-1542856391-010fb87dcfed?w=600&q=70" alt="Mihintale"/>
            <div class="srilanka-dest-body">
              <h6>Mihintale</h6>
              <p>Panoramic views from a historic stupa</p>
            </div>
          </div>
        </div>
        <!-- Card 3 -->
        <div class="col-12 col-md-4">
          <div class="srilanka-dest-card">
            <img src="https://images.unsplash.com/photo-1543731068-7e0f5beff43a?w=600&q=70" alt="Ceylon"/>
            <div class="srilanka-dest-body">
              <h6>Ceylon's Emerald Hills</h6>
              <p>Wander through the misty tea trails of scenic Ella</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════ FOOTER ══════════ -->
<footer class="site-footer py-5">
  <div class="container">
    <div class="row g-4 mb-4">
      <div class="col-12 col-md-3">
        <div class="footer-logo mb-2">Fare<span>Buzz</span></div>
        <p style="font-size:12px;color:#aaa;line-height:1.6;">Your trusted travel companion for flights, hotels, holidays, trains and more. Best prices guaranteed.</p>
        <div class="d-flex gap-3 mt-3">
          <a href="#" style="color:#aaa;font-size:18px;"><i class="bi bi-facebook"></i></a>
          <a href="#" style="color:#aaa;font-size:18px;"><i class="bi bi-twitter-x"></i></a>
          <a href="#" style="color:#aaa;font-size:18px;"><i class="bi bi-instagram"></i></a>
          <a href="#" style="color:#aaa;font-size:18px;"><i class="bi bi-youtube"></i></a>
        </div>
      </div>
      <div class="col-6 col-md-2 footer-links">
        <h6>Company</h6>
        <ul>
          <li><a href="#">About Us</a></li>
          <li><a href="#">Careers</a></li>
          <li><a href="#">News &amp; Blog</a></li>
          <li><a href="#">Investor Relations</a></li>
          <li><a href="#">Partner with us</a></li>
        </ul>
      </div>
      <div class="col-6 col-md-2 footer-links">
        <h6>Products</h6>
        <ul>
          <li><a href="#">Flights</a></li>
          <li><a href="#">Hotels</a></li>
          <li><a href="#">Holiday Packages</a></li>
          <li><a href="#">Trains</a></li>
          <li><a href="#">Buses &amp; Cabs</a></li>
        </ul>
      </div>
      <div class="col-6 col-md-2 footer-links">
        <h6>Support</h6>
        <ul>
          <li><a href="#">Help Center</a></li>
          <li><a href="#">My Trips</a></li>
          <li><a href="#">Cancellation Policy</a></li>
          <li><a href="#">Travel Insurance</a></li>
          <li><a href="#">Contact Us</a></li>
        </ul>
      </div>
      <div class="col-6 col-md-3 footer-links">
        <h6>Download App</h6>
        <p style="font-size:12px;color:#aaa;margin-bottom:10px;">Get the best deals on the go</p>
        <a href="#" class="d-flex align-items-center gap-2 bg-dark text-white rounded p-2 mb-2 text-decoration-none" style="font-size:12px;max-width:160px;">
          <i class="bi bi-apple" style="font-size:20px;"></i>
          <div><div style="font-size:9px;opacity:.7;">Download on the</div><div style="font-weight:700;">App Store</div></div>
        </a>
        <a href="#" class="d-flex align-items-center gap-2 bg-dark text-white rounded p-2 text-decoration-none" style="font-size:12px;max-width:160px;">
          <i class="bi bi-google-play" style="font-size:20px;"></i>
          <div><div style="font-size:9px;opacity:.7;">Get it on</div><div style="font-weight:700;">Google Play</div></div>
        </a>
      </div>
    </div>
    <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
      <div>© 2025 FareBuzz. All rights reserved.</div>
      <div class="d-flex gap-3 flex-wrap">
        <a href="#" style="color:#888;text-decoration:none;font-size:12px;">Privacy Policy</a>
        <a href="#" style="color:#888;text-decoration:none;font-size:12px;">Terms of Service</a>
        <a href="#" style="color:#888;text-decoration:none;font-size:12px;">Cookie Policy</a>
        <a href="#" style="color:#888;text-decoration:none;font-size:12px;">Sitemap</a>
      </div>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="asset/js/custom.js"></script>
</body>
</html>
