<?php

use App\Livewire\Catalog;
use App\Models\Product;
use Database\Seeders\ProductSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(ProductSeeder::class);
});

it('renders the catalog with all seeded products', function () {
    $this->get('/catalog')
        ->assertOk()
        ->assertSee('Каталог')
        ->assertSee('Наушники «Аура Pro»', false)
        ->assertSee('По популярности');
});

it('filters products by a root category', function () {
    $this->get('/catalog?category=home')
        ->assertOk()
        ->assertSee('Кружка керамическая «Утро»', false)
        ->assertDontSee('Наушники «Аура Pro»', false);
});

it('filters products by a subcategory', function () {
    $this->get('/catalog?category=electronics&sub=audio')
        ->assertOk()
        ->assertSee('Наушники «Аура Pro»', false)
        ->assertSee('Колонка портативная «Эхо Mini»', false)
        ->assertDontSee('Клавиатура механическая «Клик 87»', false);
});

it('filters products by price and rating', function () {
    $this->get('/catalog?price_min=5000')
        ->assertOk()
        ->assertSee('Клавиатура механическая', false)
        ->assertSee('Часы смарт', false)
        ->assertDontSee('Кружка керамическая', false);

    $this->get('/catalog?rating=4.8')
        ->assertOk()
        ->assertSee('Наушники «Аура Pro»', false)
        ->assertDontSee('Кроссовки «Ритм»', false);
});

it('shows only discounted products for the sale tag', function () {
    Product::factory()->create([
        'name' => 'Товар без скидки',
        'price' => 1000,
        'old_price' => null,
        'sold_count' => 9999,
    ]);

    $this->get('/catalog?tag=sale')
        ->assertOk()
        ->assertSee('Наушники «Аура Pro»', false)
        ->assertDontSee('Товар без скидки', false);
});

it('updates the results when a filter changes', function () {
    Livewire::test(Catalog::class)
        ->set('category', 'home')
        ->assertSee('Кружка керамическая', false)
        ->assertDontSee('Наушники «Аура Pro»', false)
        ->set('category', '')
        ->assertSee('Наушники «Аура Pro»', false);
});

it('sorts products by ascending price', function () {
    $this->get('/catalog?sort=cheap')
        ->assertOk()
        ->assertSeeInOrder(['Кружка керамическая «Утро»', 'Держатель для телефона в авто'], false);
});

it('paginates the catalog', function () {
    $this->get('/catalog?page=2')
        ->assertOk()
        ->assertSee('Свитшот «Оверсайз»', false)
        ->assertDontSee('Кружка керамическая «Утро»', false);
});

it('shows an empty state when nothing matches', function () {
    $this->get('/catalog?q=zzzz-no-such-product')
        ->assertOk()
        ->assertSee('Ничего не нашлось')
        ->assertDontSee('Наушники «Аура Pro»', false);
});
