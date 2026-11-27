<?php

use App\Enums\OrderStatus;
use App\Enums\SellerRequestStatus;
use App\Enums\UserRole;
use App\Livewire\AdminCategories;
use App\Livewire\AdminOrders;
use App\Livewire\AdminProducts;
use App\Livewire\AdminPromoCodes;
use App\Livewire\AdminSellerRequests;
use App\Livewire\AdminShops;
use App\Livewire\AdminUsers;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\PromoCode;
use App\Models\SellerRequest;
use App\Models\Shop;
use App\Models\User;
use Livewire\Livewire;

it('renders every admin section for an administrator', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    foreach ([
        '/admin',
        '/admin/requests',
        '/admin/shops',
        '/admin/orders',
        '/admin/products',
        '/admin/categories',
        '/admin/users',
        '/admin/promocodes',
    ] as $url) {
        $this->get($url)->assertOk();
    }
});

it('blocks non-administrators from the admin panel', function () {
    $this->get('/admin')->assertRedirect('/login');

    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertForbidden();

    $this->actingAs(User::factory()->seller()->create())
        ->get('/admin/products')
        ->assertForbidden();
});

it('approves a seller request by opening a shop and promoting the user', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();
    $category = Category::factory()->create();

    $sellerRequest = SellerRequest::factory()->create([
        'user_id' => $customer->id,
        'category_id' => $category->id,
        'shop_name' => 'ТехноДом',
    ]);

    $this->actingAs($admin);

    Livewire::test(AdminSellerRequests::class)
        ->call('approve', $sellerRequest->id)
        ->assertHasNoErrors();

    expect($customer->refresh()->role)->toBe(UserRole::Seller);
    expect($sellerRequest->refresh()->status)->toBe(SellerRequestStatus::Approved);

    $this->assertDatabaseHas('shops', [
        'user_id' => $customer->id,
        'name' => 'ТехноДом',
        'status' => 'active',
    ]);
});

it('does not approve a guest seller request without a linked user', function () {
    $admin = User::factory()->admin()->create();
    $sellerRequest = SellerRequest::factory()->create([
        'user_id' => null,
        'shop_name' => 'Гостевой магазин',
    ]);

    $this->actingAs($admin);

    Livewire::test(AdminSellerRequests::class)
        ->call('approve', $sellerRequest->id)
        ->assertHasErrors('review-'.$sellerRequest->id);

    expect($sellerRequest->refresh()->status)->toBe(SellerRequestStatus::New);
});

it('rejects a seller request', function () {
    $admin = User::factory()->admin()->create();
    $sellerRequest = SellerRequest::factory()->create();

    $this->actingAs($admin);

    Livewire::test(AdminSellerRequests::class)
        ->call('reject', $sellerRequest->id)
        ->assertHasNoErrors();

    expect($sellerRequest->refresh()->status)->toBe(SellerRequestStatus::Rejected);
});

it('changes user roles and protects the current administrator', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();

    $this->actingAs($admin);

    Livewire::test(AdminUsers::class)
        ->call('updateRole', $customer->id, UserRole::Seller->value)
        ->assertHasNoErrors();

    expect($customer->refresh()->role)->toBe(UserRole::Seller);

    Livewire::test(AdminUsers::class)
        ->call('updateRole', $admin->id, UserRole::Customer->value)
        ->assertHasErrors('role-'.$admin->id);

    expect($admin->refresh()->role)->toBe(UserRole::Admin);
});

it('moderates products and updates price and stock', function () {
    $admin = User::factory()->admin()->create();
    $product = Product::factory()->create(['price' => 1000, 'stock' => 2]);

    $this->actingAs($admin);

    Livewire::test(AdminProducts::class)
        ->call('toggleActive', $product->id)
        ->assertHasNoErrors();

    expect($product->refresh()->is_active)->toBeFalse();

    Livewire::test(AdminProducts::class)
        ->call('edit', $product->id)
        ->set('editPrice', '25.50')
        ->set('editStock', '9')
        ->set('editActive', true)
        ->call('save')
        ->assertHasNoErrors();

    expect($product->refresh())
        ->price->toBe(2550)
        ->stock->toBe(9)
        ->is_active->toBeTrue();
});

it('updates an order status', function () {
    $admin = User::factory()->admin()->create();
    $order = Order::factory()->create();

    $this->actingAs($admin);

    Livewire::test(AdminOrders::class)
        ->call('updateStatus', $order->id, OrderStatus::Shipped->value)
        ->assertHasNoErrors();

    expect($order->refresh()->status)->toBe(OrderStatus::Shipped);
});

it('creates and toggles categories', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire::test(AdminCategories::class)
        ->call('createCategory')
        ->set('newName', 'Новая категория')
        ->set('newSort', '4')
        ->call('store')
        ->assertHasNoErrors();

    $category = Category::query()->where('name', 'Новая категория')->firstOrFail();

    expect($category->slug)->not->toBe('');

    Livewire::test(AdminCategories::class)
        ->call('toggleActive', $category->id)
        ->assertHasNoErrors();

    expect($category->refresh()->is_active)->toBeFalse();
});

it('creates and toggles promo codes', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire::test(AdminPromoCodes::class)
        ->call('createPromoCode')
        ->set('code', 'test10')
        ->set('type', 'percent')
        ->set('value', '10')
        ->set('minTotal', '20.00')
        ->call('store')
        ->assertHasNoErrors();

    $promoCode = PromoCode::query()->where('code', 'TEST10')->firstOrFail();

    expect($promoCode->value)->toBe(10)
        ->and($promoCode->min_total)->toBe(2000);

    Livewire::test(AdminPromoCodes::class)
        ->call('toggleActive', $promoCode->id)
        ->assertHasNoErrors();

    expect($promoCode->refresh()->is_active)->toBeFalse();
});

it('toggles shop status and verification', function () {
    $admin = User::factory()->admin()->create();
    $shop = Shop::factory()->create(['is_verified' => true]);

    $this->actingAs($admin);

    Livewire::test(AdminShops::class)
        ->call('toggleStatus', $shop->id)
        ->call('toggleVerified', $shop->id)
        ->assertHasNoErrors();

    $shop->refresh();

    expect($shop->status->value)->toBe('suspended')
        ->and($shop->is_verified)->toBeFalse();
});
