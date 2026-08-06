@php
  $rating = (float) ($rating ?? 0);
  $full = (int) floor($rating);
  $half = ($rating - $full) >= 0.5;
@endphp
@for($i = 0; $i < $full; $i++)<i class="bi bi-star-fill"></i>@endfor
@if($half)<i class="bi bi-star-half"></i>@endif
@for($i = 0; $i < (5 - $full - ($half ? 1 : 0)); $i++)<i class="bi bi-star"></i>@endfor
