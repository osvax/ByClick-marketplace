<?php

use App\Enums\SellerRequestStatus;
use App\Livewire\SellerRequestForm;
use App\Models\Category;
use App\Models\SellerRequest;
use App\Models\User;
use Database\Seeders\DeliveryPointSeeder;
use Database\Seeders\ProductSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed([ProductSeeder::class, DeliveryPointSeeder::class]);
});

it('renders the delivery page with pickup points and prices', function () {
    $this->get('/delivery')
        ->assertOk()
        ->assertSee('Способы доставки')
        ->assertSee('Пункт выдачи')
        ->assertSee('Минск, пр-т Независимости, 18')
        ->assertSee('бесплатно от');
});

it('renders the help page with FAQ sections', function () {
    $this->get('/help')
        ->assertOk()
        ->assertSee('Заказ и оплата')
        ->assertSee('Как вернуть товар')
        ->assertSee('Поддержка')
        ->assertSee('Пользовательское соглашение');
});

it('renders the about page', function () {
    $this->get('/about')
        ->assertOk()
        ->assertSee('Маркетплейс, где всё в один клик')
        ->assertSee('Честные цены')
        ->assertSee('Головной офис');
});

it('renders the seller page with the request form', function () {
    $this->get('/seller')
        ->assertOk()
        ->assertSee('Продавайте на ByClick')
        ->assertSee('Заявка на подключение')
        ->assertSee('wire:name="seller-request-form"', false);
});

it('stores a seller request from a guest', function () {
    $category = Category::query()->roots()->firstOrFail();

    Livewire::test(SellerRequestForm::class)
        ->set('name', 'Иван Петров')
        ->set('phone', '+375 29 123-45-67')
        ->set('email', 'seller-request@example.com')
        ->set('categoryId', (string) $category->id)
        ->set('comment', 'Магазин электроники')
        ->set('agree', true)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true)
        ->assertSee('Заявка отправлена');

    $this->assertDatabaseHas('seller_requests', [
        'name' => 'Иван Петров',
        'email' => 'seller-request@example.com',
        'category_id' => $category->id,
        'user_id' => null,
        'status' => SellerRequestStatus::New->value,
    ]);
});

it('prefills the seller request for an authenticated user', function () {
    $user = User::factory()->create([
        'name' => 'Мария Лебедева',
        'email' => 'maria-seller@example.com',
    ]);

    $this->actingAs($user);

    Livewire::test(SellerRequestForm::class)
        ->assertSet('name', 'Мария Лебедева')
        ->assertSet('email', 'maria-seller@example.com');
});

it('validates the seller request form', function () {
    Livewire::test(SellerRequestForm::class)
        ->call('submit')
        ->assertHasErrors(['name', 'phone', 'email', 'categoryId', 'agree']);

    expect(SellerRequest::query()->count())->toBe(0);
});

it('renders the custom 404 page', function () {
    $this->get('/this-page-does-not-exist')
        ->assertNotFound()
        ->assertSee('Страница не найдена')
        ->assertSee('В каталог');
});
