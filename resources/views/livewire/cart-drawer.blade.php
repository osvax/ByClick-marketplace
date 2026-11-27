@php
    $scale = (int) config('marketplace.currency_scale', 100);
    $currency = config('marketplace.currency', 'BYN');
    $money = fn (int $amount): string => number_format($amount / $scale, 2, ',', ' ').' '.$currency;

    $count = $cart?->totalQuantity() ?? 0;
    $subtotal = $cart?->subtotal() ?? 0;
    $delivery = $cart?->deliveryPrice() ?? 0;
    $total = $cart?->total() ?? 0;
@endphp

<div class="va_drawer @if ($open) is-open @endif" data-va-drawer aria-hidden="{{ $open ? 'false' : 'true' }}">
    <div class="va_drawer__overlay" wire:click="close"></div>

    <aside class="va_drawer__panel" role="dialog" aria-modal="true" aria-labelledby="va-cart-title">
        <header class="va_drawer__head">
            <h2 class="va_drawer__title" id="va-cart-title">
                Корзина <span class="va_drawer__count" data-va-cart-count-text>{{ $count }}</span>
            </h2>
            <button class="va_icon-btn" type="button" aria-label="Закрыть корзину" wire:click="close">
                <svg class="va_icon" aria-hidden="true"><use href="#va-i-close" /></svg>
            </button>
        </header>

        <div class="va_drawer__body" data-va-cart-body>
            @if ($items->isEmpty())
                <div class="va_cart-empty" data-va-cart-empty>
                    <span class="va_cart-empty__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-cart" /></svg></span>
                    <p class="va_cart-empty__title">Пока пусто</p>
                    <p class="va_cart-empty__text">Добавьте товары из подборок — они появятся здесь.</p>
                    <button class="va_btn va_btn--primary" type="button" wire:click="close">Перейти к покупкам</button>
                </div>
            @else
                <ul class="va_cart-list" data-va-cart-list>
                    @foreach ($items as $item)
                        <li class="va_cart-item" wire:key="drawer-item-{{ $item->id }}">
                            <img
                                class="va_cart-item__img"
                                src="{{ $item->product->mainImage?->url() ?? asset('images/products/placeholder.svg') }}"
                                alt="{{ $item->product->name }}"
                                width="64"
                                height="64"
                                loading="lazy"
                            />

                            <div class="va_cart-item__main">
                                <p class="va_cart-item__title">
                                    <a href="{{ route('product.show', $item->product) }}">{{ $item->product->name }}</a>
                                </p>

                                <p class="va_cart-item__prices">
                                    <span class="va_cart-item__price">{{ $money($item->lineTotal()) }}</span>
                                    @if ($item->product->hasDiscount())
                                        <span class="va_price va_price--old">{{ $money($item->product->old_price) }}</span>
                                    @endif
                                </p>

                                <div class="va_cart-item__controls">
                                    <div class="va_qty">
                                        <button class="va_qty__btn" type="button" aria-label="Уменьшить количество" wire:click="decrement({{ $item->id }})" @disabled($item->quantity <= 1)>
                                            <svg class="va_icon" aria-hidden="true"><use href="#va-i-minus" /></svg>
                                        </button>
                                        <span class="va_qty__value">{{ $item->quantity }}</span>
                                        <button class="va_qty__btn" type="button" aria-label="Увеличить количество" wire:click="increment({{ $item->id }})" @disabled($item->quantity >= 99)>
                                            <svg class="va_icon" aria-hidden="true"><use href="#va-i-plus" /></svg>
                                        </button>
                                    </div>

                                    <button class="va_cart-item__remove" type="button" aria-label="Удалить «{{ $item->product->name }}» из корзины" wire:click="remove({{ $item->id }})">
                                        <svg class="va_icon" aria-hidden="true"><use href="#va-i-trash" /></svg>
                                    </button>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <footer class="va_drawer__foot" @if ($items->isEmpty()) hidden @endif>
            <div class="va_drawer__row">
                <span>Товары</span>
                <span data-va-cart-subtotal>{{ $money($subtotal) }}</span>
            </div>
            <div class="va_drawer__row">
                <span>Доставка</span>
                <span class="va_drawer__free">{{ $delivery > 0 ? $money($delivery) : 'бесплатно' }}</span>
            </div>
            <div class="va_drawer__row va_drawer__row--total">
                <span>Итого</span>
                <span data-va-cart-total>{{ $money($total) }}</span>
            </div>
            <a class="va_btn va_btn--primary va_btn--block" href="{{ route('cart') }}">Перейти в корзину</a>
        </footer>
    </aside>
</div>
