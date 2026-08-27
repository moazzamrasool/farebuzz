{{--
  Trip overview + day-by-day + hotels + inclusions/exclusions — everything a
  premium document shows once a HolidayPackage is attached. Shared between
  pdf.premium.itinerary and pdf.premium.quotation so both stay visually
  identical wherever a package is involved.
  Expects: $package, $days, $hotels, $inclusions, $exclusions (from
  PremiumItineraryPresenter::present()).
--}}
<div style="page-break-after: always;">
  <div class="kicker">TRIP OVERVIEW</div>
  <div class="section-title">{{ $package->destination?->name ?? $package->title }}</div>
  <table class="two-col">
    <tr>
      <td>
        <strong>Duration</strong>
        <ul class="plain"><li>{{ $package->nights }} Nights / {{ $package->days }} Days</li></ul>
        @if($package->hotel_category)
          <strong>Hotels</strong>
          <ul class="plain"><li>{{ $package->hotel_category }}</li></ul>
        @endif
      </td>
      <td>
        @if($package->meals)
          <strong>Meals</strong>
          <ul class="plain"><li>{{ $package->meals }}</li></ul>
        @endif
        @if($package->places_to_visit)
          <strong>Places to Visit</strong>
          <ul class="plain"><li>{{ $package->places_to_visit }}</li></ul>
        @endif
      </td>
    </tr>
  </table>
</div>

@foreach($days as $day)
  @include('pdf.premium.partials._day', ['day' => $day])
@endforeach

@if(!empty($hotels))
  <div style="page-break-after: always;">
    <div class="kicker">STAY</div>
    <div class="section-title">Hotels</div>
    @foreach($hotels as $hotel)
      <div class="hotel-card">
        @if($hotel['image'])
          <div class="thumb" style="background-image: url('{{ $hotel['image'] }}');"></div>
        @else
          @include('pdf.premium.partials._placeholder', ['height' => 90, 'label' => $hotel['name']])
        @endif
        <div class="body">
          <div class="name">{{ $hotel['name'] }}</div>
          @if($hotel['star_rating'])
            <div class="stars">{{ str_repeat('★', (int) $hotel['star_rating']) }}</div>
          @endif
          <div class="meta">
            @if($hotel['nights']){{ $hotel['nights'] }} Night{{ $hotel['nights'] > 1 ? 's' : '' }}@endif
            @if($hotel['note']) &middot; {{ $hotel['note'] }}@endif
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif

<div>
  <div class="kicker">GOOD TO KNOW</div>
  <div class="section-title">Inclusions &amp; Exclusions</div>
  <div style="padding: 0 34px;">
    @if(!empty($inclusions))
      <div class="feature-box inclusion">
        <h4>&#10003; Inclusions</h4>
        <ul>@foreach($inclusions as $item)<li>{{ $item }}</li>@endforeach</ul>
      </div>
    @endif
    @if(!empty($exclusions))
      <div class="feature-box exclusion">
        <h4>&#10007; Exclusions</h4>
        <ul>@foreach($exclusions as $item)<li>{{ $item }}</li>@endforeach</ul>
      </div>
    @endif
  </div>
</div>
