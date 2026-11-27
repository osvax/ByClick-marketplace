<?php

namespace App\Services;

use App\Enums\DeliveryMethod;

class DeliveryCalculator
{
    /**
     * Calculate a delivery quote for the cart contents.
     *
     * @return array{price: int, min_days: int, max_days: int}
     */
    public function quote(
        DeliveryMethod $method,
        ?string $city,
        int $subtotal,
        int $discount = 0,
    ): array {
        $days = $this->daysFor($method);

        // Pickup points and postamats are free. An empty cart has no delivery
        // cost either — this keeps cart/checkout totals consistent.
        if ($method !== DeliveryMethod::Courier || $subtotal <= 0) {
            return ['price' => 0, ...$days];
        }

        $freeFrom = (int) config('marketplace.delivery.free_from', 10000);
        $price = max(0, $subtotal - $discount) >= $freeFrom
            ? 0
            : $this->courierPriceForCity($city);

        return ['price' => $price, ...$days];
    }

    /**
     * Courier price for a city before the free-delivery threshold.
     */
    public function courierPriceForCity(?string $city): int
    {
        $zone = $this->zone($this->normalizeCity($city));
        $basePrice = (int) config('marketplace.delivery.courier_price', 500);

        return max(0, $basePrice + (int) $zone['surcharge']);
    }

    /**
     * Available courier zones, keyed by city.
     *
     * @return array<string, array{surcharge: int, min_days: int, max_days: int}>
     */
    public function zones(): array
    {
        /** @var array<string, array{surcharge: int, min_days: int, max_days: int}> $zones */
        $zones = config('marketplace.delivery.zones', []);

        return $zones;
    }

    /**
     * City names for the checkout select.
     *
     * @return list<string>
     */
    public function cityOptions(): array
    {
        return array_keys($this->zones());
    }

    /**
     * Human-readable delivery estimate, for example "1–2 дня".
     *
     * @param  array{price: int, min_days: int, max_days: int}  $quote
     */
    public function estimateLabel(array $quote): string
    {
        $min = $quote['min_days'];
        $max = $quote['max_days'];

        if ($min === $max) {
            return $min.' '.$this->pluralDays($min);
        }

        return $min.'–'.$max.' '.$this->pluralDays($max);
    }

    /**
     * Normalize a user-entered city to a known delivery zone.
     */
    public function normalizeCity(?string $city): string
    {
        $defaultCity = (string) config('marketplace.delivery.default_city', 'Минск');
        $city = trim((string) $city);

        if ($city === '') {
            return $defaultCity;
        }

        foreach (array_keys($this->zones()) as $zoneCity) {
            if (mb_strtolower($zoneCity) === mb_strtolower($city)) {
                return $zoneCity;
            }
        }

        return (string) config('marketplace.delivery.default_zone', 'Другой город');
    }

    /**
     * @return array{surcharge: int, min_days: int, max_days: int}
     */
    protected function zone(string $city): array
    {
        $zones = $this->zones();
        $defaultZone = (string) config('marketplace.delivery.default_zone', 'Другой город');

        return $zones[$city] ?? $zones[$defaultZone] ?? [
            'surcharge' => 0,
            'min_days' => 2,
            'max_days' => 4,
        ];
    }

    /**
     * @return array{min_days: int, max_days: int}
     */
    protected function daysFor(DeliveryMethod $method): array
    {
        /** @var array{min: int, max: int} $days */
        $days = config(
            'marketplace.delivery.'.$method->value.'_days',
            ['min' => 1, 'max' => 2],
        );

        return ['min_days' => (int) $days['min'], 'max_days' => (int) $days['max']];
    }

    /**
     * Russian plural for the word "день".
     */
    protected function pluralDays(int $days): string
    {
        if ($days % 10 === 1 && $days % 100 !== 11) {
            return 'день';
        }

        if (in_array($days % 10, [2, 3, 4], true) && ! in_array($days % 100, [12, 13, 14], true)) {
            return 'дня';
        }

        return 'дней';
    }
}
