<?php

use App\Livewire\AuthPage;
use App\Livewire\CheckoutPage;
use App\Livewire\SearchBox;
use App\Models\User;
use Database\Seeders\DeliveryPointSeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed([ProductSeeder::class, DeliveryPointSeeder::class]);
});

it('rate limits repeated login attempts', function () {
    RateLimiter::clear('login:test@example.com|127.0.0.1');

    $component = Livewire::test(AuthPage::class)
        ->set('email', 'test@example.com')
        ->set('password', 'wrong-password');

    for ($attempt = 0; $attempt < 5; $attempt++) {
        $component->call('login')->assertHasErrors(['email']);
    }

    $component->call('login')->assertHasErrors(['email']);
});

it('rate limits repeated checkout attempts', function () {
    $this->actingAs(User::factory()->create());

    $component = Livewire::test(CheckoutPage::class)
        ->set('customerName', 'Иван Петров')
        ->set('customerPhone', '+375 29 123-45-67')
        ->set('customerEmail', 'ivan@example.com')
        ->set('address', 'Минск, ул. Немига, 5')
        ->set('agree', true);

    for ($attempt = 0; $attempt < 10; $attempt++) {
        $component->call('placeOrder')->assertHasErrors(['cart']);
    }

    $component->call('placeOrder')->assertHasErrors(['cart']);
});

it('rate limits live search requests', function () {
    RateLimiter::clear('search:127.0.0.1');

    $component = Livewire::test(SearchBox::class)->set('query', 'наушники');

    for ($attempt = 0; $attempt < 60; $attempt++) {
        $component->set('query', 'наушники '.$attempt);
    }

    $component->set('query', 'наушники final')
        ->assertSet('throttled', true)
        ->assertSet('open', false);
});
