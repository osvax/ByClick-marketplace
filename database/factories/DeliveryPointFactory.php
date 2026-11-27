<?php

namespace Database\Factories;

use App\Enums\DeliveryMethod;
use App\Models\DeliveryPoint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryPoint>
 */
class DeliveryPointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'city' => fake()->city(),
            'address' => fake()->streetAddress(),
            'schedule' => 'Ежедневно 09:00–21:00',
            'type' => DeliveryMethod::Pickup,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the delivery point is a postamat.
     */
    public function postamat(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => DeliveryMethod::Postamat,
            'schedule' => 'Круглосуточно',
        ]);
    }
}
