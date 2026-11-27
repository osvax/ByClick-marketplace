@php
    $scale = (int) config('marketplace.currency_scale', 100);
    $currency = config('marketplace.currency', 'BYN');
    $money = fn (int $amount): string => number_format($amount / $scale, 2, ',', ' ').' '.$currency;
@endphp

<x-layouts.app
    title="Заказ {{ $order->number }} оформлен — ByClick"
    description="Заказ на ByClick успешно оформлен."
    :canonical="route('checkout.success', $order)"
>
    <div class="va_container">
        <x-site.breadcrumbs :items="[
            ['label' => 'Корзина', 'url' => route('cart')],
            ['label' => 'Оформление', 'url' => route('checkout')],
            ['label' => 'Готово', 'url' => null],
        ]" />

        <ol class="va_steps-inline">
            <li class="is-muted"><b>1</b> Корзина</li>
            <li class="is-muted"><b>2</b> Оформление</li>
            <li><b>3</b> Готово</li>
        </ol>

        <div class="va_checkout-success">
            <span class="va_checkout-success__icon">
                <svg class="va_icon" aria-hidden="true"><use href="#va-i-check" /></svg>
            </span>
            <h1 class="va_checkout-success__title">Заказ оформлен</h1>
            <p class="va_checkout-success__text">
                Спасибо! Мы отправили письмо с деталями на {{ $order->customer_email }}.
                Это демонстрационный заказ — оплата не списывалась.
            </p>
            <p class="va_checkout-success__number">Номер заказа: {{ $order->number }}</p>
            <a class="va_btn va_btn--primary" href="{{ route('catalog') }}">Продолжить покупки</a>
            <a class="va_btn va_btn--outline" href="{{ route('home') }}">На главную</a>
        </div>

        <div class="va_checkout-layout va_checkout-success-details">
            <section class="va_panel">
                <h2 class="va_panel__title">Детали заказа</h2>

                <div class="va_summary__row">
                    <span>Статус</span>
                    <span>{{ $order->status->label() }}</span>
                </div>
                <div class="va_summary__row">
                    <span>Получатель</span>
                    <span>{{ $order->customer_name }}</span>
                </div>
                <div class="va_summary__row">
                    <span>Телефон</span>
                    <span>{{ $order->customer_phone }}</span>
                </div>
                <div class="va_summary__row">
                    <span>Доставка</span>
                    <span>{{ $order->delivery_method->label() }}</span>
                </div>
                <div class="va_summary__row">
                    <span>{{ $order->delivery_method === \App\Enums\DeliveryMethod::Courier ? 'Адрес' : 'Пункт' }}</span>
                    <span>
                        @if ($order->delivery_method === \App\Enums\DeliveryMethod::Courier)
                            {{ $order->address }}
                        @else
                            {{ $order->deliveryPoint?->city }}, {{ $order->deliveryPoint?->address }}
                        @endif
                    </span>
                </div>
                <div class="va_summary__row">
                    <span>Оплата</span>
                    <span>{{ $order->payment_method->label() }}</span>
                </div>
            </section>

            <aside class="va_summary" aria-label="Состав заказа">
                <h2 class="va_summary__title">
                    Состав заказа
                    <span class="va_summary__count">({{ $order->items->sum('quantity') }})</span>
                </h2>

                <ul class="va_checkout-list">
                    @foreach ($order->items as $item)
                        <li class="va_checkout-item va_checkout-item--no-img">
                            <div class="va_checkout-item__body">
                                <p class="va_checkout-item__title">{{ $item->product_name }}</p>
                                <p class="va_checkout-item__meta">{{ $item->quantity }} × {{ $money($item->price) }}</p>
                            </div>
                            <p class="va_checkout-item__sum">{{ $money($item->total) }}</p>
                        </li>
                    @endforeach
                </ul>

                <div class="va_summary__row">
                    <span>Товары</span>
                    <span>{{ $money($order->subtotal) }}</span>
                </div>

                @if ($order->discount > 0)
                    <div class="va_summary__row">
                        <span>Скидка{{ $order->promoCode ? ' по промокоду '.$order->promoCode->code : '' }}</span>
                        <span class="va_summary__discount">−{{ $money($order->discount) }}</span>
                    </div>
                @endif

                <div class="va_summary__row">
                    <span>Доставка</span>
                    <span class="va_summary__free">{{ $order->delivery_price > 0 ? $money($order->delivery_price) : 'бесплатно' }}</span>
                </div>

                <div class="va_summary__row va_summary__total">
                    <span>Итого</span>
                    <span>{{ $money($order->total) }}</span>
                </div>
            </aside>
        </div>
    </div>
</x-layouts.app>
