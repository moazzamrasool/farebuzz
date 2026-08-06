@php
  $map = [
    'confirmed' => 'badge-green',
    'pending'   => 'badge-amber',
    'cancelled' => 'badge-gray',
    'failed'    => 'badge-red',
  ];
@endphp
<span class="badge-status {{ $map[$status] ?? 'badge-gray' }}">{{ ucfirst($status) }}</span>
