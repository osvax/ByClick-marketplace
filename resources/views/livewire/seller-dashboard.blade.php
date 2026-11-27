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
                Чтобы управлять товарами и заказами, отправьте заявку на подключение магазина.
                После проверки администратор откроет кабинет продавца.
            </p>
            <div class="va_admin-actions">
                <a class="va_btn va_btn--primary" href="{{ route('seller') }}">Стать продавцом</a>
                <a class="va_btn va_btn--outline" href="{{ route('home') }}">На главную</a>
            </div>
        </div>
    @else
        <div class="va_panel" style="margin-bottom: 18px;">
            <div class="va_admin-toolbar" style="margin-bottom: 0;">
                <div>
                    <h2 class="va_panel__title">{{ $shop->name }}</h2>
                    <p class="va_admin-note">
                        Статус магазина: {{ $shop->status->label() }}
                        @if ($shop->is_verified)
                            · проверенный продавец
                        @endif
                    </p>
                </div>
                <div class="va_admin-actions">
                    <a class="va_btn va_btn--outline va_btn--sm" href="{{ url('/seller') }}">Страница для продавцов</a>
                    <a class="va_btn va_btn--primary va_btn--sm" href="{{ route('seller.products') }}">Управлять товарами</a>
                </div>
            </div>
        </div>

        <div class="va_profile-stats">
            <div class="va_profile-stat">
                <span class="va_profile-stat__label">Товары</span>
                <strong class="va_profile-stat__value">{{ $stats['products'] }}</strong>
            </div>
            <div class="va_profile-stat">
                <span class="va_profile-stat__label">Опубликовано</span>
                <strong class="va_profile-stat__value">{{ $stats['active'] }}</strong>
            </div>
            <div class="va_profile-stat">
                <span class="va_profile-stat__label">Заказы с товарами</span>
                <strong class="va_profile-stat__value">{{ $stats['orders'] }}</strong>
            </div>
            <div class="va_profile-stat">
                <span class="va_profile-stat__label">Выручка</span>
                <strong class="va_profile-stat__value">{{ $money($stats['revenue']) }}</strong>
            </div>
        </div>

        @if ($stats['low_stock'] > 0)
            <div class="va_panel" style="margin-top: 18px;">
                <h2 class="va_panel__title">
                    Заканчиваются на складе
                    <span class="va_profile-group__badge va_profile-group__badge--accent">{{ $stats['low_stock'] }}</span>
                </h2>

                <div class="va_admin-table-wrap">
                    <table class="va_table">
                        <thead>
                            <tr>
                                <th>Товар</th>
                                <th>Остаток</th>
                                <th>Цена</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lowStock as $product)
                                <tr wire:key="low-stock-{{ $product->id }}">
                                    <td>
                                        <a href="{{ route('seller.products') }}">{{ $product->name }}</a>
                                    </td>
                                    <td class="va_table__num">{{ $product->stock }} шт.</td>
                                    <td>{{ $money($product->price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div>
                    <a class="va_btn va_btn--outline va_btn--sm" href="{{ route('seller.products') }}">
                        Перейти к остаткам
                    </a>
                </div>
            </div>
        @endif

        <div class="va_panel" style="margin-top: 18px;">
            <header class="va_section__head" style="margin-bottom: 0;">
                <h2 class="va_panel__title">Последние заказы</h2>
                <a class="va_section__link" href="{{ route('seller.orders') }}">
                    Все заказы
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-arrow-right" /></svg>
                </a>
            </header>

            @if ($recentOrders->isEmpty())
                <p class="va_admin-note">Пока нет заказов с товарами вашего магазина.</p>
            @else
                <div class="va_admin-table-wrap">
                    <table class="va_table">
                        <thead>
                            <tr>
                                <th>Заказ</th>
                                <th>Дата</th>
                                <th>Товары магазина</th>
                                <th>Сумма магазина</th>
                                <th>Статус</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentOrders as $order)
                                <tr wire:key="seller-recent-order-{{ $order->id }}">
                                    <td class="va_table__num">{{ $order->number }}</td>
                                    <td>{{ ($order->placed_at ?? $order->created_at)?->translatedFormat('j M Y') }}</td>
                                    <td>
                                        {{ $order->items->sum('quantity') }} шт.
                                        <div class="va_table__muted">
                                            {{ $order->items->pluck('product_name')->join(', ') }}
                                        </div>
                                    </td>
                                    <td class="va_table__num">{{ $money((int) $order->items->sum('total')) }}</td>
                                    <td>
                                        <span class="va_status {{ $order->status->badgeClass() }}">
                                            {{ $order->status->label() }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>
