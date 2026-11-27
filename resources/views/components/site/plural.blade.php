@props(['count', 'one', 'few', 'many'])

@php
    $mod10 = $count % 10;
    $mod100 = $count % 100;

    $word = match (true) {
        $mod10 === 1 && $mod100 !== 11 => $one,
        $mod10 >= 2 && $mod10 <= 4 && ($mod100 < 10 || $mod100 >= 20) => $few,
        default => $many,
    };
@endphp

{{ $word }}
