@csrf
@php
  $hp = $holidayPackage ?? null;
  $selectedCategoryIds = old('category_ids', $hp ? $hp->categories->pluck('id')->all() : []);
  $selectedInclusionIds = old('inclusion_feature_ids', $hp ? $hp->inclusionFeatures->pluck('id')->all() : []);
  $selectedExclusionIds = old('exclusion_feature_ids', $hp ? $hp->exclusionFeatures->pluck('id')->all() : []);
  $selectedRelatedIds = old('related_ids', $hp ? $hp->relatedPackages->pluck('id')->all() : []);
  // Drives the "Check-in Day" selects on the Hotels/Activities tabs. Derived from
  // the itinerary rows actually present (not the old nights/days inputs, which no
  // longer exist) so it stays correct across a failed-validation re-render too.
  $dayCount = max(count(old('itineraries', $hp ? $hp->itineraries->toArray() : [])), 1);
  $nightsDaysPreview = $hp && $hp->itineraries->count() > 0
    ? $hp->nights.' Nights / '.$hp->days.' Days — based on '.$hp->itineraries->count().' itinerary days'
    : 'Will be calculated from itinerary';
  $currentAdmin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
  $aiEnabled = \App\Models\AiPackageSetting::isEnabledForCurrentTenant();
@endphp

@if($currentAdmin?->isAdmin() || $currentAdmin?->can('holiday-packages.create'))
<div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap:10px;">
  <div>
    @if($aiEnabled)
      <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#aiGeneratePackageModal">
        <i class="fas fa-magic mr-1"></i>✨ Generate with AI
      </button>
      <small class="text-muted d-block d-sm-inline ml-sm-2">Drafts fields below for you to review — nothing saves until you click {{ $hp ? 'Update' : 'Create' }}.</small>
    @else
      <span class="text-muted"><i class="fas fa-magic mr-1"></i>AI Generate is turned off.</span>
    @endif
  </div>
  @if($currentAdmin?->isAdmin())
    <div class="custom-control custom-switch">
      <input type="checkbox" class="custom-control-input" id="aiFeatureToggle" {{ $aiEnabled ? 'checked' : '' }}>
      <label class="custom-control-label" for="aiFeatureToggle">AI Generate enabled</label>
    </div>
  @endif
</div>
@endif

<ul class="nav nav-tabs" id="pkgTabs" role="tablist">
  <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab-basic"><i class="fas fa-info-circle"></i> Basic</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-pricing"><i class="fas fa-tags"></i> Pricing</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-overview"><i class="fas fa-align-left"></i> Overview</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-seo"><i class="fas fa-search"></i> SEO</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-itinerary"><i class="fas fa-route"></i> Itinerary</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-inclusions"><i class="fas fa-check-double"></i> Inclusions/Exclusions</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-hotels"><i class="fas fa-hotel"></i> Hotels</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-activities"><i class="fas fa-hiking"></i> Activities</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-photos"><i class="fas fa-images"></i> Photos</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-faq"><i class="fas fa-question-circle"></i> FAQ</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-reviews"><i class="fas fa-star"></i> Reviews</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-related"><i class="fas fa-link"></i> Related</a></li>
</ul>

