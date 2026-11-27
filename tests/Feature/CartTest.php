<?php

use App\Enums\PromoType;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\PromoCode;

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

it('clamps added quantity to the available stock', function () {
    $cart = Cart::factory()->create();
    $product = Product::factory()->create(['stock' => 2]);

    $cart->add($product, 5);

    expect($cart->items()->first()?->quantity)->toBe(2);
});

it('does not add a product that is out of stock', function () {
    $cart = Cart::factory()->create();
    $product = Product::factory()->create(['stock' => 0]);

    $cart->add($product, 1);

    expect($cart->items()->count())->toBe(0);
});

it('calculates subtotal, delivery and total with old prices', function () {
    config([
        'marketplace.delivery.free_from' => 10000,
        'marketplace.delivery.courier_price' => 500,
    ]);

    $cart = Cart::factory()->create();
    $product = Product::factory()->create(['price' => 1000, 'old_price' => 1500]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);

    $cart->load('items.product');

    expect($cart->subtotal())->toBe(3000)
        ->and($cart->oldSubtotal())->toBe(4500)
        ->and($cart->discount())->toBe(0)
        ->and($cart->deliveryPrice())->toBe(500)
        ->and($cart->total())->toBe(3500)
        ->and($cart->savings())->toBe(1000);
});

it('gives free delivery above the threshold', function () {
    config([
        'marketplace.delivery.free_from' => 10000,
        'marketplace.delivery.courier_price' => 500,
    ]);

    $cart = Cart::factory()->create();
    $product = Product::factory()->create(['price' => 10000]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $cart->load('items.product');

    expect($cart->deliveryPrice())->toBe(0)
        ->and($cart->total())->toBe(10000);
});

it('applies percent and fixed promo codes', function () {
    $cart = Cart::factory()->create();
    $product = Product::factory()->create(['price' => 1000]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $cart->load('items.product');

    $percent = PromoCode::factory()->create([
        'type' => PromoType::Percent,
        'value' => 10,
        'min_total' => 0,
    ]);

    expect($percent->discountFor(2000))->toBe(200);

    $fixed = PromoCode::factory()->create([
        'type' => PromoType::Fixed,
        'value' => 500,
        'min_total' => 0,
    ]);

    expect($fixed->discountFor(2000))->toBe(500);

    $tooSmall = PromoCode::factory()->create([
        'type' => PromoType::Percent,
        'value' => 10,
        'min_total' => 5000,
    ]);

    expect($tooSmall->discountFor(2000))->toBe(0);
});
