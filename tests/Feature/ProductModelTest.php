<?php

use App\Models\Category;
use App\Models\Product;

it('filters only active products', function () {
    $active = Product::factory()->create();
    Product::factory()->inactive()->create();

    expect(Product::query()->active()->pluck('id')->all())->toBe([$active->id]);
});

it('filters products by a root category including its children', function () {
    $root = Category::factory()->create();
    $child = Category::factory()->childOf($root)->create();
    $other = Category::factory()->create();

    $inRoot = Product::factory()->create(['category_id' => $child->id]);
    Product::factory()->create(['category_id' => $other->id]);

    expect(Product::query()->inCategory($root)->pluck('id')->all())->toBe([$inRoot->id]);
});

it('filters products by price range', function () {
    Product::factory()->create(['price' => 100]);
    $middle = Product::factory()->create(['price' => 500]);
    Product::factory()->create(['price' => 1000]);

    expect(Product::query()->priceBetween(200, 900)->pluck('id')->all())->toBe([$middle->id]);
});

it('calculates the discount percent', function () {
    $discounted = Product::factory()->create(['price' => 4990, 'old_price' => 7490]);
    $regular = Product::factory()->create(['price' => 4990, 'old_price' => null]);

    expect($discounted->hasDiscount())->toBeTrue()
        ->and($discounted->discountPercent())->toBe(33)
        ->and($regular->hasDiscount())->toBeFalse()
        ->and($regular->discountPercent())->toBe(0);
});
