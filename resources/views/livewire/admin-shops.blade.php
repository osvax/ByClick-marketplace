<div>
    <div class="va_admin-toolbar">
        <div class="va_admin-filters" style="margin-bottom: 0;">
            <button type="button" class="{{ $status === 'all' ? 'is-active' : '' }}" wire:click="setStatus('all')">Все</button>
            <button type="button" class="{{ $status === 'active' ? 'is-active' : '' }}" wire:click="setStatus('active')">Активные</button>
            <button type="button" class="{{ $status === 'pending' ? 'is-active' : '' }}" wire:click="setStatus('pending')">На модерации</button>
            <button type="button" class="{{ $status === 'suspended' ? 'is-active' : '' }}" wire:click="setStatus('suspended')">Заблокированные</button>
        </div>

        <form class="va_admin-search" wire:submit.prevent>
            <input
                class="va_input"
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Магазин или владелец"
                aria-label="Поиск магазинов"
            />
        </form>
    </div>

    @if ($shops->isEmpty())
        <div class="va_panel">
            <p class="va_admin-note">Магазинов с такими параметрами нет.</p>
        </div>
    @else
        <div class="va_admin-table-wrap">
            <table class="va_table">
                <thead>
                    <tr>
                        <th>Магазин</th>
                        <th>Владелец</th>
                        <th>Товары</th>
                        <th>Статус</th>
                        <th>Проверен</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($shops as $shop)
                        <tr wire:key="admin-shop-{{ $shop->id }}">
                            <td>
                                <strong>{{ $shop->name }}</strong>
                                <div class="va_table__muted">{{ $shop->slug }}</div>
                            </td>
                            <td>
                                {{ $shop->user?->name ?? '—' }}
                                <div class="va_table__muted">{{ $shop->user?->email }}</div>
                            </td>
                            <td class="va_table__num">{{ $shop->products_count }}</td>
                            <td>
                                <span class="va_status {{ $shop->status->value === 'active' ? 'va_status--done' : ($shop->status->value === 'suspended' ? 'va_status--cancel' : 'va_status--way') }}">
                                    {{ $shop->status->label() }}
                                </span>
                            </td>
                            <td>
                                <span class="va_status {{ $shop->is_verified ? 'va_status--done' : 'va_status--way' }}">
                                    {{ $shop->is_verified ? 'Да' : 'Нет' }}
                                </span>
                            </td>
                            <td>
                                <div class="va_admin-actions">
                                    <button
                                        class="va_btn va_btn--outline va_btn--sm"
                                        type="button"
                                        wire:click="toggleStatus({{ $shop->id }})"
                                    >
                                        {{ $shop->status->value === 'active' ? 'Заблокировать' : 'Активировать' }}
                                    </button>
                                    <button
                                        class="va_btn va_btn--ghost va_btn--sm"
                                        type="button"
                                        wire:click="toggleVerified({{ $shop->id }})"
                                    >
                                        {{ $shop->is_verified ? 'Снять отметку' : 'Проверен' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $shops->links('pagination.marketplace') }}
        </div>
    @endif
</div>
