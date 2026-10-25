@props(['amount', 'currency' => 'BDT'])

@php
$numeric = is_numeric($amount) ? (float) $amount : 0;
$formatted = number_format($numeric, 0);
@endphp

<span {{ $attributes->merge(['class' => 'font-semibold tracking-tight']) }}>
  {{ $currency }} {{ $formatted }}
</span>
