<?php

namespace Database\Seeders;

use App\Enums\DeliveryMethod;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoOrderSeeder extends Seeder
{
    /**
     * Demo orders for the customer account, so the profile and orders pages
     * are not empty in the local demo.
     */
    public function run(): void
    {
        $customer = User::query()->where('email', 'customer@byclick.by')->first();

        if ($customer === null) {
            return;
        }

        $orders = [
            [
                'number' => 'BC-DEMO-0001',
                'status' => OrderStatus::Delivered,
                'placed_at' => now()->subDays(7),
                'payment_method' => PaymentMethod::Online,
                'items' => [
                    ['slug' => 'naushniki-aura-pro', 'quantity' => 1],
                    ['slug' => 'kruzhka-utro', 'quantity' => 2],
                ],
            ],
            [
                'number' => 'BC-DEMO-0002',
                'status' => OrderStatus::Shipped,
                'placed_at' => now()->subDays(2),
                'payment_method' => PaymentMethod::Card,
                'items' => [
                    ['slug' => 'klaviatura-klik-87', 'quantity' => 1],
                    ['slug' => 'termos-dorozhny', 'quantity' => 1],
                ],
            ],
        ];

        foreach ($orders as $data) {
            $items = collect($data['items'])
                ->map(fn (array $item): array => [
                    'product' => Product::query()->where('slug', $item['slug'])->first(),
                    'quantity' => $item['quantity'],
                ])
                ->filter(fn (array $item): bool => $item['product'] !== null);

            if ($items->isEmpty()) {
                continue;
            }

            $subtotal = $items->sum(
                fn (array $item): int => $item['product']->price * $item['quantity'],
            );

            $order = Order::query()->updateOrCreate(
                ['number' => $data['number']],
                [
                    'user_id' => $customer->id,
                    'status' => $data['status'],
                    'customer_name' => $customer->name,
                    'customer_phone' => $customer->phone ?? '+375 (29) 000-00-03',
                    'customer_email' => $customer->email,
                    'delivery_method' => DeliveryMethod::Courier,
                    'delivery_point_id' => null,
                    'address' => 'Минск, пр-т Независимости, 18',
                    'payment_method' => $data['payment_method'],
                    'comment' => null,
                    'subtotal' => $subtotal,
                    'discount' => 0,
                    'delivery_price' => 0,
                    'total' => $subtotal,
                    'promo_code_id' => null,
                    'placed_at' => $data['placed_at'],
                ],
            );

            foreach ($items as $item) {
                $product = $item['product'];

                OrderItem::query()->updateOrCreate(
                    ['order_id' => $order->id, 'product_id' => $product->id],
                    [
                        'product_name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $item['quantity'],
                        'total' => $product->price * $item['quantity'],
                    ],
                );
            }
        }
    }
}
