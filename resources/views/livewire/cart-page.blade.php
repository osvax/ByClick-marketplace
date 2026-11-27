@php
    $scale = (int) config('marketplace.currency_scale', 100);
    $currency = config('marketplace.currency', 'BYN');
    $money = fn (int $amount): string => number_format($amount / $scale, 2, ',', ' ').' '.$currency;

    $count = $cart?->totalQuantity() ?? 0;
    $subtotal = $cart?->subtotal() ?? 0;
    $discount = $cart?->discount() ?? 0;
    $delivery = $cart?->deliveryPrice() ?? 0;
    $oldSubtotal = $cart?->oldSubtotal() ?? 0;
    $total = $cart?->total() ?? 0;
    $savings = $cart?->savings() ?? 0;
@endphp

<div class="va_cart-page">
    <x-site.breadcrumbs :items="[['label' => 'Корзина', 'url' => null]]" />

    <div class="va_cart-page__head">
        <div>
            <h1 class="va_page-title">Корзина</h1>
            <p class="va_page-sub">
                <span>{{ $count }}</span>
                <x-site.plural :count="$count" one="товар" few="товара" many="товаров" />
            </p>
        </div>

        @if ($items->isNotEmpty())
            <button class="va_link-btn" type="button" wire:click="clear">Очистить корзину</button>
        @endif
    </div>

    @if ($items->isEmpty())
        <div class="va_cart-page__empty">
            <span class="va_cart-empty__icon">
                <svg class="va_icon" aria-hidden="true"><use href="#va-i-cart" /></svg>
            </span>
            <p class="va_cart-empty__title">Корзина пуста</p>
            <p class="va_cart-empty__text">Добавьте товары из каталога — они сохранятся, даже если закрыть страницу.</p>
            <a class="va_btn va_btn--primary" href="{{ route('catalog') }}">Перейти в каталог</a>
        </div>
    @else
        <div class="va_cart-layout">
            <div class="va_cart-lines">
                <ul class="va_cart-lines__list">
                    @foreach ($items as $item)
                        <li class="va_cart-line" wire:key="cart-line-{{ $item->id }}">
                            <img
                                class="va_cart-line__img"
                                src="{{ $item->product->mainImage?->url() ?? asset('images/products/placeholder.svg') }}"
                                alt="{{ $item->product->name }}"
                                width="96"
                                height="96"
                                loading="lazy"
                            />

                            <div class="va_cart-line__body">
                                <a class="va_cart-line__title" href="{{ route('product.show', $item->product) }}">
                                    {{ $item->product->name }}
                                </a>
                                <p class="va_cart-line__meta">
                                    Продавец {{ $item->product->shop?->name ?? 'ByClick' }}
                                    · {{ $item->product->stock > 0 ? 'В наличии' : 'Под заказ' }}
                                </p>
                                <p class="va_cart-line__price">
                                    <span class="va_price">{{ $money($item->product->price) }}</span>
                                    @if ($item->product->hasDiscount())
                                        <span class="va_price va_price--old">{{ $money($item->product->old_price) }}</span>
                                    @endif
                                </p>
                            </div>

                            <div class="va_cart-line__controls">
                                <div class="va_qty">
                                    <button class="va_qty__btn" type="button" aria-label="Уменьшить количество" wire:click="decrement({{ $item->id }})" @disabled($item->quantity <= 1)>
                                        <svg class="va_icon" aria-hidden="true"><use href="#va-i-minus" /></svg>
                                    </button>
                                    <span class="va_qty__value">{{ $item->quantity }}</span>
                                    <button class="va_qty__btn" type="button" aria-label="Увеличить количество" wire:click="increment({{ $item->id }})" @disabled($item->quantity >= 99)>
                                        <svg class="va_icon" aria-hidden="true"><use href="#va-i-plus" /></svg>
                                    </button>
                                </div>

                                <button class="va_cart-line__remove" type="button" aria-label="Удалить «{{ $item->product->name }}» из корзины" wire:click="remove({{ $item->id }})">
                                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-trash" /></svg>
                                </button>
                            </div>

                            <p class="va_cart-line__sum">{{ $money($item->lineTotal()) }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>

            <aside class="va_summary" aria-label="Итоги заказа">
                <h2 class="va_summary__title">Ваш заказ</h2>

                <div class="va_summary__row">
                    <span>Товары</span>
                    <span>{{ $money($subtotal) }}</span>
                </div>

                @if ($discount > 0)
                    <div class="va_summary__row">
                        <span>Скидка по промокоду</span>
                        <span class="va_summary__discount">−{{ $money($discount) }}</span>
                    </div>
                @endif

                <div class="va_summary__row">
                    <span>Доставка</span>
                    <span class="va_summary__free">{{ $delivery > 0 ? $money($delivery) : 'бесплатно' }}</span>
                </div>

                @if ($oldSubtotal > $subtotal)
                    <div class="va_summary__row">
                        <span>Без скидки</span>
                        <span class="va_summary__old">{{ $money($oldSubtotal) }}</span>
                    </div>
                @endif

                <div class="va_summary__row va_summary__total">
                    <span>Итого</span>
                    <span>{{ $money($total) }}</span>
                </div>

                @if ($savings > 0)
                    <p class="va_summary__savings">
                        <svg class="va_icon" aria-hidden="true"><use href="#va-i-gift" /></svg>
                        Вы экономите <b>{{ $money($savings) }}</b>
                    </p>
                @endif

                {{-- Промокод: хранится в корзине, а не в сессии --}}
                @if ($cart?->promoCode)
                    <p class="va_summary__message">
                        Промокод <b>{{ $cart->promoCode->code }}</b> применён.
                        <button class="va_link-btn" type="button" wire:click="removePromo">Убрать</button>
                    </p>
                @else
                    <form class="va_summary__promo" wire:submit="applyPromo">
                        <input
                            class="va_input"
                            type="text"
                            wire:model="promoCode"
                            placeholder="Промокод"
                            aria-label="Промокод"
                        />
                        <button class="va_btn va_btn--outline" type="submit">Применить</button>
                    </form>

                    @if ($promoError)
                        <p class="va_summary__message">{{ $promoError }}</p>
                    @endif
                @endif

                <button
                    class="va_btn va_btn--primary va_btn--block"
                    type="button"
                    data-va-demo-toast="Демо-режим: оформление заказа появится на следующем шаге"
                >Оформить заказ</button>
                <p class="va_summary__hint">Демо-режим: оплата и доставка не подключены.</p>

                <div class="va_summary__links">
                    <a href="{{ route('catalog') }}">Продолжить покупки</a>
                    <a href="{{ url('/catalog?tag=sale') }}">Смотреть скидки</a>
                </div>
            </aside>
        </div>
    @endif
</div>
