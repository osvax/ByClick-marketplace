<?php

use App\Enums\OrderStatus;
use App\Livewire\OrdersPage;
use App\Livewire\ProfilePage;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(ProductSeeder::class);
});

/**
 * Create an order with one product line for the given user.
 */
function orderForUser(User $user, Product $product, int $quantity = 1): Order
{
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => OrderStatus::Delivered,
        'subtotal' => 4990 * $quantity,
        'delivery_price' => 0,
        'total' => 4990 * $quantity,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price' => 4990,
        'quantity' => $quantity,
        'total' => 4990 * $quantity,
    ]);

    return $order;
}

it('requires authentication for the profile and orders pages', function () {
    $this->get('/profile')->assertRedirect(route('login'));
    $this->get('/orders')->assertRedirect(route('login'));
});

it('renders the profile with customer data, stats and recent orders', function () {
    $user = User::factory()->create(['name' => 'Иван Петров']);
    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    $order = orderForUser($user, $product);

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk()
        ->assertSee('Иван Петров')
        ->assertSee('Личный кабинет')
        ->assertSee($order->number)
        ->assertSee('Доставлен')
        ->assertSee('va_status--done', false);
});

it('updates the customer profile', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(ProfilePage::class)
        ->set('name', 'Новое Имя')
        ->set('email', 'new-email@example.com')
        ->set('phone', '+375 29 999-99-99')
        ->call('updateProfile')
        ->assertHasNoErrors()
        ->assertDispatched('notify');

    $user->refresh();

    expect($user->name)->toBe('Новое Имя')
        ->and($user->email)->toBe('new-email@example.com')
        ->and($user->phone)->toBe('+375 29 999-99-99');
});

it('does not allow an e-mail of another account', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(ProfilePage::class)
        ->set('email', $other->email)
        ->call('updateProfile')
        ->assertHasErrors('email');
});

it('changes the password after checking the current one', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(ProfilePage::class)
        ->set('currentPassword', 'wrong-password')
        ->set('newPassword', 'new-secret-123')
        ->set('newPasswordConfirmation', 'new-secret-123')
        ->call('updatePassword')
        ->assertHasErrors('currentPassword');

    Livewire::test(ProfilePage::class)
        ->set('currentPassword', 'password')
        ->set('newPassword', 'new-secret-123')
        ->set('newPasswordConfirmation', 'new-secret-123')
        ->call('updatePassword')
        ->assertHasNoErrors()
        ->assertDispatched('notify');

    expect(Hash::check('new-secret-123', $user->fresh()?->password))->toBeTrue();
});

it('shows only orders of the current customer', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();

    $ownOrder = orderForUser($user, $product);
    $otherOrder = orderForUser($other, $product);

    $this->actingAs($user);

    Livewire::test(OrdersPage::class)
        ->assertSee($ownOrder->number)
        ->assertDontSee($otherOrder->number);

    $this->get('/orders')
        ->assertOk()
        ->assertSee($ownOrder->number)
        ->assertDontSee($otherOrder->number);
});

it('repeats an order into the cart', function () {
    $user = User::factory()->create();
    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    $order = orderForUser($user, $product, 2);

    $this->actingAs($user);

    Livewire::test(OrdersPage::class)
        ->call('repeatOrder', $order->id)
        ->assertRedirect(route('cart'));

    $cart = Cart::query()->where('user_id', $user->id)->with('items')->first();

    expect($cart)->not->toBeNull()
        ->and($cart->items->sum('quantity'))->toBe(2);
});
