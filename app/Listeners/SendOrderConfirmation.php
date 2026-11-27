<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Mail\OrderConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmation implements ShouldQueue
{
    /**
     * Send the customer a confirmation after the order transaction is committed.
     */
    public function handle(OrderPlaced $event): void
    {
        $event->order->loadMissing('items');

        Mail::to($event->order->customer_email)
            ->send(new OrderConfirmationMail($event->order));
    }
}
