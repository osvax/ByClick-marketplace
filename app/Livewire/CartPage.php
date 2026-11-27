<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\PromoCode;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class CartPage extends Component
{
    /** Promo code entered by the user. */
    public string $promoCode = '';

    /** Validation message for the promo form. */
    public ?string $promoError = null;

    public function increment(int $itemId): void
    {
        $this->changeQuantity($itemId, 1);
    }

    public function decrement(int $itemId): void
    {
        $this->changeQuantity($itemId, -1);
    }

    public function remove(int $itemId): void
    {
        $cart = Cart::current();
        $item = $cart->items()->find($itemId);

        if ($item === null) {
            return;
        }

        $cart->removeItem($item);
        $this->dispatchCartUpdated($cart);
    }

    /**
     * Remove all products and the applied promo code.
     */
    public function clear(): void
    {
        $cart = Cart::current();
        $cart->clearItems();

        $this->promoError = null;
        $this->dispatchCartUpdated($cart);
        $this->dispatch('notify', message: 'Корзина очищена');
    }

    /**
     * Validate and attach a promo code to the cart.
     */
    public function applyPromo(): void
    {
        $code = Str::upper(trim($this->promoCode));

        if ($code === '') {
            return;
        }

        $cart = Cart::current()->load(['items.product', 'promoCode']);
        $promo = PromoCode::query()->where('code', $code)->first();

        if ($promo === null || ! $promo->isApplicableTo($cart->subtotal())) {
            $this->promoError = 'Промокод не найден или не подходит к заказу';

            return;
        }

        $cart->applyPromoCode($promo);

        $this->promoCode = '';
        $this->promoError = null;

        $this->dispatchCartUpdated($cart);
        $this->dispatch('notify', message: 'Промокод '.$promo->code.' применён');
    }

    public function removePromo(): void
    {
        $cart = Cart::current()->load(['items.product', 'promoCode']);
        $cart->applyPromoCode(null);

        $this->promoError = null;
        $this->dispatchCartUpdated($cart);
    }

    /**
     * Change quantity by a delta and drop the line when it reaches zero.
     */
    protected function changeQuantity(int $itemId, int $delta): void
    {
        $cart = Cart::current();
        $item = $cart->items()->with('product')->find($itemId);

        if ($item === null) {
            return;
        }

        $quantity = $item->quantity + $delta;

        if ($quantity < 1) {
            $cart->removeItem($item);
        } else {
            $cart->setQuantity($item, $quantity);
        }

        $this->dispatchCartUpdated($cart);
    }

    protected function dispatchCartUpdated(Cart $cart): void
    {
        $this->dispatch('cart-updated', count: $cart->load('items')->totalQuantity());
    }

    public function render(): View
    {
        $cart = Cart::currentCart()?->load(['items.product.shop', 'items.product.mainImage', 'promoCode']);

        return view('livewire.cart-page', [
            'cart' => $cart,
            'items' => $cart === null ? collect() : $cart->items,
        ]);
    }
}
