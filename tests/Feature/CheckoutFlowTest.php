<?php

use App\Enums\DeliveryMethod;
use App\Livewire\CheckoutPage;
use App\Models\Cart;
use App\Models\DeliveryPoint;
use App\Models\Order;
use App\Models\Product;
use App\Models\PromoCode;
use App\Models\User;
use Database\Seeders\DeliveryPointSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\PromoCodeSeeder;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed([ProductSeeder::class, PromoCodeSeeder::class, DeliveryPointSeeder::class]);
});

/**
 * Fill the contact fields required by the checkout form.
 */
function fillCheckoutContacts(Testable $component): Testable
{
    return $component
        ->set('customerName', 'Иван Петров')
        ->set('customerPhone', '+375 29 123-45-67')
        ->set('customerEmail', 'ivan@example.com');
}

it('renders the checkout page with cart contents', function () {
    $this->actingAs(User::factory()->create());

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    Cart::current()->add($product, 2);

    $this->get('/checkout')
        ->assertOk()
        ->assertSee('Оформление заказа')
        ->assertSee('Наушники «Аура Pro»', false)
        ->assertSee('99,80 BYN');
});

it('validates contacts, address and agreement', function () {
    $this->get('/cart')->assertOk();

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    Cart::current()->add($product, 1);

    Livewire::test(CheckoutPage::class)
        ->call('placeOrder')
        ->assertHasErrors(['customerName', 'customerPhone', 'customerEmail', 'address', 'agree']);
});

it('places a courier order, decrements stock and clears the cart', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    Cart::current()->add($product, 2);

    $component = fillCheckoutContacts(Livewire::test(CheckoutPage::class))
        ->set('address', 'Минск, пр-т Независимости, 1')
        ->set('agree', true)
        ->call('placeOrder')
        ->assertHasNoErrors();

    $order = Order::query()->firstOrFail();

    $component->assertRedirect(route('checkout.success', $order));

    expect($order->user_id)->toBe($user->id)
        ->and($order->subtotal)->toBe(9980)
        ->and($order->discount)->toBe(0)
        ->and($order->delivery_price)->toBe(500)
        ->and($order->total)->toBe(10480)
        ->and($order->items()->count())->toBe(1);

    $item = $order->items()->firstOrFail();

    expect($item->product_name)->toBe($product->name)
        ->and($item->price)->toBe(4990)
        ->and($item->quantity)->toBe(2)
        ->and($item->total)->toBe(9980)
        ->and($product->fresh()?->stock)->toBe(16)
        ->and(Cart::current()->items()->count())->toBe(0);
});

it('places a pickup order for free', function () {
    $this->actingAs(User::factory()->create());

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    Cart::current()->add($product, 1);

    $point = DeliveryPoint::query()->where('type', DeliveryMethod::Pickup)->firstOrFail();

    fillCheckoutContacts(Livewire::test(CheckoutPage::class))
        ->set('deliveryMethod', 'pickup')
        ->set('deliveryPointId', (string) $point->id)
        ->set('agree', true)
        ->call('placeOrder')
        ->assertHasNoErrors();

    $order = Order::query()->firstOrFail();

    expect($order->delivery_method)->toBe(DeliveryMethod::Pickup)
        ->and($order->delivery_point_id)->toBe($point->id)
        ->and($order->address)->toBeNull()
        ->and($order->delivery_price)->toBe(0)
        ->and($order->total)->toBe(4990);
});

it('requires a delivery point for pickup', function () {
    $this->actingAs(User::factory()->create());

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    Cart::current()->add($product, 1);

    fillCheckoutContacts(Livewire::test(CheckoutPage::class))
        ->set('deliveryMethod', 'pickup')
        ->set('agree', true)
        ->call('placeOrder')
        ->assertHasErrors(['deliveryPointId']);
});

it('applies the promo code to the order and counts its usage', function () {
    $this->actingAs(User::factory()->create());

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    $cart = Cart::current()->load(['items.product', 'promoCode']);
    $cart->add($product, 1);

    $promo = PromoCode::query()->where('code', 'BYCLICK10')->firstOrFail();
    $cart->load('items.product')->applyPromoCode($promo);

    fillCheckoutContacts(Livewire::test(CheckoutPage::class))
        ->set('address', 'Минск, ул. Немига, 5')
        ->set('agree', true)
        ->call('placeOrder')
        ->assertHasNoErrors();

    $order = Order::query()->firstOrFail();

    expect($order->promo_code_id)->toBe($promo->id)
        ->and($order->discount)->toBe(499)
        ->and($order->total)->toBe(4991)
        ->and($promo->fresh()?->used_count)->toBe(1);
});

it('places a guest order without a user', function () {
    $this->get('/cart')->assertOk();

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    Cart::current()->add($product, 1);

    fillCheckoutContacts(Livewire::test(CheckoutPage::class))
        ->set('address', 'Гомель, ул. Советская, 34')
        ->set('agree', true)
        ->call('placeOrder')
        ->assertHasNoErrors();

    expect(Order::query()->firstOrFail()->user_id)->toBeNull();
});

it('refuses to place an order when the stock is insufficient', function () {
    $this->actingAs(User::factory()->create());

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    Cart::current()->add($product, 2);

    $product->update(['stock' => 1]);

    fillCheckoutContacts(Livewire::test(CheckoutPage::class))
        ->set('address', 'Минск, ул. Немига, 5')
        ->set('agree', true)
        ->call('placeOrder')
        ->assertHasErrors(['cart']);

    expect(Order::query()->count())->toBe(0)
        ->and(Cart::current()->items()->count())->toBe(1);
});

it('protects the success page of an order', function () {
    $order = Order::factory()->create(['user_id' => null]);

    $this->get(route('checkout.success', $order))->assertNotFound();

    $this->withSession(['placed_order_'.$order->id => true])
        ->get(route('checkout.success', $order))
        ->assertOk()
        ->assertSee($order->number);
});
