<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

it('calculates line and cart totals in kopecks', function () {
    $product = Product::factory()->create(['price' => 1990]);
    $cart = Cart::factory()->create();

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);

    $cart->load('items.product');

    expect($cart->items->first()->lineTotal())->toBe(5970)
        ->and($cart->subtotal())->toBe(5970)
        ->and($cart->totalQuantity())->toBe(3);
});

it('sums several cart items', function () {
    $cart = Cart::factory()->create();

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => Product::factory()->create(['price' => 1000])->id,
        'quantity' => 2,
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => Product::factory()->create(['price' => 500])->id,
        'quantity' => 1,
    ]);

    $cart->load('items.product');

    expect($cart->subtotal())->toBe(2500)
        ->and($cart->totalQuantity())->toBe(3);
});
