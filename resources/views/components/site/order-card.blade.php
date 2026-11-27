@props(['order'])

@php
    $scale = (int) config('marketplace.currency_scale', 100);
    $currency = config('marketplace.currency', 'BYN');
    $money = fn (int $amount): string => number_format($amount / $scale, 2, ',', ' ').' '.$currency;
    $date = $order->placed_at ?? $order->created_at;
@endphp

<article {{ $attributes->class(['va_order']) }}>
    <div class="va_order__head">
        <span class="va_order__number">Заказ {{ $order->number }}</span>
        @if ($date)
            <span>{{ $date->translatedFormat('j F Y') }}</span>
        @endif
        <span class="va_status {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span>
    </div>

    <div class="va_order__body">
        @foreach ($order->items as $item)
            <div class="va_order__item" wire:key="order-item-{{ $order->id }}-{{ $item->id }}">
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
                    <p class="va_order__name">
                        @if ($item->product)
                            <a href="{{ url('/product/'.$item->product->slug) }}">{{ $item->product_name }}</a>
                        @else
                            {{ $item->product_name }}
                        @endif
                    </p>
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
        <span class="va_order__total">Итого: {{ $money($order->total) }}</span>
        <div class="va_order__actions">
            <a class="va_btn va_btn--outline" href="{{ route('checkout.success', $order) }}">Детали</a>
            <button class="va_btn va_btn--primary" type="button" wire:click="repeatOrder({{ $order->id }})">
                Повторить заказ
            </button>
        </div>
    </div>
</article>