<div class="tab-content pkg-tab-content mb-3">

  <!-- ══════════ BASIC ══════════ -->
  <div class="tab-pane fade show active" id="tab-basic">
    <div class="pkg-section-heading">
      <span class="pkg-section-icon"><i class="fas fa-info-circle"></i></span>
      <div>
        <h5>Basic Details</h5>
        <p>Destination, title, badges and core trip stats shown on the package listing.</p>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="destination_id">Destination</label>
        <select name="destination_id" id="destination_id" class="form-control @error('destination_id') is-invalid @enderror">
          <option value="">Select a destination</option>
          @foreach($destinations as $dest)
            <option value="{{ $dest->id }}" {{ (int) old('destination_id', $hp->destination_id ?? '') === $dest->id ? 'selected' : '' }}>{{ $dest->name }}</option>
          @endforeach
        </select>
        @error('destination_id') <span class="text-danger">{{ $message }}</span> @enderror
      </div>
      <div class="form-group col-md-6">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $hp->title ?? '') }}" placeholder="e.g. Goa Beach Escape" required>
        @error('title') <span class="text-danger">{{ $message }}</span> @enderror
      </div>
    </div>

    <div class="form-group">
      <label for="slug">Slug <small class="text-muted">(auto-generated from title if left blank)</small></label>
      <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $hp->slug ?? '') }}" placeholder="e.g. goa-beach-escape">
      @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
      <label for="places_to_visit">Places to Visit</label>
      <input type="text" name="places_to_visit" id="places_to_visit" class="form-control @error('places_to_visit') is-invalid @enderror" value="{{ old('places_to_visit', $hp->places_to_visit ?? '') }}" maxlength="255" placeholder="e.g. Delhi -> Shimla -> Delhi">
      @error('places_to_visit') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
      <label class="d-block">Categories / Badges</label>
      <div class="d-flex flex-wrap" style="gap:16px;">
        @forelse($travelCategories as $category)
          <div class="custom-control custom-checkbox">
            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" class="custom-control-input" id="cat_{{ $category->id }}"
              {{ in_array($category->id, $selectedCategoryIds) ? 'checked' : '' }}>
            <label class="custom-control-label" for="cat_{{ $category->id }}">
              <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{ $category->badge_color ?? '#ccc' }};"></span>
              {{ $category->name }}
            </label>
          </div>
        @empty
          <span class="text-muted">No categories available yet.</span>
        @endforelse
      </div>
    </div>

    <div class="form-row">
      <div class="form-group col-md-3">
        <label>Nights / Days <small class="text-muted">(derived from itinerary)</small></label>
        <input type="text" id="nightsDaysPreview" class="form-control" value="{{ $nightsDaysPreview }}" readonly>
      </div>
      <div class="form-group col-md-3">
        <label for="hotel_category">Hotel Category <small class="text-muted">(derived from attached hotels)</small></label>
        <input type="text" id="hotel_category_display" class="form-control" value="" readonly style="{{ old('hotel_category_overridden', $hp->hotel_category_overridden ?? false) ? 'display:none;' : '' }}">
        <input type="text" name="hotel_category" id="hotel_category" class="form-control mt-1" value="{{ old('hotel_category', $hp->hotel_category ?? '') }}" placeholder="e.g. 4 Star Hotels + Houseboat" style="{{ old('hotel_category_overridden', $hp->hotel_category_overridden ?? false) ? '' : 'display:none;' }}">
        <div class="custom-control custom-checkbox mt-1">
          <input type="checkbox" class="custom-control-input" id="hotel_category_overridden" name="hotel_category_overridden" value="1" {{ old('hotel_category_overridden', $hp->hotel_category_overridden ?? false) ? 'checked' : '' }}>
          <label class="custom-control-label" for="hotel_category_overridden">Override</label>
        </div>
      </div>
      <div class="form-group col-md-3">
        <label for="meals">Meals <small class="text-muted">(derived from itinerary meal tags)</small></label>
        <input type="text" id="meals_display" class="form-control" value="" readonly style="{{ old('meals_overridden', $hp->meals_overridden ?? false) ? 'display:none;' : '' }}">
        <input type="text" name="meals" id="meals" class="form-control mt-1" value="{{ old('meals', $hp->meals ?? '') }}" placeholder="e.g. Daily Breakfast" style="{{ old('meals_overridden', $hp->meals_overridden ?? false) ? '' : 'display:none;' }}">
        <div class="custom-control custom-checkbox mt-1">
          <input type="checkbox" class="custom-control-input" id="meals_overridden" name="meals_overridden" value="1" {{ old('meals_overridden', $hp->meals_overridden ?? false) ? 'checked' : '' }}>
          <label class="custom-control-label" for="meals_overridden">Override</label>
        </div>
      </div>
      <div class="form-group col-md-3">
        <label for="language">Language</label>
        <input type="text" name="language" id="language" class="form-control" value="{{ old('language', $hp->language ?? '') }}" placeholder="e.g. English / Hindi">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group col-md-3">
        <label class="d-block">Best Seller</label>
        <div class="custom-control custom-checkbox">
          <input type="checkbox" name="is_best_seller" value="1" class="custom-control-input" id="is_best_seller" {{ old('is_best_seller', $hp->is_best_seller ?? false) ? 'checked' : '' }}>
          <label class="custom-control-label" for="is_best_seller">Show "Best Seller" tag</label>
        </div>
      </div>
      <div class="form-group col-md-3">
        <label class="d-block">Featured</label>
        <div class="custom-control custom-checkbox">
          <input type="checkbox" name="featured" value="1" class="custom-control-input" id="featured" {{ old('featured', $hp->featured ?? false) ? 'checked' : '' }}>
          <label class="custom-control-label" for="featured">Show as featured</label>
        </div>
      </div>
      <div class="form-group col-md-3">
        <label for="status">Status</label>
        <select name="status" id="status" class="form-control">
          <option value="active" {{ old('status', $hp->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ old('status', $hp->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>
      <div class="form-group col-md-3">
        <label for="sort_order">Sort Order</label>
        <input type="number" min="0" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $hp->sort_order ?? 0) }}">
      </div>
    </div>
  </div>

  <!-- ══════════ PRICING ══════════ -->
  <div class="tab-pane fade" id="tab-pricing">
    <div class="pkg-section-heading">
      <span class="pkg-section-icon"><i class="fas fa-tags"></i></span>
      <div>
        <h5>Pricing &amp; Departures</h5>
        <p>Base pricing, departure cities and room-type options for this package.</p>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-3">
        <label for="price">Base Price / Person</label>
        <input type="number" step="0.01" min="0" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $hp->price ?? '') }}" placeholder="e.g. 24000" required>
        @error('price') <span class="text-danger">{{ $message }}</span> @enderror
      </div>
      <div class="form-group col-md-3">
        <label for="discounted_price">Discounted Price</label>
        <input type="number" step="0.01" min="0" name="discounted_price" id="discounted_price" class="form-control @error('discounted_price') is-invalid @enderror" value="{{ old('discounted_price', $hp->discounted_price ?? '') }}" placeholder="e.g. 18999">
        @error('discounted_price') <span class="text-danger">{{ $message }}</span> @enderror
      </div>
      @if($hp && $hp->savings_percent)
        <div class="form-group col-md-3">
          <label class="d-block">Savings</label>
          <span class="badge badge-success" style="font-size:14px;">{{ $hp->savings_percent }}% OFF</span>
        </div>
      @endif
    </div>

    <div class="form-group">
      <label class="d-block">Booking Type</label>
      <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" name="booking_type" id="booking_type_enquiry" value="enquiry_only" class="custom-control-input"
          {{ old('booking_type', $hp->booking_type ?? 'enquiry_only') === 'enquiry_only' ? 'checked' : '' }}>
        <label class="custom-control-label" for="booking_type_enquiry">Enquiry Only <small class="text-muted">— detail page shows only "Enquire Now"</small></label>
      </div>
      <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" name="booking_type" id="booking_type_book" value="book_enquiry" class="custom-control-input"
          {{ old('booking_type', $hp->booking_type ?? 'enquiry_only') === 'book_enquiry' ? 'checked' : '' }}>
        <label class="custom-control-label" for="booking_type_book">Book + Enquiry <small class="text-muted">— shows both "Book Now" and "Enquire Now"</small></label>
      </div>
      @error('booking_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <hr>
    <h6><i class="fas fa-plane-departure text-muted mr-1"></i> Departure Cities</h6>
    <div data-repeater id="departureCitiesRepeater">
      <div data-repeater-rows>
        @foreach(old('departure_cities', $hp ? $hp->departureCities->pluck('city_name')->all() : []) as $i => $city)
          <div class="form-row align-items-center mb-2 repeater-row-plain" data-repeater-row>
            <div class="form-group col-md-8 mb-2">
              <input type="text" name="departure_cities[{{ $i }}]" class="form-control" value="{{ $city }}" placeholder="e.g. Delhi (DEL)">
            </div>
            <div class="form-group col-md-2 mb-2">
              <button type="button" class="btn btn-danger btn-sm btn-block" data-repeater-remove><i class="fas fa-times"></i></button>
            </div>
          </div>
        @endforeach
      </div>
      <template data-repeater-template>
        <div class="form-row align-items-center mb-2 repeater-row-plain" data-repeater-row>
          <div class="form-group col-md-8 mb-2">
            <input type="text" name="departure_cities[__INDEX__]" class="form-control" placeholder="e.g. Delhi (DEL)">
          </div>
          <div class="form-group col-md-2 mb-2">
            <button type="button" class="btn btn-danger btn-sm btn-block" data-repeater-remove><i class="fas fa-times"></i></button>
          </div>
        </div>
      </template>
      <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add><i class="fas fa-plus mr-1"></i>Add Departure City</button>
    </div>

    <hr>
    <h6><i class="fas fa-bed text-muted mr-1"></i> Room Types</h6>
    <div data-repeater id="roomTypesRepeater">
      <div data-repeater-rows>
        @foreach(($hp->roomTypes ?? collect()) as $i => $item)
          @include('admin.holiday-packages.partials._room_type_row', ['index' => $i, 'item' => $item])
        @endforeach
      </div>
      <template data-repeater-template>
        @include('admin.holiday-packages.partials._room_type_row', ['index' => '__INDEX__', 'item' => null])
      </template>
      <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add>+ Add Room Type</button>
    </div>
  </div>

  <!-- ══════════ OVERVIEW ══════════ -->
  <div class="tab-pane fade" id="tab-overview">
    <div class="pkg-section-heading">
      <span class="pkg-section-icon"><i class="fas fa-align-left"></i></span>
      <div>
        <h5>Overview</h5>
        <p>The main package description.</p>
      </div>
    </div>
    <div class="form-group">
      <label for="overview">Overview Description <small class="text-muted">(e.g. "Escape to the sun-kissed shores of Goa with our handpicked 5-day package...")</small></label>
      <textarea name="overview" id="overview" rows="5" class="form-control rich-text-editor" placeholder="e.g. Escape to the sun-kissed shores of Goa with our handpicked 5-day package...">{{ old('overview', $hp->overview ?? '') }}</textarea>
    </div>
  </div>

  <!-- ══════════ SEO ══════════ -->
  <div class="tab-pane fade" id="tab-seo">
    <div class="pkg-section-heading">
      <span class="pkg-section-icon"><i class="fas fa-search"></i></span>
      <div>
        <h5>SEO</h5>
        <p>Search-engine title, description and keywords for this package's public page.</p>
      </div>
    </div>
    <div class="form-group">
      <label for="seo_content">Long-form SEO Content <small class="text-muted">(shown in a dedicated SEO block on the package page, separate from the Overview above)</small></label>
      <textarea name="seo_content" id="seo_content" rows="8" class="form-control rich-text-editor @error('seo_content') is-invalid @enderror" placeholder="e.g. Book the best Kashmir tour package from Delhi with FareBuzzer Travel...">{{ old('seo_content', $hp->seo_content ?? '') }}</textarea>
      @error('seo_content') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    @include('admin.partials._seo_fields', [
      'seo' => $hp,
      'seoUrl' => $hp && $hp->slug ? route('packages.show', $hp->slug) : null,
      'seoPreviewFallback' => ($hp->title ?? 'Holiday Package').' – FareBuzzer',
    ])
  </div>

  <!-- ══════════ INCLUSIONS / EXCLUSIONS ══════════ -->
  <div class="tab-pane fade" id="tab-inclusions">
    <div class="pkg-section-heading">
      <span class="pkg-section-icon"><i class="fas fa-check-double"></i></span>
      <div>
        <h5>Inclusions &amp; Exclusions</h5>
        <p>Pick from the shared feature library, plus any one-off lines specific to this package.</p>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <h6 class="text-success">What's Included</h6>
        <div class="mb-3">
          @forelse($inclusionFeatures as $feature)
            <div class="custom-control custom-checkbox">
              <input type="checkbox" name="inclusion_feature_ids[]" value="{{ $feature->id }}" class="custom-control-input" id="incl_{{ $feature->id }}"
                {{ in_array($feature->id, $selectedInclusionIds) ? 'checked' : '' }}>
              <label class="custom-control-label" for="incl_{{ $feature->id }}"><i class="{{ $feature->icon }}"></i> {{ $feature->title }}</label>
            </div>
          @empty
            <span class="text-muted">No inclusion items yet — add some under Master Data &gt; Inclusions.</span>
          @endforelse
        </div>

        <h6>Custom Inclusion Lines</h6>
        <div data-repeater id="customInclusionsRepeater">
          <div data-repeater-rows>
            @foreach(old('custom_inclusions', $hp ? $hp->customInclusions->pluck('title')->all() : []) as $i => $title)
              <div class="form-row align-items-center mb-2 repeater-row-plain" data-repeater-row>
                <div class="form-group col-9 mb-2"><input type="text" name="custom_inclusions[{{ $i }}]" class="form-control" value="{{ $title }}" placeholder="e.g. Dedicated FareBuzzer travel guide"></div>
                <div class="form-group col-3 mb-2"><button type="button" class="btn btn-danger btn-sm btn-block" data-repeater-remove><i class="fas fa-times"></i></button></div>
              </div>
            @endforeach
          </div>
          <template data-repeater-template>
            <div class="form-row align-items-center mb-2 repeater-row-plain" data-repeater-row>
              <div class="form-group col-9 mb-2"><input type="text" name="custom_inclusions[__INDEX__]" class="form-control" placeholder="e.g. Dedicated FareBuzzer travel guide"></div>
              <div class="form-group col-3 mb-2"><button type="button" class="btn btn-danger btn-sm btn-block" data-repeater-remove><i class="fas fa-times"></i></button></div>
            </div>
          </template>
          <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add><i class="fas fa-plus mr-1"></i>Add Line</button>
        </div>
      </div>

      <div class="col-md-6">
        <h6 class="text-danger">What's Not Included</h6>
        <div class="mb-3">
          @forelse($exclusionFeatures as $feature)
            <div class="custom-control custom-checkbox">
              <input type="checkbox" name="exclusion_feature_ids[]" value="{{ $feature->id }}" class="custom-control-input" id="excl_{{ $feature->id }}"
                {{ in_array($feature->id, $selectedExclusionIds) ? 'checked' : '' }}>
              <label class="custom-control-label" for="excl_{{ $feature->id }}"><i class="{{ $feature->icon }}"></i> {{ $feature->title }}</label>
            </div>
          @empty
            <span class="text-muted">No exclusion items yet — add some under Master Data &gt; Exclusions.</span>
          @endforelse
        </div>

        <h6>Custom Exclusion Lines</h6>
        <div data-repeater id="customExclusionsRepeater">
          <div data-repeater-rows>
            @foreach(old('custom_exclusions', $hp ? $hp->customExclusions->pluck('title')->all() : []) as $i => $title)
              <div class="form-row align-items-center mb-2 repeater-row-plain" data-repeater-row>
                <div class="form-group col-9 mb-2"><input type="text" name="custom_exclusions[{{ $i }}]" class="form-control" value="{{ $title }}" placeholder="e.g. Porterage / laundry charges"></div>
                <div class="form-group col-3 mb-2"><button type="button" class="btn btn-danger btn-sm btn-block" data-repeater-remove><i class="fas fa-times"></i></button></div>
              </div>
            @endforeach
          </div>
          <template data-repeater-template>
            <div class="form-row align-items-center mb-2 repeater-row-plain" data-repeater-row>
              <div class="form-group col-9 mb-2"><input type="text" name="custom_exclusions[__INDEX__]" class="form-control" placeholder="e.g. Porterage / laundry charges"></div>
              <div class="form-group col-3 mb-2"><button type="button" class="btn btn-danger btn-sm btn-block" data-repeater-remove><i class="fas fa-times"></i></button></div>
            </div>
          </template>
          <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add><i class="fas fa-plus mr-1"></i>Add Line</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ══════════ ITINERARY ══════════ -->
  <div class="tab-pane fade" id="tab-itinerary">
    <div class="pkg-section-heading">
      <span class="pkg-section-icon"><i class="fas fa-route"></i></span>
      <div>
        <h5>Day-by-Day Itinerary</h5>
        <p>Break the trip down into days with a title, summary and bullet-point plan.</p>
      </div>
    </div>
    <div data-repeater id="itineraryRepeater">
      <div data-repeater-rows>
        @foreach(($hp->itineraries ?? collect()) as $i => $item)
          @include('admin.holiday-packages.partials._itinerary_row', ['index' => $i, 'item' => $item])
        @endforeach
      </div>
      <template data-repeater-template>
        @include('admin.holiday-packages.partials._itinerary_row', ['index' => '__INDEX__', 'item' => null])
      </template>
      <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add><i class="fas fa-plus mr-1"></i>Add Day</button>
    </div>
  </div>

  <!-- ══════════ HOTELS (optional paid add-ons) ══════════ -->
  <div class="tab-pane fade" id="tab-hotels">
    <div class="pkg-section-heading">
      <span class="pkg-section-icon"><i class="fas fa-hotel"></i></span>
      <div>
        <h5>Hotels</h5>
        <p>Attach hotels used on this package. Pick a room type for its per-night price, or override it manually. Leave price blank with no room type selected to attach as a display-only / included hotel. Entirely optional — a package can have none.</p>
      </div>
    </div>

    <input type="text" id="hotelSearch" class="form-control mb-3" placeholder="Search hotels by name...">

    <div class="row" id="hotelsGrid">
      @forelse($hotels as $hotel)
        @php
          $attachedHotel = $hp ? $hp->hotels->firstWhere('id', $hotel->id) : null;
          $isHotelAttached = old("hotels.$hotel->id.attach", $attachedHotel ? '1' : null);
        @endphp
        <div class="col-md-6 col-lg-4 mb-3 hotel-card-col" data-hotel-name="{{ strtolower($hotel->name) }}">
          <div class="card h-100 hotel-pick-card {{ $isHotelAttached ? 'is-attached' : '' }}">
            <div class="card-body">
              <div class="custom-control custom-checkbox mb-2">
                <input type="checkbox" class="custom-control-input hotel-attach-toggle" id="htl_attach_{{ $hotel->id }}"
                  name="hotels[{{ $hotel->id }}][attach]" value="1" data-star-rating="{{ $hotel->star_rating }}" {{ $isHotelAttached ? 'checked' : '' }}>
                <label class="custom-control-label font-weight-bold" for="htl_attach_{{ $hotel->id }}">{{ $hotel->name }} <small class="text-muted">({{ $hotel->star_rating }}★)</small></label>
              </div>
              @if($hotel->cover_image)
                <img src="{{ asset('storage/'.$hotel->cover_image) }}" alt="{{ $hotel->name }}" class="img-fluid rounded mb-2" style="max-height:100px;width:100%;object-fit:cover;">
              @endif
              <div class="text-muted small mb-2">{{ $hotel->address ?: 'No address on file' }}</div>

              <div class="hotel-extra-fields" style="{{ $isHotelAttached ? '' : 'display:none;' }}">
                <div class="form-group mb-2">
                  <label class="form-label-sm">Check-in Day <small class="text-muted">(which day of the itinerary this stay starts on)</small></label>
                  <select class="form-control form-control-sm" name="hotels[{{ $hotel->id }}][day_number]">
                    <option value="">— No specific day —</option>
                    @for($d = 1; $d <= $dayCount; $d++)
                      <option value="{{ $d }}" {{ (string) old("hotels.$hotel->id.day_number", $attachedHotel->pivot->day_number ?? '') === (string) $d ? 'selected' : '' }}>Day {{ $d }}</option>
                    @endfor
                  </select>
                </div>
                <div class="form-group mb-2">
                  <label class="form-label-sm">Room Type <small class="text-muted">(sets the per-night price)</small></label>
                  <select class="form-control form-control-sm room-type-select" name="hotels[{{ $hotel->id }}][room_type_id]">
                    <option value="">— No room type (manual price) —</option>
                    @foreach($hotel->roomTypes as $room)
                      <option value="{{ $room->id }}"
                        data-price="{{ number_format($room->price, 2, '.', '') }}"
                        data-discounted-price="{{ $room->discounted_price !== null ? number_format($room->discounted_price, 2, '.', '') : '' }}"
                        data-bed-type="{{ $room->bed_type?->label() }}"
                        data-adults="{{ $room->occupancy_adults }}"
                        data-children="{{ $room->occupancy_children }}"
                        data-size="{{ $room->size_sqft }}"
                        data-meal-plan="{{ $room->mealPlanLabel }}"
                        {{ (string) old("hotels.$hotel->id.room_type_id", $attachedHotel->pivot->room_type_id ?? '') === (string) $room->id ? 'selected' : '' }}>
                        {{ $room->name }} — ₹{{ number_format($room->sell_price, 2) }}/night
                      </option>
                    @endforeach
                  </select>
                  <div class="room-type-detail small text-muted mt-1"></div>
                </div>
                <div class="form-group mb-2">
                  <label class="form-label-sm">Price Override / Night <small class="text-muted">(blank = room type price)</small></label>
                  <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                    name="hotels[{{ $hotel->id }}][price]"
                    value="{{ old("hotels.$hotel->id.price", $attachedHotel->pivot->price ?? '') }}">
                </div>
                <div class="form-group mb-2">
                  <label class="form-label-sm">Nights</label>
                  <input type="number" min="1" class="form-control form-control-sm"
                    name="hotels[{{ $hotel->id }}][nights]"
                    value="{{ old("hotels.$hotel->id.nights", $attachedHotel->pivot->nights ?? ($hp->nights ?? 1)) }}">
                </div>
                <div class="form-group mb-2">
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="htl_optional_{{ $hotel->id }}"
                      name="hotels[{{ $hotel->id }}][is_optional]" value="1"
                      {{ old("hotels.$hotel->id.is_optional", $attachedHotel->pivot->is_optional ?? true) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="htl_optional_{{ $hotel->id }}">Optional <small class="text-muted">(uncheck = Included, display-only)</small></label>
                  </div>
                </div>
                <div class="form-group mb-2">
                  <label class="form-label-sm">Sort Order</label>
                  <input type="number" min="0" class="form-control form-control-sm"
                    name="hotels[{{ $hotel->id }}][sort_order]"
                    value="{{ old("hotels.$hotel->id.sort_order", $attachedHotel->pivot->sort_order ?? 0) }}">
                </div>
                <div class="form-group mb-0">
                  <label class="form-label-sm">Note</label>
                  <input type="text" class="form-control form-control-sm"
                    name="hotels[{{ $hotel->id }}][note]"
                    value="{{ old("hotels.$hotel->id.note", $attachedHotel->pivot->note ?? '') }}"
                    placeholder="e.g. Subject to availability. A similar or better property may be provided.">
                </div>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12"><span class="text-muted">No hotels available yet — add some under Master Data &gt; Hotels.</span></div>
      @endforelse
    </div>
  </div>

  <!-- ══════════ ACTIVITIES (optional paid add-ons) ══════════ -->
  <div class="tab-pane fade" id="tab-activities">
    <div class="pkg-section-heading">
      <span class="pkg-section-icon"><i class="fas fa-hiking"></i></span>
      <div>
        <h5>Activities (Optional Add-ons)</h5>
        <p>Attach activities travellers can add on at booking time. Leave price blank to use the activity's own master price. Entirely optional — a package can have none.</p>
      </div>
    </div>

    <input type="text" id="activitySearch" class="form-control mb-3" placeholder="Search activities by name...">

    <div class="row" id="activitiesGrid">
      @forelse($activities as $activity)
        @php
          $attachedActivity = $hp ? $hp->optionalActivities->firstWhere('id', $activity->id) : null;
          $isAttached = old("activities.$activity->id.attach", $attachedActivity ? '1' : null);
        @endphp
        <div class="col-md-6 col-lg-4 mb-3 activity-card-col" data-activity-name="{{ strtolower($activity->name) }}">
          <div class="card h-100 activity-pick-card {{ $isAttached ? 'is-attached' : '' }}">
            <div class="card-body">
              <div class="custom-control custom-checkbox mb-2">
                <input type="checkbox" class="custom-control-input activity-attach-toggle" id="act_attach_{{ $activity->id }}"
                  name="activities[{{ $activity->id }}][attach]" value="1" {{ $isAttached ? 'checked' : '' }}>
                <label class="custom-control-label font-weight-bold" for="act_attach_{{ $activity->id }}">{{ $activity->name }}</label>
              </div>
              @if($activity->image)
                <img src="{{ asset('storage/'.$activity->image) }}" alt="{{ $activity->name }}" class="img-fluid rounded mb-2" style="max-height:100px;width:100%;object-fit:cover;">
              @endif
              <div class="text-muted small mb-2">Base price: ₹{{ number_format($activity->price ?? 0, 2) }}</div>

              <div class="activity-extra-fields" style="{{ $isAttached ? '' : 'display:none;' }}">
                <div class="form-group mb-2">
                  <label class="form-label-sm">Day <small class="text-muted">(which day of the itinerary this happens on)</small></label>
                  <select class="form-control form-control-sm" name="activities[{{ $activity->id }}][day_number]">
                    <option value="">— No specific day —</option>
                    @for($d = 1; $d <= $dayCount; $d++)
                      <option value="{{ $d }}" {{ (string) old("activities.$activity->id.day_number", $attachedActivity->pivot->day_number ?? '') === (string) $d ? 'selected' : '' }}>Day {{ $d }}</option>
                    @endfor
                  </select>
                </div>
                <div class="form-group mb-2">
                  <label class="form-label-sm">Price Override <small class="text-muted">(blank = base price)</small></label>
                  <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                    name="activities[{{ $activity->id }}][price]"
                    value="{{ old("activities.$activity->id.price", $attachedActivity->pivot->price ?? '') }}">
                </div>
                <div class="form-group mb-2">
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="act_optional_{{ $activity->id }}"
                      name="activities[{{ $activity->id }}][is_optional]" value="1"
                      {{ old("activities.$activity->id.is_optional", $attachedActivity->pivot->is_optional ?? true) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="act_optional_{{ $activity->id }}">Optional <small class="text-muted">(uncheck = Included)</small></label>
                  </div>
                </div>
                <div class="form-group mb-2">
                  <label class="form-label-sm">Sort Order</label>
                  <input type="number" min="0" class="form-control form-control-sm"
                    name="activities[{{ $activity->id }}][sort_order]"
                    value="{{ old("activities.$activity->id.sort_order", $attachedActivity->pivot->sort_order ?? 0) }}">
                </div>
                <div class="form-group mb-0">
                  <label class="form-label-sm">Note</label>
                  <input type="text" class="form-control form-control-sm"
                    name="activities[{{ $activity->id }}][note]"
                    value="{{ old("activities.$activity->id.note", $attachedActivity->pivot->note ?? '') }}"
                    placeholder="e.g. Subject to weather conditions">
                </div>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12"><span class="text-muted">No activities available yet — add some under Master Data &gt; Activities.</span></div>
      @endforelse
    </div>
  </div>

  <!-- ══════════ PHOTOS ══════════ -->
  <div class="tab-pane fade" id="tab-photos">
    <div class="pkg-section-heading">
      <span class="pkg-section-icon"><i class="fas fa-images"></i></span>
      <div>
        <h5>Photo Gallery</h5>
        <p>Recommended size 1200&times;800. Mark one photo as <strong>Cover</strong> — it becomes the main gallery image.</p>
      </div>
    </div>
    <div data-repeater id="photosRepeater">
      <div data-repeater-rows>
        @foreach(($hp->photos ?? collect()) as $i => $item)
          @include('admin.holiday-packages.partials._photo_row', ['index' => $i, 'item' => $item])
        @endforeach
      </div>
      <template data-repeater-template>
        @include('admin.holiday-packages.partials._photo_row', ['index' => '__INDEX__', 'item' => null])
      </template>
      <button type="button" class="btn btn-outline-primary btn-sm mt-1" data-repeater-add><i class="fas fa-plus mr-1"></i>Add Photo</button>
    </div>
  </div>

  <!-- ══════════ FAQ ══════════ -->
  <div class="tab-pane fade" id="tab-faq">
    <div class="pkg-section-heading">
      <span class="pkg-section-icon"><i class="fas fa-question-circle"></i></span>
      <div>
        <h5>Frequently Asked Questions</h5>
        <p>Answer the questions travellers most commonly ask about this package.</p>
      </div>
    </div>
    <div data-repeater id="faqRepeater">
      <div data-repeater-rows>
        @foreach(($hp->faqs ?? collect()) as $i => $item)
          @include('admin.holiday-packages.partials._faq_row', ['index' => $i, 'item' => $item])
        @endforeach
      </div>
      <template data-repeater-template>
        @include('admin.holiday-packages.partials._faq_row', ['index' => '__INDEX__', 'item' => null])
      </template>
      <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add><i class="fas fa-plus mr-1"></i>Add FAQ</button>
    </div>
  </div>

  <!-- ══════════ REVIEWS ══════════ -->
  <div class="tab-pane fade" id="tab-reviews">
    <div class="pkg-section-heading">
      <span class="pkg-section-icon"><i class="fas fa-star"></i></span>
      <div>
        <h5>Guest Reviews</h5>
        <p>Manage traveller reviews and category ratings shown on the package page.</p>
      </div>
    </div>
    @if($hp)
      <div class="alert alert-info">
        Average rating: <strong>{{ $hp->average_rating ?? 'N/A' }}</strong>
        @php $bars = $hp->category_rating_bars; @endphp
        &middot; Hotels: {{ $bars['hotels'] ?? 'N/A' }}
        &middot; Sightseeing: {{ $bars['sightseeing'] ?? 'N/A' }}
        &middot; Food: {{ $bars['food'] ?? 'N/A' }}
        &middot; Value: {{ $bars['value'] ?? 'N/A' }}
        <small class="d-block text-muted">Computed automatically from the reviews below.</small>
      </div>
    @endif
    <div data-repeater id="reviewsRepeater">
      <div data-repeater-rows>
        @foreach(($hp->reviews ?? collect()) as $i => $item)
          @include('admin.holiday-packages.partials._review_row', ['index' => $i, 'item' => $item])
        @endforeach
      </div>
      <template data-repeater-template>
        @include('admin.holiday-packages.partials._review_row', ['index' => '__INDEX__', 'item' => null])
      </template>
      <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add><i class="fas fa-plus mr-1"></i>Add Review</button>
    </div>
  </div>

  <!-- ══════════ RELATED ══════════ -->
  <div class="tab-pane fade" id="tab-related">
    <div class="pkg-section-heading">
      <span class="pkg-section-icon"><i class="fas fa-link"></i></span>
      <div>
        <h5>Related Packages</h5>
        <p>Choose which packages to cross-sell here — leave blank to auto-suggest by shared category.</p>
      </div>
    </div>
    <label class="d-block font-weight-bold">You Might Also Like</label>
    <div class="d-flex flex-wrap" style="gap:16px;">
      @forelse($candidatePackages as $candidate)
        <div class="custom-control custom-checkbox">
          <input type="checkbox" name="related_ids[]" value="{{ $candidate->id }}" class="custom-control-input" id="rel_{{ $candidate->id }}"
            {{ in_array($candidate->id, $selectedRelatedIds) ? 'checked' : '' }}>
          <label class="custom-control-label" for="rel_{{ $candidate->id }}">{{ $candidate->title }}</label>
        </div>
      @empty
        <span class="text-muted">No other packages available yet.</span>
      @endforelse
    </div>
  </div>

</div>

<div class="pkg-form-actions">
  <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>{{ $hp ? 'Update' : 'Create' }} Holiday Package</button>
  <a href="{{ route('crm.holiday-packages.index') }}" class="btn btn-secondary">Cancel</a>
</div>

@if($aiEnabled)
<!-- ══════════ AI GENERATE MODAL ══════════ -->
<div class="modal fade zoom-modal" id="aiGeneratePackageModal" tabindex="-1" role="dialog" aria-labelledby="aiGeneratePackageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aiGeneratePackageModalLabel"><i class="fas fa-magic mr-1"></i>Generate Package with AI</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="ai-generate-error alert alert-danger d-none" role="alert"></div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="ai_destination">Destination</label>
            <input type="text" id="ai_destination" class="form-control" list="ai_destination_list" placeholder="e.g. Goa" value="{{ $hp?->destination?->name }}">
            <datalist id="ai_destination_list">
              @foreach($destinations as $dest)
                <option value="{{ $dest->name }}">
              @endforeach
            </datalist>
          </div>
          <div class="form-group col-md-6">
            <label for="ai_nights">Number of Nights</label>
            <input type="number" min="0" max="60" id="ai_nights" class="form-control" value="{{ $hp->nights ?? 4 }}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="ai_theme">Trip Theme / Category</label>
            <input type="text" id="ai_theme" class="form-control" list="ai_theme_list" placeholder="e.g. Honeymoon">
            <datalist id="ai_theme_list">
              @foreach($travelCategories as $category)
                <option value="{{ $category->name }}">
              @endforeach
            </datalist>
          </div>
          <div class="form-group col-md-4">
            <label for="ai_budget">Budget Level</label>
            <select id="ai_budget" class="form-control">
              <option value="budget">Budget</option>
              <option value="mid-range" selected>Mid-range</option>
              <option value="luxury">Luxury</option>
            </select>
          </div>
          <div class="form-group col-md-4">
            <label for="ai_traveller_type">Traveller Type</label>
            <select id="ai_traveller_type" class="form-control">
              <option value="couple">Couple</option>
              <option value="family">Family</option>
              <option value="group">Group</option>
              <option value="solo">Solo</option>
              <option value="friends">Friends</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="ai_extra_instructions">Extra Instructions <small class="text-muted">(optional)</small></label>
          <textarea id="ai_extra_instructions" rows="2" class="form-control" placeholder="e.g. Focus on beaches and nightlife, avoid water sports"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" id="aiGenerateSubmitBtn" class="btn btn-primary">
          <span class="btn-label"><i class="fas fa-magic mr-1"></i>Generate</span>
          <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  window.FB_AI_GENERATE_URL = @json(route('crm.holiday-packages.ai-generate'));
</script>
@endif

@if($currentAdmin?->isAdmin())
<script>
  window.FB_AI_SETTINGS_TOGGLE_URL = @json(route('crm.holiday-packages.ai-settings.toggle'));
</script>
@endif

@if($currentAdmin?->isAdmin() || $currentAdmin?->can('holiday-packages.create'))
<script src="{{ asset('admin/ai-package-generate.js') }}"></script>
@endif
