@php
  $rating = round((float) ($rating ?? 0) * 2) / 2;
  $full = (int) floor($rating);
  $half = ($rating - $full) === 0.5;
  $empty = 5 - $full - ($half ? 1 : 0);
@endphp
@for($i = 0; $i < $full; $i++)<i class="fas fa-star"></i>@endfor
@if($half)<i class="fas fa-star-half-alt"></i>@endif
@for($i = 0; $i < $empty; $i++)<i class="far fa-star"></i>@endfor
