<?php

namespace Database\Factories;

use App\Enums\DeliveryMethod;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(1000, 50000);
        $deliveryPrice = fake()->randomElement([0, 500]);

        return [
            'user_id' => User::factory(),
            'number' => 'BC-'.fake()->unique()->numerify('######-####'),
            'status' => OrderStatus::New,
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->numerify('+375 (##) ###-##-##'),
            'customer_email' => fake()->unique()->safeEmail(),
            'delivery_method' => DeliveryMethod::Courier,
            'delivery_point_id' => null,
            'address' => fake()->address(),
            'delivery_city' => fake()->randomElement(['Минск', 'Гомель', 'Брест', 'Витебск', 'Гродно', 'Могилёв']),
            'payment_method' => PaymentMethod::Online,
            'comment' => null,
            'subtotal' => $subtotal,
            'discount' => 0,
            'delivery_price' => $deliveryPrice,
            'total' => $subtotal + $deliveryPrice,
            'promo_code_id' => null,
            'placed_at' => now(),
        ];
    }

    /**
     * Indicate that the order is delivered.
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Delivered,
        ]);
    }
}
