@php
    $scale = (int) config('marketplace.currency_scale', 100);
    $currency = config('marketplace.currency', 'Руб');
    $money = fn (int $amount): string => number_format($amount / $scale, 2, ',', ' ').' '.$currency;
@endphp

<div>
    <div class="va_profile-stats">
        <div class="va_profile-stat">
            <span class="va_profile-stat__label">Пользователи</span>
            <strong class="va_profile-stat__value">{{ $stats['users'] }}</strong>
            <span class="va_admin-note">Продавцов: {{ $stats['sellers'] }}</span>
        </div>
        <div class="va_profile-stat">
            <span class="va_profile-stat__label">Заказы</span>
            <strong class="va_profile-stat__value">{{ $stats['orders'] }}</strong>
            <span class="va_admin-note">Выручка: {{ $money($stats['revenue']) }}</span>
        </div>
        <div class="va_profile-stat">
            <span class="va_profile-stat__label">Товары</span>
            <strong class="va_profile-stat__value">{{ $stats['products'] }}</strong>
            <span class="va_admin-note">Опубликовано: {{ $stats['active_products'] }}</span>
        </div>
        <div class="va_profile-stat">
            <span class="va_profile-stat__label">Магазины</span>
            <strong class="va_profile-stat__value">{{ $stats['shops'] }}</strong>
            <span class="va_admin-note">На модерации: {{ $stats['pending_shops'] }}</span>
        </div>
    </div>

    <div class="va_panel" style="margin-top: 18px;">
        <div class="va_admin-toolbar" style="margin-bottom: 0;">
            <h2 class="va_panel__title">Очередь модерации</h2>
            <a class="va_section__link" href="{{ route('admin.requests') }}">
                Все заявки
                <svg class="va_icon" aria-hidden="true"><use href="#va-i-arrow-right" /></svg>
            </a>
        </div>

        @if ($pendingRequests->isEmpty())
            <p class="va_admin-note">Новых заявок продавцов нет. Магазины на модерации: {{ $stats['pending_shops'] }}.</p>
        @else
            <div class="va_admin-table-wrap">
                <table class="va_table">
                    <thead>
                        <tr>
                            <th>Магазин</th>
                            <th>Контакт</th>
                            <th>Категория</th>
                            <th>Дата</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendingRequests as $request)
                            <tr wire:key="admin-dashboard-request-{{ $request->id }}">
                                <td>
                                    <a href="{{ route('admin.requests') }}">{{ $request->shop_name ?: 'Без названия' }}</a>
                                </td>
                                <td>
                                    {{ $request->name }}
                                    <div class="va_table__muted">{{ $request->email }}</div>
                                </td>
                                <td>{{ $request->category?->name ?? '—' }}</td>
                                <td class="va_table__nowrap">{{ $request->created_at?->translatedFormat('j M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="va_panel" style="margin-top: 18px;">
        <div class="va_admin-toolbar" style="margin-bottom: 0;">
            <h2 class="va_panel__title">Последние заказы</h2>
            <a class="va_section__link" href="{{ route('admin.orders') }}">
                Все заказы
                <svg class="va_icon" aria-hidden="true"><use href="#va-i-arrow-right" /></svg>
            </a>
        </div>

        @if ($recentOrders->isEmpty())
            <p class="va_admin-note">Заказов пока нет.</p>
        @else
            <div class="va_admin-table-wrap">
                <table class="va_table">
                    <thead>
                        <tr>
                            <th>Заказ</th>
                            <th>Покупатель</th>
                            <th>Сумма</th>
                            <th>Статус</th>
                            <th>Дата</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentOrders as $order)
                            <tr wire:key="admin-dashboard-order-{{ $order->id }}">
                                <td class="va_table__num">{{ $order->number }}</td>
                                <td>
                                    {{ $order->customer_name }}
                                    <div class="va_table__muted">{{ $order->customer_email }}</div>
                                </td>
                                <td class="va_table__num">{{ $money($order->total) }}</td>
                                <td>
                                    <span class="va_status {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span>
                                </td>
                                <td class="va_table__nowrap">{{ ($order->placed_at ?? $order->created_at)?->translatedFormat('j M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
