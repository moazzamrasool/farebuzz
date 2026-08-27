<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Itinerary — {{ $package->title }}</title>
  @include('pdf.premium.partials._styles')
</head>
<body>

  <div class="brand-header">
    @if($logo)<img src="{{ $logo }}">@endif FAREBUZZER
  </div>
  @include('pdf.premium.partials._footer', ['note' => 'This itinerary is indicative and subject to change based on availability.'])

  {{-- Cover --}}
  <div style="page-break-after: always;">
    <div style="padding: 24px 44px 16px;">
      <div style="font-size: 40px; font-weight: bold; color: #0B2545;">{{ $package->title }}</div>
      @if($travellerName)
        <div style="font-size: 14px; color: #6b7280; margin-top: 6px;">Prepared for {{ $travellerName }}</div>
      @endif
      <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">
        @if($referenceLabel){{ $referenceLabel }}: {{ $referenceValue }} &middot; @endif
        @if($travelDate)Travel Date: {{ $travelDate->format('d M Y') }} &middot; @endif
        Date: {{ now()->format('d M Y') }}
      </div>
    </div>
    <table class="cover-collage">
      <tr>
        @foreach($coverImages as $tile)
          <td>
            @if($tile)
              <div class="tile" style="background-image: url('{{ $tile }}');"></div>
            @else
              @include('pdf.premium.partials._placeholder', ['height' => 230, 'label' => $package->destination?->name])
            @endif
          </td>
        @endforeach
      </tr>
    </table>
  </div>

  @include('pdf.premium.partials._package_pages', [
    'package' => $package, 'days' => $days, 'hotels' => $hotels,
    'inclusions' => $inclusions, 'exclusions' => $exclusions,
  ])

</body>
</html>
