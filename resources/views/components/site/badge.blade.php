@props(['variant' => 'sale'])

<span {{ $attributes->class(['va_badge', 'va_badge--'.$variant]) }}>{{ $slot }}</span>
