<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Mail\SellerOrderNotificationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NotifySellersOfOrder implements ShouldQueue
{
    /**
     * Notify every seller whose products are present in the new order.
     */
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->loadMissing(['items.product.shop.user']);

        $itemsByShop = $order->items
            ->filter(fn ($item): bool => $item->product?->shop !== null)
            ->groupBy(fn ($item): int => $item->product->shop_id);

        foreach ($itemsByShop as $items) {
            $shop = $items->first()?->product?->shop;

            if ($shop === null || $shop->user === null) {
                continue;
            }

            Mail::to($shop->user->email)
                ->send(new SellerOrderNotificationMail($order, $shop, $items));
        }
    }
}
