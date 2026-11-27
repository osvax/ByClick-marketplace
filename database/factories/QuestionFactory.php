<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
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
            'text' => fake()->sentence(8).'?',
            'answer' => null,
            'answered_at' => null,
            'is_published' => true,
        ];
    }

    /**
     * Indicate that the question has been answered by the seller.
     */
    public function answered(): static
    {
        return $this->state(fn (array $attributes) => [
            'answer' => fake()->paragraph(),
            'answered_at' => now(),
        ]);
    }
}
