<?php

namespace Database\Factories;

use App\Enums\ShopStatus;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shop>
 */
class ShopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->seller(),
            'name' => fake()->company(),
            'slug' => fake()->unique()->slug(2),
            'description' => fake()->sentence(),
            'logo_path' => null,
            'status' => ShopStatus::Active,
            'rating' => fake()->randomFloat(1, 4, 5),
            'is_verified' => fake()->boolean(70),
        ];
    }

    /**
     * Indicate that the shop is pending moderation.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ShopStatus::Pending,
            'is_verified' => false,
        ]);
    }
}
