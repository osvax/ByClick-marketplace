<?php

use App\Enums\OrderStatus;
use App\Models\Order;

it('generates a unique public number when an order is created', function () {
    $first = Order::factory()->create(['number' => null]);
    $second = Order::factory()->create(['number' => null]);

    expect($first->number)->toStartWith('BC-')
        ->and($second->number)->toStartWith('BC-')
        ->and($second->number)->not->toBe($first->number);
});

it('casts order status to an enum', function () {
    $order = Order::factory()->create();

    expect($order->status)->toBe(OrderStatus::New)
        ->and(Order::query()->withStatus(OrderStatus::New)->count())->toBe(1);
});
