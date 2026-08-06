@php
  $map = [
    'paid'    => 'badge-green',
    'pending' => 'badge-amber',
    'failed'  => 'badge-red',
  ];
@endphp
<span class="badge-status {{ $map[$status] ?? 'badge-gray' }}">{{ ucfirst($status) }}</span>
