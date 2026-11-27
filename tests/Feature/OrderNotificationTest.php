<?php

use App\Listeners\NotifySellersOfOrder;
use App\Listeners\SendOrderConfirmation;
use App\Livewire\CheckoutPage;
use App\Mail\OrderConfirmationMail;
use App\Mail\SellerOrderNotificationMail;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DeliveryPointSeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed([ProductSeeder::class, DeliveryPointSeeder::class]);
});

function placeOrderForNotificationTest(): void
{
    $user = User::factory()->create();
    test()->actingAs($user);

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    Cart::current()->add($product, 1);

    Livewire::test(CheckoutPage::class)
        ->set('customerName', 'Иван Петров')
        ->set('customerPhone', '+375 29 123-45-67')
        ->set('customerEmail', 'ivan@example.com')
        ->set('address', 'Минск, ул. Немига, 5')
        ->set('agree', true)
        ->call('placeOrder')
        ->assertHasNoErrors();
}

it('queues customer and seller notifications after checkout', function () {
    Queue::fake();

    placeOrderForNotificationTest();

    Queue::assertPushed(
        CallQueuedListener::class,
        fn (CallQueuedListener $job): bool => $job->class === SendOrderConfirmation::class,
    );

    Queue::assertPushed(
        CallQueuedListener::class,
        fn (CallQueuedListener $job): bool => $job->class === NotifySellersOfOrder::class,
    );
});

it('sends order confirmation and seller notification emails', function () {
    Mail::fake();

    placeOrderForNotificationTest();

    Mail::assertSent(OrderConfirmationMail::class);
    Mail::assertSent(SellerOrderNotificationMail::class);
});
