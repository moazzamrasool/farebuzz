{{--
  One day page of the premium brochure. Expects $day (array shape from
  PremiumItineraryPresenter::present()['days'][n]). Each day is its own PDF page
  (page-break-after), matching the reference "Nepal Vision" sample's one-day-per-page
  layout: full-bleed hero image, bold day numeral as the dominant element, short
  bullets over paragraphs, plus 1-2 smaller supporting images where available.

  Sizes here (hero/numeral/tile heights, paddings) are deliberately tight: an A4
  landscape page is ~543pt of usable height once the header/footer bars are
  reserved, and a day with a hero + title + 4 bullets + a meal pill + 2 supporting
  images must fit in one page — anything taller silently spills onto a second,
  unbroken page (dompdf's natural overflow break), which is what happened at the
  original 260px hero / 88px numeral / 110px tiles: the last element (the
  supporting-images row) simply landed on an unplanned extra page instead of
  being clipped or erroring, so it looked "missing" rather than broken.
--}}
<div style="page-break-after: always;">
  @if($day['image'])
    <div style="height: 190px; background-image: url('{{ $day['image'] }}'); background-size: cover; background-position: center;"></div>
  @else
    @include('pdf.premium.partials._placeholder', ['height' => 190, 'label' => $day['title']])
  @endif

  <div style="padding: 14px 44px 16px;">
    <div>
      <span class="day-word">Day</span>
      <span class="day-number">{{ $day['day_number'] }}</span>
    </div>

    @if($day['title'])
      <div style="font-size: 15px; font-weight: bold; color: #0B2545; margin-top: 2px;">{{ $day['title'] }}</div>
    @endif
    @if($day['route_summary'])
      <div style="font-size: 10px; color: #6b7280; margin-top: 1px;">{{ $day['route_summary'] }}</div>
    @endif

    @if(!empty($day['bullet_points']))
      <ul style="margin: 8px 0 0; padding-left: 20px; font-size: 11px; line-height: 1.6; color: #222;">
        @foreach($day['bullet_points'] as $point)
          <li>{{ $point }}</li>
        @endforeach
      </ul>
    @elseif($day['detail'])
      <div style="margin-top: 8px; font-size: 11px; line-height: 1.5; color: #222;">{!! $day['detail'] !!}</div>
    @endif

    @if(!empty($day['meal_tags']))
      <div style="margin-top: 6px;">
        @foreach($day['meal_tags'] as $meal)
          <span style="display: inline-block; background: #FFF1E8; color: #FF6B1A; border-radius: 10px; padding: 3px 12px; font-size: 10px; font-weight: bold; margin-right: 6px;">{{ ucfirst($meal) }}</span>
        @endforeach
      </div>
    @endif

    @if(!empty($day['supporting_images']))
      <table class="day-supporting">
        <tr>
          @foreach($day['supporting_images'] as $image)
            <td><div class="tile" style="background-image: url('{{ $image }}');"></div></td>
          @endforeach
        </tr>
      </table>
    @endif
  </div>
</div>
