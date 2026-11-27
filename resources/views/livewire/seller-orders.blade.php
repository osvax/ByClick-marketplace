@php
    $scale = (int) config('marketplace.currency_scale', 100);
    $currency = config('marketplace.currency', 'Руб');
    $money = fn (int $amount): string => number_format($amount / $scale, 2, ',', ' ').' '.$currency;
@endphp

<div>
    @if ($shop === null)
        <div class="va_panel">
            <h2 class="va_panel__title">Магазин ещё не подключён</h2>
            <p class="va_admin-note">
                Заказы появятся здесь после одобрения заявки и первых продаж.
            </p>
            <div>
                <a class="va_btn va_btn--primary" href="{{ route('seller') }}">Отправить заявку</a>
            </div>
        </div>
    @elseif ($orders->isEmpty())
        <div class="va_panel">
            <p class="va_admin-note">Заказов с товарами вашего магазина пока нет.</p>
        </div>
    @else
        <div class="va_orders">
            @foreach ($orders as $order)
                <article class="va_order" wire:key="seller-order-{{ $order->id }}">
                    <div class="va_order__head">
                        <span class="va_order__number">Заказ {{ $order->number }}</span>
                        <span>{{ ($order->placed_at ?? $order->created_at)?->translatedFormat('j F Y') }}</span>
                        <span class="va_status {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span>
                    </div>

                    <div class="va_order__body">
                        @foreach ($order->items as $item)
                            <div class="va_order__item" wire:key="seller-order-item-{{ $order->id }}-{{ $item->id }}">
                                @if ($item->product?->mainImage)
                                    <img
                                        src="{{ $item->product->mainImage->url() }}"
                                        alt="{{ $item->product_name }}"
                                        width="56"
                                        height="56"
                                        loading="lazy"
                                    />
                                @else
                                    <span class="va_order__placeholder" aria-hidden="true">
                                        <svg class="va_icon"><use href="#va-i-cart" /></svg>
                                    </span>
                                @endif

                                <div>
                                    <p class="va_order__name">{{ $item->product_name }}</p>
                                    <p class="va_order__meta">
                                        {{ $item->quantity }} шт.
                                        · {{ $item->product?->category?->name ?? 'Товар' }}
                                    </p>
                                </div>

                                <span class="va_order__price">{{ $money($item->total) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="va_order__foot">
                        <span class="va_order__total">
                            Ваша сумма: {{ $money((int) $order->items->sum('total')) }}
                        </span>
                        <span class="va_admin-note">
                            Покупатель: {{ $order->customer_name }}
                        </span>
                    </div>
                </article>
            @endforeach
        </div>

        <div style="margin-top: 16px;">
            {{ $orders->links('pagination.marketplace') }}
        </div>
    @endif
</div>
