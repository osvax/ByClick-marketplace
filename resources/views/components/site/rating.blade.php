@props(['value' => 0, 'count' => 0])

<div class="va_rating">
    <svg class="va_rating__star" aria-hidden="true"><use href="#va-i-star" /></svg>
    <span class="va_rating__value">{{ number_format((float) $value, 1) }}</span>
    <span class="va_rating__count">
        {{ number_format($count, 0, ',', ' ') }}
        <x-site.plural :count="$count" one="отзыв" few="отзыва" many="отзывов" />
    </span>
</div>
