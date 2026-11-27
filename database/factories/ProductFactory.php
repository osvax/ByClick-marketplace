<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = rtrim(fake()->unique()->sentence(3), '.');
        $price = fake()->numberBetween(500, 50000);

        return [
            'shop_id' => Shop::factory(),
            'category_id' => Category::factory(),
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('####'),
            'name' => Str::ucfirst($name),
            'brand' => fake()->company(),
            'description' => fake()->paragraph(),
            'specs' => [
                'Бренд' => fake()->company(),
                'Гарантия' => fake()->randomElement(['12 месяцев', '24 месяца']),
            ],
            'price' => $price,
            'old_price' => fake()->boolean(60) ? null : $price + fake()->numberBetween(100, 5000),
            'stock' => fake()->numberBetween(0, 50),
            'rating' => fake()->randomFloat(1, 3, 5),
            'reviews_count' => fake()->numberBetween(0, 200),
            'sold_count' => fake()->numberBetween(0, 2000),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the product is hidden from the catalog.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
