<?php

namespace App\Livewire;

use App\Enums\DeliveryMethod;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Cart;
use App\Models\DeliveryPoint;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CheckoutPage extends Component
{
    /** Customer contact details. */
    public string $customerName = '';

    public string $customerPhone = '';

    public string $customerEmail = '';

    /** Delivery method value from the DeliveryMethod enum. */
    public string $deliveryMethod = 'courier';

    /** Selected pickup point / postamat id (string comes from the select). */
    public int|string|null $deliveryPointId = null;

    /** Courier address. */
    public string $address = '';

    /** Payment method value from the PaymentMethod enum. */
    public string $paymentMethod = 'online';

    public string $comment = '';

    /** Terms agreement checkbox. */
    public bool $agree = false;

    /**
     * Prefill contacts for authenticated users.
     */
    public function mount(): void
    {
        $user = auth()->user();

        if ($user === null) {
            return;
        }

        $this->customerName = $user->name;
        $this->customerEmail = $user->email;
        $this->customerPhone = $user->phone ?? '';
    }

    /**
     * Switching the delivery method invalidates the previously selected point.
     */
    public function updated(string $property): void
    {
        if ($property === 'deliveryMethod') {
            $this->deliveryPointId = null;
        }
    }

    /**
     * Validate the form, create the order and clear the cart.
     */
    public function placeOrder(): void
    {
        // Normalize values before validation (empty select → null, trim text).
        $this->customerName = trim($this->customerName);
        $this->customerPhone = trim($this->customerPhone);
        $this->customerEmail = trim($this->customerEmail);
        $this->address = trim($this->address);
        $this->comment = trim($this->comment);
        $this->deliveryPointId = filled($this->deliveryPointId) ? (int) $this->deliveryPointId : null;

        $this->validate([
            'customerName' => ['required', 'string', 'max:255'],
            'customerPhone' => ['required', 'string', 'max:32'],
            'customerEmail' => ['required', 'email', 'max:255'],
            'deliveryMethod' => ['required', Rule::enum(DeliveryMethod::class)],
            'deliveryPointId' => [
                'nullable',
                'integer',
                Rule::requiredIf($this->deliveryMethod !== DeliveryMethod::Courier->value),
                Rule::exists('delivery_points', 'id')
                    ->where('is_active', true)
                    ->where('type', $this->deliveryMethod),
            ],
            'address' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf($this->deliveryMethod === DeliveryMethod::Courier->value),
            ],
            'paymentMethod' => ['required', Rule::enum(PaymentMethod::class)],
            'comment' => ['nullable', 'string', 'max:1000'],
            'agree' => ['accepted'],
        ], attributes: [
            'customerName' => 'имя',
            'customerPhone' => 'телефон',
            'customerEmail' => 'e-mail',
            'deliveryMethod' => 'способ доставки',
            'deliveryPointId' => 'пункт выдачи',
            'address' => 'адрес',
            'paymentMethod' => 'способ оплаты',
            'agree' => 'согласие с условиями',
        ]);

        $cart = Cart::current()->load(['items.product', 'promoCode']);

        if ($cart->items->isEmpty()) {
            $this->addError('cart', 'Корзина пуста — добавьте товары перед оформлением.');

            return;
        }

        $deliveryMethod = DeliveryMethod::from($this->deliveryMethod);
        $paymentMethod = PaymentMethod::from($this->paymentMethod);
        $subtotal = $cart->subtotal();
        $discount = $cart->discount();
        $deliveryPrice = $cart->deliveryPriceFor($deliveryMethod);
        $total = max(0, $subtotal - $discount + $deliveryPrice);

        $order = DB::transaction(function () use ($cart, $deliveryMethod, $paymentMethod, $subtotal, $discount, $deliveryPrice, $total): Order {
            // Lock products and make sure the stock is still sufficient.
            foreach ($cart->items as $item) {
                $product = Product::query()->lockForUpdate()->find($item->product_id);

                if ($product === null || $product->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => 'Товара «'.$item->product->name.'» недостаточно на складе.',
                    ]);
                }
            }

            $order = Order::query()->create([
                'user_id' => auth()->id(),
                'status' => OrderStatus::New,
                'customer_name' => $this->customerName,
                'customer_phone' => $this->customerPhone,
                'customer_email' => $this->customerEmail,
                'delivery_method' => $deliveryMethod,
                'delivery_point_id' => $deliveryMethod === DeliveryMethod::Courier ? null : $this->deliveryPointId,
                'address' => $deliveryMethod === DeliveryMethod::Courier ? $this->address : null,
                'payment_method' => $paymentMethod,
                'comment' => $this->comment !== '' ? $this->comment : null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'delivery_price' => $deliveryPrice,
                'total' => $total,
                'promo_code_id' => $cart->promoCode?->id,
                'placed_at' => now(),
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'total' => $item->lineTotal(),
                ]);

                $item->product->decrement('stock', $item->quantity);
                $item->product->increment('sold_count', $item->quantity);
            }

            if ($cart->promoCode !== null) {
                $cart->promoCode->increment('used_count');
            }

            // Order is stored (items are snapshots), so the cart can be emptied.
            $cart->clearItems();

            return $order;
        });

        // Guests may only open the success page of an order they just placed.
        session()->put('placed_order_'.$order->id, true);

        $this->redirect(route('checkout.success', $order), navigate: true);
    }

    public function render(): View
    {
        $cart = Cart::currentCart()?->load(['items.product.shop', 'items.product.mainImage', 'promoCode']);
        $deliveryMethod = DeliveryMethod::tryFrom($this->deliveryMethod) ?? DeliveryMethod::Courier;
        $deliveryPrice = $cart?->deliveryPriceFor($deliveryMethod) ?? 0;
        $total = max(0, ($cart?->subtotal() ?? 0) - ($cart?->discount() ?? 0) + $deliveryPrice);

        $deliveryPoints = DeliveryPoint::query()
            ->active()
            ->when(
                $deliveryMethod !== DeliveryMethod::Courier,
                fn ($query) => $query->where('type', $deliveryMethod),
            )
            ->orderBy('city')
            ->orderBy('address')
            ->get();

        return view('livewire.checkout-page', [
            'cart' => $cart,
            'items' => $cart === null ? collect() : $cart->items,
            'deliveryMethod' => $deliveryMethod,
            'deliveryPrice' => $deliveryPrice,
            'deliveryPoints' => $deliveryPoints,
            'total' => $total,
        ]);
    }
}
