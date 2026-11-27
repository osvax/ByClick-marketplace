<?php

namespace App\Livewire\Concerns;

use App\Models\Cart;
use App\Models\Order;

/**
 * Shared "repeat order" action for the profile and orders pages.
 */
trait RepeatsOrders
{
    /**
     * Put all still available products of an order back into the cart.
     */
    public function repeatOrder(int $orderId): void
    {
        $order = Order::query()
            ->where('user_id', (int) auth()->id())
            ->with(['items.product'])
            ->findOrFail($orderId);

        $cart = Cart::current();
        $added = false;

        foreach ($order->items as $item) {
            $product = $item->product;

            if ($product === null || ! $product->is_active || $product->stock < 1) {
                continue;
            }

            $cart->add($product, $item->quantity);
            $added = true;
        }

        $this->dispatch('cart-updated', count: $cart->load('items')->totalQuantity());

        if (! $added) {
            $this->dispatch('notify', message: 'Товары из этого заказа сейчас недоступны');

            return;
        }

        $this->dispatch('notify', message: 'Товары из заказа добавлены в корзину');
        $this->redirect(route('cart'), navigate: true);
    }
}
