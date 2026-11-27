@props(['amount', 'old' => false])

@php
    $scale = (int) config('marketplace.currency_scale', 100);
    $currency = config('marketplace.currency', 'BYN');
    $value = $amount / $scale;
    $formatted = floor($value) === $value
        ? number_format($value, 0, ',', ' ')
        : number_format($value, 2, ',', ' ');
@endphp

<span @class(['va_price', 'va_price--old' => $old])>{{ $formatted }} {{ $currency }}</span>
