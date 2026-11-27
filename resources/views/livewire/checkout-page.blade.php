@php
    $scale = (int) config('marketplace.currency_scale', 100);
    $currency = config('marketplace.currency', 'Руб');
    $money = fn (int $amount): string => number_format($amount / $scale, 2, ',', ' ').' '.$currency;

    $count = $cart?->totalQuantity() ?? 0;
    $subtotal = $cart?->subtotal() ?? 0;
    $discount = $cart?->discount() ?? 0;
@endphp

<div class="va_checkout-page">
    <x-site.breadcrumbs :items="[
        ['label' => 'Корзина', 'url' => route('cart')],
        ['label' => 'Оформление', 'url' => null],
    ]" />

    <div class="va_page-head">
        <h1 class="va_page-title">Оформление заказа</h1>
    </div>

    <ol class="va_steps-inline">
        <li class="is-muted"><b>1</b> Корзина</li>
        <li><b>2</b> Оформление</li>
        <li class="is-muted"><b>3</b> Готово</li>
    </ol>

    @if ($cart === null || $items->isEmpty())
        <div class="va_cart-page__empty">
            <span class="va_cart-empty__icon">
                <svg class="va_icon" aria-hidden="true"><use href="#va-i-cart" /></svg>
            </span>
            <p class="va_cart-empty__title">Нечего оформлять</p>
            <p class="va_cart-empty__text">Сначала добавьте товары в корзину — потом вернитесь сюда.</p>
            <a class="va_btn va_btn--primary" href="{{ route('catalog') }}">Перейти в каталог</a>
        </div>
    @else
        <div class="va_checkout-layout">
            <form class="va_checkout-form" wire:submit="placeOrder">
                {{-- 1. Контакты --}}
                <section class="va_panel">
                    <h2 class="va_panel__title"><span class="va_panel__num">1</span> Контакты</h2>

                    <div class="va_form-grid">
                        <label class="va_field">
                            Имя и фамилия
                            <input
                                class="va_input"
                                type="text"
                                wire:model="customerName"
                                autocomplete="name"
                                placeholder="Иван Петров"
                            />
                            @error('customerName')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="va_field">
                            Телефон
                            <input
                                class="va_input"
                                type="tel"
                                wire:model="customerPhone"
                                autocomplete="tel"
                                placeholder="+375 29 000-00-00"
                            />
                            @error('customerPhone')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="va_field va_form-grid--full">
                            E-mail для чека
                            <input
                                class="va_input"
                                type="email"
                                wire:model="customerEmail"
                                autocomplete="email"
                                placeholder="you@example.com"
                            />
                            @error('customerEmail')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>
                </section>

                {{-- 2. Доставка --}}
                <section class="va_panel">
                    <h2 class="va_panel__title"><span class="va_panel__num">2</span> Доставка</h2>

                    <div class="va_radios">
                        <label class="va_radio">
                            <input type="radio" value="courier" wire:model.live="deliveryMethod" />
                            <span>
                                <b>Курьером</b><br />
                                {{ $methodQuotes['courier']['label'] }},
                                {{ $methodQuotes['courier']['price'] > 0 ? $money($methodQuotes['courier']['price']) : 'бесплатно' }}
                            </span>
                        </label>
                        <label class="va_radio">
                            <input type="radio" value="pickup" wire:model.live="deliveryMethod" />
                            <span><b>В пункт выдачи</b><br />{{ $methodQuotes['pickup']['label'] }}, бесплатно</span>
                        </label>
                        <label class="va_radio">
                            <input type="radio" value="postamat" wire:model.live="deliveryMethod" />
                            <span><b>Постамат</b><br />{{ $methodQuotes['postamat']['label'] }}, бесплатно</span>
                        </label>
                    </div>

                    <p class="va_admin-note">
                        Бесплатная курьерская доставка от {{ $money($freeDeliveryFrom) }}.
                    </p>

                    @error('deliveryMethod')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror

                    @if ($selectedDeliveryMethod === \App\Enums\DeliveryMethod::Courier)
                        <label class="va_field">
                            Город доставки
                            <select class="va_input" wire:model.live="deliveryCity">
                                @foreach ($deliveryCities as $city)
                                    <option value="{{ $city }}">{{ $city }}</option>
                                @endforeach
                            </select>
                            @error('deliveryCity')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="va_field">
                            Адрес доставки
                            <input
                                class="va_input"
                                type="text"
                                wire:model="address"
                                placeholder="Улица, дом, квартира"
                            />
                            @error('address')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>
                    @else
                        <label class="va_field">
                            {{ $selectedDeliveryMethod === \App\Enums\DeliveryMethod::Postamat ? 'Постамат' : 'Пункт выдачи' }}
                            <select class="va_input" id="checkout-delivery-point" wire:model="deliveryPointId">
                                <option value="">Выберите пункт</option>
                                @foreach ($deliveryPoints as $point)
                                    <option value="{{ $point->id }}">
                                        {{ $point->city }}, {{ $point->address }}@if ($point->schedule) · {{ $point->schedule }}@endif
                                    </option>
                                @endforeach
                            </select>
                            @error('deliveryPointId')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>

                        <div class="va_delivery-map-block" wire:key="checkout-delivery-map-{{ $selectedDeliveryMethod->value }}">
                            <x-site.delivery-map
                                id="checkout-delivery-map"
                                :points="$deliveryPoints"
                                :selected-id="$deliveryPointId"
                                select-selector="#checkout-delivery-point"
                            />
                        </div>
                    @endif

                    <label class="va_field va_form-grid--full">
                        Комментарий курьеру
                        <textarea
                            class="va_input va_input--area"
                            wire:model="comment"
                            placeholder="Код домофона, удобное время"
                        ></textarea>
                        @error('comment')
                            <span class="va_field__error">{{ $message }}</span>
                        @enderror
                    </label>
                </section>

                {{-- 3. Оплата --}}
                <section class="va_panel">
                    <h2 class="va_panel__title"><span class="va_panel__num">3</span> Оплата</h2>

                    <div class="va_radios">
                        <label class="va_radio">
                            <input type="radio" value="online" wire:model="paymentMethod" />
                            <span><b>Картой онлайн</b><br />Visa, Mastercard, БЕЛКАРТ</span>
                        </label>
                        <label class="va_radio">
                            <input type="radio" value="cash" wire:model="paymentMethod" />
                            <span><b>Наличными</b><br />при получении</span>
                        </label>
                        <label class="va_radio">
                            <input type="radio" value="card" wire:model="paymentMethod" />
                            <span><b>Картой при получении</b><br />терминал курьера</span>
                        </label>
                    </div>

                    @error('paymentMethod')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror

                    <label class="va_check">
                        <input type="checkbox" wire:model="agree" />
                        <span>
                            Согласен с <a href="#" data-va-demo-toast="Демо-режим: условия и политика появятся позже">условиями</a>
                            и <a href="#" data-va-demo-toast="Демо-режим: условия и политика появятся позже">политикой конфиденциальности</a>
                        </span>
                    </label>

                    @error('agree')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror
                    @error('cart')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror

                    <button class="va_btn va_btn--primary va_btn--block" type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>Подтвердить заказ</span>
                        <span wire:loading>Оформляем…</span>
                    </button>
                </section>
            </form>

            {{-- Состав заказа --}}
            <aside class="va_summary" aria-label="Состав заказа">
                <h2 class="va_summary__title">
                    Ваш заказ
                    <span class="va_summary__count">({{ $count }})</span>
                </h2>

                <ul class="va_checkout-list">
                    @foreach ($items as $item)
                        <li class="va_checkout-item" wire:key="checkout-item-{{ $item->id }}">
                            <img
                                class="va_checkout-item__img"
                                src="{{ $item->product->mainImage?->url() ?? asset('images/products/placeholder.svg') }}"
                                alt="{{ $item->product->name }}"
                                width="56"
                                height="56"
                                loading="lazy"
                            />
                            <div class="va_checkout-item__body">
                                <p class="va_checkout-item__title">{{ $item->product->name }}</p>
                                <p class="va_checkout-item__meta">{{ $item->quantity }} × {{ $money($item->product->price) }}</p>
                            </div>
                            <p class="va_checkout-item__sum">{{ $money($item->lineTotal()) }}</p>
                        </li>
                    @endforeach
                </ul>

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
                    <span>
                        Доставка
                        <small class="va_summary__count">{{ $deliveryQuoteLabel }}</small>
                    </span>
                    <span class="va_summary__free">{{ $deliveryPrice > 0 ? $money($deliveryPrice) : 'бесплатно' }}</span>
                </div>

                <div class="va_summary__row va_summary__total">
                    <span>Итого</span>
                    <span>{{ $money($total) }}</span>
                </div>

                <p class="va_summary__hint">Доставка рассчитывается по городу и способу получения. Оплата — в демо-режиме.</p>

                <div class="va_summary__links">
                    <a href="{{ route('cart') }}">Вернуться в корзину</a>
                </div>
            </aside>
        </div>
    @endif
</div>
