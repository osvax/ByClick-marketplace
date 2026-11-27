<?php

use App\Models\Product;
use App\Models\Question;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\DemoContentSeeder;

beforeEach(function () {
    $this->seed(DemoContentSeeder::class);
});

it('renders a product page with price, specs and seller', function () {
    $this->get('/product/naushniki-aura-pro')
        ->assertOk()
        ->assertSee('Наушники «Аура Pro», беспроводные', false)
        ->assertSee('49,90 '.config('marketplace.currency'))
        ->assertSee('74,90 '.config('marketplace.currency'))
        ->assertSee('ByClick Store')
        ->assertSee('104-230')
        ->assertSee('Характеристики');
});

it('shows reviews and questions from the database', function () {
    $this->get('/product/naushniki-aura-pro')
        ->assertOk()
        ->assertSee('Отличный звук за свои деньги')
        ->assertSee('Плюсы:')
        ->assertSee('Подключаются ли наушники к телевизору?')
        ->assertSee('Ответ продавца:')
        ->assertSee('Рекомендуем');
});

it('hides unpublished reviews and questions', function () {
    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();

    Review::factory()->create([
        'product_id' => $product->id,
        'user_id' => User::factory()->create()->id,
        'text' => 'Скрытый отзыв покупателя',
        'is_published' => false,
    ]);

    Question::factory()->create([
        'product_id' => $product->id,
        'user_id' => User::factory()->create()->id,
        'text' => 'Скрытый вопрос покупателя?',
        'is_published' => false,
    ]);

    $this->get('/product/naushniki-aura-pro')
        ->assertOk()
        ->assertDontSee('Скрытый отзыв покупателя')
        ->assertDontSee('Скрытый вопрос покупателя');
});

it('shows an empty state when a product has no reviews', function () {
    $this->get('/product/kruzhka-utro')
        ->assertOk()
        ->assertSee('Отзывов пока нет');
});

it('returns 404 for unknown and inactive products', function () {
    $this->get('/product/unknown-product')->assertNotFound();

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    $product->update(['is_active' => false]);

    $this->get('/product/naushniki-aura-pro')->assertNotFound();
});

it('shows related products and a bundle', function () {
    $this->get('/product/naushniki-aura-pro')
        ->assertOk()
        ->assertSee('Покупают вместе')
        ->assertSee('Колонка портативная «Эхо Mini»', false)
        ->assertSee('Этот товар');
});
