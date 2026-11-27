<?php

use Database\Seeders\ProductSeeder;

it('renders the marketplace home page with seeded products', function () {
    $this->seed(ProductSeeder::class);

    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('data-va-slider', false)
        ->assertSee('Звук, который держит ритм')
        ->assertSee('Категории')
        ->assertSee('Акции и скидки')
        ->assertSee('Хиты недели')
        ->assertSee('Рекомендуем для вас')
        ->assertSee('Распродажа выходного дня')
        ->assertSee('Наушники «Аура Pro»', false)
        ->assertSee('49,90 '.config('marketplace.currency'))
        ->assertSee('Зоотовары');
});
