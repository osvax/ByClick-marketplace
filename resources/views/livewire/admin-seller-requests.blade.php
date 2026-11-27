<div>
    <div class="va_admin-toolbar">
        <div class="va_admin-filters" style="margin-bottom: 0;">
            <button type="button" class="{{ $status === 'new' ? 'is-active' : '' }}" wire:click="setStatus('new')">
                Новые ({{ $counts['new'] }})
            </button>
            <button type="button" class="{{ $status === 'approved' ? 'is-active' : '' }}" wire:click="setStatus('approved')">
                Одобренные ({{ $counts['approved'] }})
            </button>
            <button type="button" class="{{ $status === 'rejected' ? 'is-active' : '' }}" wire:click="setStatus('rejected')">
                Отклонённые ({{ $counts['rejected'] }})
            </button>
            <button type="button" class="{{ $status === 'all' ? 'is-active' : '' }}" wire:click="setStatus('all')">
                Все
            </button>
        </div>

        <form class="va_admin-search" wire:submit.prevent>
            <input
                class="va_input"
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Имя, e-mail или магазин"
                aria-label="Поиск заявок"
            />
        </form>
    </div>

    @if ($requests->isEmpty())
        <div class="va_panel">
            <p class="va_admin-note">Заявок с таким статусом нет.</p>
        </div>
    @else
        <div class="va_admin-table-wrap">
            <table class="va_table">
                <thead>
                    <tr>
                        <th>Магазин</th>
                        <th>Контакт</th>
                        <th>Категория</th>
                        <th>Комментарий</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $request)
                        <tr wire:key="admin-request-{{ $request->id }}">
                            <td>
                                <strong>{{ $request->shop_name ?: 'Без названия' }}</strong>
                                <div class="va_table__muted">{{ $request->created_at?->translatedFormat('j M Y H:i') }}</div>
                            </td>
                            <td>
                                {{ $request->name }}
                                <div class="va_table__muted">{{ $request->email }}</div>
                                <div class="va_table__muted">{{ $request->phone }}</div>
                                <div class="va_table__muted">
                                    {{ $request->user_id ? 'Зарегистрирован' : 'Гость' }}
                                </div>
                            </td>
                            <td>{{ $request->category?->name ?? '—' }}</td>
                            <td>
                                <span class="va_admin-note">{{ $request->comment ?: '—' }}</span>
                                @error('review-'.$request->id)
                                    <div class="va_field__error">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <span class="va_status {{ $request->status->value === 'approved' ? 'va_status--done' : ($request->status->value === 'rejected' ? 'va_status--cancel' : 'va_status--way') }}">
                                    {{ $request->status->label() }}
                                </span>
                            </td>
                            <td>
                                @if ($request->status->value === 'new')
                                    <div class="va_admin-actions">
                                        <button
                                            class="va_btn va_btn--primary va_btn--sm"
                                            type="button"
                                            wire:click="approve({{ $request->id }})"
                                            wire:loading.attr="disabled"
                                        >
                                            Одобрить
                                        </button>
                                        <button
                                            class="va_btn va_btn--outline va_btn--sm"
                                            type="button"
                                            wire:click="reject({{ $request->id }})"
                                            wire:confirm="Отклонить заявку?"
                                        >
                                            Отклонить
                                        </button>
                                    </div>
                                @else
                                    <span class="va_admin-note">Обработана</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $requests->links('pagination.marketplace') }}
        </div>
    @endif
</div>
