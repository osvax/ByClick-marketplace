<?php

namespace Database\Factories;

use App\Enums\SellerRequestStatus;
use App\Models\Category;
use App\Models\SellerRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SellerRequest>
 */
class SellerRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'phone' => fake()->numerify('+375 (##) ###-##-##'),
            'email' => fake()->unique()->safeEmail(),
            'category_id' => Category::factory(),
            'comment' => fake()->sentence(),
            'status' => SellerRequestStatus::New,
        ];
    }
}
