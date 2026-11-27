<?php

namespace Database\Factories;

use App\Enums\PromoType;
use App\Models\PromoCode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PromoCode>
 */
class PromoCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => Str::upper(fake()->unique()->bothify('PROMO-####')),
            'type' => PromoType::Percent,
            'value' => 10,
            'min_total' => 0,
            'starts_at' => null,
            'ends_at' => null,
            'usage_limit' => null,
            'used_count' => 0,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the promo code is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
