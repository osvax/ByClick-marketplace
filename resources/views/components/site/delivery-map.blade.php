@props([
    'points',
    'selectedId' => null,
    'selectSelector' => null,
    'id' => 'va-delivery-map',
])

@php
    $mapPoints = $points
        ->filter(fn ($point): bool => $point->latitude !== null && $point->longitude !== null)
        ->map(fn ($point): array => [
            'id' => $point->id,
            'city' => $point->city,
            'address' => $point->address,
            'schedule' => $point->schedule,
            'type' => $point->type->label(),
            'lat' => (float) $point->latitude,
            'lng' => (float) $point->longitude,
        ])
        ->values();

    $payload = json_encode(
        $mapPoints,
        JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT,
    ) ?: '[]';
@endphp

<div {{ $attributes->class(['va_map']) }}>
    <div
        class="va_map__canvas"
        id="{{ $id }}"
        data-va-delivery-map
        data-points="{{ $payload }}"
        data-selected-id="{{ $selectedId ?? '' }}"
        @if ($selectSelector) data-select-selector="{{ $selectSelector }}" @endif
        role="region"
        aria-label="Карта пунктов выдачи"
    >
        <p class="va_map__status" data-va-map-status>Загрузка карты…</p>
    </div>

    @if ($mapPoints->isEmpty())
        <p class="va_map__fallback">
            Для пунктов выдачи пока не заданы координаты. Список пунктов доступен ниже.
        </p>
    @endif
</div>
