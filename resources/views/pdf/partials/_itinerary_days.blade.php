{{--
  Day-by-day itinerary blocks, shared between pdf/package_itinerary.blade.php and
  pdf/quotation.blade.php. Expects: $itineraries (collection of PackageItinerary,
  ordered by day_number). Parent view must define the .day-block/.day-header/
  .day-route/.day-detail/.day-bullets/.meal-tag styles (see pdf/package_itinerary.blade.php).
--}}
@foreach($itineraries as $day)
  <div class="day-block">
    <div class="day-header">Day {{ $day->day_number }}@if($day->title) &middot; {{ $day->title }}@endif</div>
    @if($day->route_summary)
      <div class="day-route">{{ $day->route_summary }}</div>
    @endif
    @if($day->detail)
      <div class="day-detail">{!! $day->detail !!}</div>
    @endif
    @if(!empty($day->bullet_points))
      <ul class="day-bullets">
        @foreach($day->bullet_points as $point)
          <li>{{ $point }}</li>
        @endforeach
      </ul>
    @endif
    @if(!empty($day->meal_tags))
      <div style="padding:0 8px 6px;">
        @foreach($day->meal_tags as $meal)
          <span class="meal-tag">{{ $meal }}</span>
        @endforeach
      </div>
    @endif
  </div>
@endforeach
