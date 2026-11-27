<div class="va_container">
    <x-site.breadcrumbs :items="[['label' => 'Мои заказы', 'url' => null]]" />

    <div class="va_page-head">
        <h1 class="va_page-title">Мои заказы</h1>
        <p class="va_page-sub">
            Всего заказов: {{ $orders->total() }} · оформленные в демо-режиме
        </p>
    </div>

    @if ($orders->isEmpty())
        <div class="va_cart-page__empty">
            <span class="va_cart-empty__icon">
                <svg class="va_icon" aria-hidden="true"><use href="#va-i-check" /></svg>
            </span>
            <p class="va_cart-empty__title">Заказов пока нет</p>
            <p class="va_cart-empty__text">Выберите что-нибудь в каталоге — заказ появится здесь после оформления.</p>
            <a class="va_btn va_btn--primary" href="{{ route('catalog') }}">Перейти в каталог</a>
        </div>
    @else
        <div class="va_orders">
            @foreach ($orders as $order)
                <x-site.order-card :order="$order" wire:key="order-{{ $order->id }}" />
            @endforeach
        </div>

        {{ $orders->links('pagination.marketplace') }}
    @endif
</div>
