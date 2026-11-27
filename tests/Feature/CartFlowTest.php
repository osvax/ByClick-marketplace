<?php

use App\Enums\PromoType;
use App\Livewire\CartDrawer;
use App\Livewire\CartPage;
use App\Models\Cart;
use App\Models\Product;
use App\Models\PromoCode;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Database\Seeders\PromoCodeSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed([ProductSeeder::class, PromoCodeSeeder::class]);
});

it('renders an empty cart page', function () {
    $this->get('/cart')
        ->assertOk()
        ->assertSee('Корзина пуста');
});

it('adds a product through the cart drawer component', function () {
    $this->actingAs(User::factory()->create());

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();

    Livewire::test(CartDrawer::class)
        ->call('add', $product->id, 2)
        ->assertSet('open', true)
        ->assertSee('Наушники «Аура Pro»', false);

    expect(Cart::current()->items()->first()?->quantity)->toBe(2);
});

it('changes quantity and removes a drawer item', function () {
    $this->actingAs(User::factory()->create());

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    $cart = Cart::current();
    $cart->add($product, 1);
    $item = $cart->items()->firstOrFail();

    Livewire::test(CartDrawer::class)->call('increment', $item->id)->assertSee('2');
    expect($item->fresh()?->quantity)->toBe(2);

    Livewire::test(CartDrawer::class)->call('remove', $item->id);
    expect($cart->items()->count())->toBe(0);
});

it('applies a percent promo code on the cart page', function () {
    $this->actingAs(User::factory()->create());

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    Cart::current()->add($product, 1);

    Livewire::test(CartPage::class)
        ->set('promoCode', 'byclick10')
        ->call('applyPromo')
        ->assertSet('promoError', null)
        ->assertSee('BYCLICK10');

    $cart = Cart::current()?->fresh();

    expect($cart?->promo_code_id)->not->toBeNull()
        ->and($cart?->discount())->toBe(499);
});

it('rejects unknown promo codes', function () {
    $this->actingAs(User::factory()->create());

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    Cart::current()->add($product, 1);

    Livewire::test(CartPage::class)
        ->set('promoCode', 'NOPE')
        ->call('applyPromo')
        ->assertSee('Промокод не найден');
});

it('shows cart items and totals on the page', function () {
    $this->actingAs(User::factory()->create());

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    Cart::current()->add($product, 2);

    $this->get('/cart')
        ->assertOk()
        ->assertSee('Наушники «Аура Pro»', false)
        ->assertSee('99,80 BYN')
        ->assertSee('Оформить заказ');
});

it('clears the cart and its promo code', function () {
    $this->actingAs(User::factory()->create());

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    $cart = Cart::current();
    $cart->add($product, 1);
    $cart->applyPromoCode(PromoCode::factory()->create(['type' => PromoType::Percent, 'value' => 10]));

    Livewire::test(CartPage::class)
        ->call('clear')
        ->assertSee('Корзина пуста');

    expect($cart->items()->count())->toBe(0)
        ->and($cart->fresh()?->promo_code_id)->toBeNull();
});
