<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CartDrawer extends Component
{
    /** Whether the drawer is currently open (kept in Livewire so morphing doesn't reset it). */
    public bool $open = false;

    /**
     * Add a single product from any "В корзину" button on the page.
     */
    #[On('cart-add')]
    public function add(int $productId, int $quantity = 1): void
    {
        $product = Product::query()->active()->find($productId);

        if ($product === null) {
            return;
        }

        if ($product->stock < 1) {
            $this->dispatch('notify', message: 'Товара нет в наличии');

            return;
        }

        $cart = Cart::current();
        $cart->add($product, $quantity);

        $this->open = true;
        $this->dispatch('cart-updated', count: $cart->load('items')->totalQuantity());
        $this->dispatch('cart-visibility', open: true);
        $this->dispatch('notify', message: 'Товар добавлен в корзину');
    }

    /**
     * Add a whole bundle ("Покупают вместе") in one action.
     *
     * @param  array<int, int|string>  $productIds
     */
    #[On('cart-add-bundle')]
    public function addBundle(array $productIds): void
    {
        $products = Product::query()
            ->active()
            ->whereIn('id', array_map('intval', $productIds))
            ->get();

        if ($products->isEmpty()) {
            return;
        }

        $cart = Cart::current();

        foreach ($products as $product) {
            if ($product->stock > 0) {
                $cart->add($product);
            }
        }

        $this->open = true;
        $this->dispatch('cart-updated', count: $cart->load('items')->totalQuantity());
        $this->dispatch('cart-visibility', open: true);
        $this->dispatch('notify', message: 'Комплект добавлен в корзину');
    }

    public function open(): void
    {
        $this->open = true;
        $this->dispatch('cart-visibility', open: true);
    }

    public function close(): void
    {
        $this->open = false;
        $this->dispatch('cart-visibility', open: false);
    }

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
        $cart = Cart::currentCart()?->load(['items.product.mainImage', 'promoCode']);

        return view('livewire.cart-drawer', [
            'cart' => $cart,
            'items' => $cart === null ? collect() : $cart->items,
        ]);
    }
}
