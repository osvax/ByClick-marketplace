<?php

use App\Enums\DeliveryMethod;
use App\Models\DeliveryPoint;
use App\Services\DeliveryCalculator;
use Database\Seeders\DeliveryPointSeeder;

it('calculates courier delivery by city zone', function () {
    $calculator = app(DeliveryCalculator::class);

    expect($calculator->quote(DeliveryMethod::Courier, 'Минск', 5000)['price'])->toBe(500)
        ->and($calculator->quote(DeliveryMethod::Courier, 'Гомель', 5000)['price'])->toBe(700)
        ->and($calculator->quote(DeliveryMethod::Courier, 'Другой город', 5000)['price'])->toBe(900)
        ->and($calculator->quote(DeliveryMethod::Courier, 'Неизвестный город', 5000)['price'])->toBe(900);
});

it('makes courier delivery free above the threshold', function () {
    $calculator = app(DeliveryCalculator::class);

    $quote = $calculator->quote(DeliveryMethod::Courier, 'Гомель', 10000);

    expect($quote['price'])->toBe(0)
        ->and($quote['min_days'])->toBe(1)
        ->and($quote['max_days'])->toBe(2);
});

it('keeps pickup and postamat delivery free', function () {
    $calculator = app(DeliveryCalculator::class);

    $pickup = $calculator->quote(DeliveryMethod::Pickup, null, 5000);
    $postamat = $calculator->quote(DeliveryMethod::Postamat, null, 5000);

    expect($pickup['price'])->toBe(0)
        ->and($pickup['min_days'])->toBe(1)
        ->and($postamat['price'])->toBe(0)
        ->and($postamat['min_days'])->toBe(2);
});

it('formats delivery estimates with Russian plurals', function () {
    $calculator = app(DeliveryCalculator::class);

    expect($calculator->estimateLabel(['price' => 0, 'min_days' => 1, 'max_days' => 1]))->toBe('1 день')
        ->and($calculator->estimateLabel(['price' => 0, 'min_days' => 1, 'max_days' => 2]))->toBe('1–2 дня')
        ->and($calculator->estimateLabel(['price' => 0, 'min_days' => 2, 'max_days' => 4]))->toBe('2–4 дня');
});

it('seeds delivery point coordinates for the map', function () {
    $this->seed(DeliveryPointSeeder::class);

    $point = DeliveryPoint::query()->where('city', 'Минск')->firstOrFail();

    expect($point->latitude)->not->toBeNull()
        ->and($point->longitude)->not->toBeNull()
        ->and($point->latitude)->toBeFloat()
        ->and($point->longitude)->toBeFloat();
});

it('renders the delivery map and courier zones', function () {
    $this->seed(DeliveryPointSeeder::class);

    $this->get('/delivery')
        ->assertOk()
        ->assertSee('data-va-delivery-map', false)
        ->assertSee('Стоимость и сроки курьерской доставки')
        ->assertSee('Гомель')
        ->assertSee('7,00 '.config('marketplace.currency'));
});
