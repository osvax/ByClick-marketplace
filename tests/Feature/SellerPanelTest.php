<?php

use App\Livewire\SellerProducts;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

it('renders the seller cabinet for a seller', function () {
    $seller = User::factory()->seller()->create();
    $shop = Shop::factory()->create(['user_id' => $seller->id]);
    $product = Product::factory()->create(['shop_id' => $shop->id]);

    $this->actingAs($seller);

    $this->get('/seller/dashboard')->assertOk()->assertSee($shop->name);
    $this->get('/seller/products')->assertOk()->assertSee($product->name);
    $this->get('/seller/orders')->assertOk();
});

it('blocks customers from the seller cabinet', function () {
    $this->actingAs(User::factory()->create())
        ->get('/seller/dashboard')
        ->assertForbidden();
});

it('only allows a seller to manage products from their own shop', function () {
    $seller = User::factory()->seller()->create();
    $otherSeller = User::factory()->seller()->create();

    $shop = Shop::factory()->create(['user_id' => $seller->id]);
    $otherShop = Shop::factory()->create(['user_id' => $otherSeller->id]);

    $ownProduct = Product::factory()->create(['shop_id' => $shop->id]);
    $otherProduct = Product::factory()->create(['shop_id' => $otherShop->id]);

    expect(Gate::forUser($seller)->allows('update', $ownProduct))->toBeTrue()
        ->and(Gate::forUser($seller)->allows('update', $otherProduct))->toBeFalse()
        ->and(Gate::forUser($otherSeller)->allows('update', $ownProduct))->toBeFalse();

    $admin = User::factory()->admin()->create();

    expect(Gate::forUser($admin)->allows('update', $otherProduct))->toBeTrue();
});

it('creates a product for the seller shop', function () {
    $seller = User::factory()->seller()->create();
    $shop = Shop::factory()->create(['user_id' => $seller->id]);
    $category = Category::factory()->create();

    $this->actingAs($seller);

    Livewire::test(SellerProducts::class)
        ->call('createProduct')
        ->set('newName', 'Новый товар')
        ->set('newPrice', '12.50')
        ->set('newStock', '3')
        ->set('newCategoryId', $category->id)
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('products', [
        'shop_id' => $shop->id,
        'name' => 'Новый товар',
        'price' => 1250,
        'stock' => 3,
    ]);
});

it('updates price and stock of an own product', function () {
    $seller = User::factory()->seller()->create();
    $shop = Shop::factory()->create(['user_id' => $seller->id]);
    $product = Product::factory()->create([
        'shop_id' => $shop->id,
        'price' => 1000,
        'stock' => 1,
    ]);

    $this->actingAs($seller);

    Livewire::test(SellerProducts::class)
        ->call('edit', $product->id)
        ->set('editPrice', '19.90')
        ->set('editStock', '7')
        ->call('save')
        ->assertHasNoErrors();

    expect($product->refresh())
        ->price->toBe(1990)
        ->stock->toBe(7);
});

it('shows only the seller products in seller orders', function () {
    $seller = User::factory()->seller()->create();
    $shop = Shop::factory()->create(['user_id' => $seller->id]);
    $product = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Товар продавца']);

    $otherSeller = User::factory()->seller()->create();
    $otherShop = Shop::factory()->create(['user_id' => $otherSeller->id]);
    $otherProduct = Product::factory()->create(['shop_id' => $otherShop->id, 'name' => 'Чужой товар']);

    $order = Order::factory()->create();

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $otherProduct->id,
        'product_name' => $otherProduct->name,
    ]);

    $this->actingAs($seller);

    $this->get('/seller/orders')
        ->assertOk()
        ->assertSee('Товар продавца')
        ->assertDontSee('Чужой товар');
});
