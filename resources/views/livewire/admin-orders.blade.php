@php
    $scale = (int) config('marketplace.currency_scale', 100);
    $currency = config('marketplace.currency', 'Руб');
    $money = fn (int $amount): string => number_format($amount / $scale, 2, ',', ' ').' '.$currency;
@endphp

<div>
    <div class="va_admin-toolbar">
        <div class="va_admin-filters" style="margin-bottom: 0;">
            <button type="button" class="{{ $status === 'all' ? 'is-active' : '' }}" wire:click="setStatus('all')">Все</button>
            @foreach ($statuses as $orderStatus)
                <button type="button" class="{{ $status === $orderStatus->value ? 'is-active' : '' }}" wire:click="setStatus('{{ $orderStatus->value }}')">
                    {{ $orderStatus->label() }}
                </button>
            @endforeach
        </div>

        <form class="va_admin-search" wire:submit.prevent>
            <input
                class="va_input"
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Номер, имя, телефон или e-mail"
                aria-label="Поиск заказов"
            />
        </form>
    </div>

    @if ($orders->isEmpty())
        <div class="va_panel">
            <p class="va_admin-note">Заказов с такими параметрами нет.</p>
        </div>
    @else
        <div class="va_admin-table-wrap">
            <table class="va_table">
                <thead>
                    <tr>
                        <th>Заказ</th>
                        <th>Покупатель</th>
                        <th>Сумма</th>
                        <th>Оплата</th>
                        <th>Дата</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr wire:key="admin-order-{{ $order->id }}">
                            <td class="va_table__num">
                                {{ $order->number }}
                                <div class="va_table__muted">{{ $order->delivery_method->label() }}</div>
                            </td>
                            <td>
                                {{ $order->customer_name }}
                                <div class="va_table__muted">{{ $order->customer_phone }}</div>
                                <div class="va_table__muted">{{ $order->customer_email }}</div>
                            </td>
                            <td class="va_table__num">{{ $money($order->total) }}</td>
                            <td>{{ $order->payment_method->label() }}</td>
                            <td class="va_table__nowrap">
                                {{ ($order->placed_at ?? $order->created_at)?->translatedFormat('j M Y H:i') }}
                            </td>
                            <td>
                                <select
                                    class="va_input"
                                    wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                    aria-label="Статус заказа {{ $order->number }}"
                                    style="height: 36px; min-width: 170px; padding: 6px 10px; font-size: 12.5px;"
                                >
                                    @foreach ($statuses as $orderStatus)
                                        <option value="{{ $orderStatus->value }}" @selected($order->status === $orderStatus)>
                                            {{ $orderStatus->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $orders->links('pagination.marketplace') }}
        </div>
    @endif
</div>
