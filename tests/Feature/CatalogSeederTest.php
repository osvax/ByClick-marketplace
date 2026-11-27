<?php

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;

it('seeds the catalog tree', function () {
    $this->seed(CategorySeeder::class);

    expect(Category::query()->roots()->count())->toBe(8)
        ->and(Category::query()->whereNotNull('parent_id')->count())->toBe(35);

    $electronics = Category::query()
        ->roots()
        ->where('slug', 'electronics')
        ->firstOrFail();

    expect($electronics->children)->toHaveCount(6)
        ->and($electronics->children->pluck('slug')->all())->toContain('audio', 'gadgets', 'keyboards');
});

it('seeds products linked to an active shop and a child category', function () {
    $this->seed(ProductSeeder::class);

    expect(Product::query()->count())->toBe(16);

    Product::query()
        ->with(['shop', 'category.parent', 'mainImage'])
        ->get()
        ->each(function (Product $product) {
            expect($product->is_active)->toBeTrue()
                ->and($product->shop)->not->toBeNull()
                ->and($product->category)->not->toBeNull()
                ->and($product->category->parent)->not->toBeNull()
                ->and($product->mainImage)->not->toBeNull();
        });
});

it('is idempotent when seeded twice', function () {
    $this->seed(CategorySeeder::class);
    $this->seed(CategorySeeder::class);

    expect(Category::query()->count())->toBe(43);
});
