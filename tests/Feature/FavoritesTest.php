<?php

use App\Livewire\FavoritesPage;
use App\Livewire\FavoriteSync;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(ProductSeeder::class);
});

it('renders an empty favorites page', function () {
    $this->get('/favorites')
        ->assertOk()
        ->assertSee('В избранном пока пусто');
});

it('adds and removes a guest favorite in the session', function () {
    $this->withSession([]);

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();

    expect(Favorite::toggle($product))->toBeTrue();

    $favorite = Favorite::query()->firstOrFail();

    expect($favorite->user_id)->toBeNull()
        ->and($favorite->session_id)->not->toBeNull()
        ->and(Favorite::countForCurrent())->toBe(1)
        ->and(Favorite::isFavorited($product->id))->toBeTrue();

    expect(Favorite::toggle($product))->toBeFalse()
        ->and(Favorite::countForCurrent())->toBe(0);
});

it('stores favorites of an authenticated user and does not duplicate them', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();

    Favorite::toggle($product);

    $this->assertDatabaseHas('favorites', [
        'product_id' => $product->id,
        'user_id' => $user->id,
    ]);

    expect(Favorite::idsForCurrent())->toBe([$product->id])
        ->and(Favorite::countForCurrent())->toBe(1);

    // Toggling the same product again removes the only row.
    Favorite::toggle($product);

    expect(Favorite::countForCurrent())->toBe(0);
});

it('toggles favorites through the sync component', function () {
    $this->actingAs(User::factory()->create());

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();

    Livewire::test(FavoriteSync::class)
        ->call('toggle', $product->id)
        ->assertDispatched('favorites-updated')
        ->assertDispatched('notify');

    expect(Favorite::countForCurrent())->toBe(1);
});

it('shows guest favorites from the session', function () {
    $this->withSession([]);

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();

    Livewire::test(FavoriteSync::class)->call('toggle', $product->id);

    Livewire::test(FavoritesPage::class)
        ->assertSee($product->name, false);
});

it('shows favorites on the page and clears them', function () {
    $this->actingAs(User::factory()->create());

    $first = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    $second = Product::query()->where('slug', 'krossovki-ritm')->firstOrFail();

    Favorite::toggle($first);
    Favorite::toggle($second);

    Livewire::test(FavoritesPage::class)
        ->assertSee($first->name, false)
        ->assertSee($second->name, false)
        ->assertSee('aria-pressed="true"', false)
        ->call('clear')
        ->assertSee('В избранном пока пусто');

    expect(Favorite::countForCurrent())->toBe(0);
});

it('marks favorited products as active on the product page and in cards', function () {
    $this->actingAs(User::factory()->create());

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    Favorite::toggle($product);

    $this->get('/product/'.$product->slug)
        ->assertOk()
        ->assertSee('data-va-fav="'.$product->id.'"', false)
        ->assertSee('aria-pressed="true"', false);

    $this->get('/catalog')
        ->assertOk()
        ->assertSee('is-active', false);
});
