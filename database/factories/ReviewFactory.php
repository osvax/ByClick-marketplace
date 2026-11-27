<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'rating' => fake()->numberBetween(3, 5),
            'title' => fake()->sentence(4),
            'text' => fake()->paragraph(),
            'pros' => fake()->sentence(),
            'cons' => fake()->boolean(50) ? fake()->sentence() : null,
            'helpful_count' => fake()->numberBetween(0, 120),
            'is_published' => true,
        ];
    }

    /**
     * Indicate that the review is hidden from the product page.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}
