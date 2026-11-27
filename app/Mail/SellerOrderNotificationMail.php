<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SellerOrderNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, OrderItem>  $items
     */
    public function __construct(
        public Order $order,
        public Shop $shop,
        public Collection $items,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новый заказ '.$this->order->number.' — '.$this->shop->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.seller-order-notification',
        );
    }
}
