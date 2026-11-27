<div>
    <div class="va_admin-toolbar">
        <div class="va_admin-filters" style="margin-bottom: 0;">
            <button type="button" class="{{ $role === 'all' ? 'is-active' : '' }}" wire:click="setRole('all')">Все</button>
            @foreach ($roles as $userRole)
                <button type="button" class="{{ $role === $userRole->value ? 'is-active' : '' }}" wire:click="setRole('{{ $userRole->value }}')">
                    {{ $userRole->label() }}
                </button>
            @endforeach
        </div>

        <form class="va_admin-search" wire:submit.prevent>
            <input
                class="va_input"
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Имя, e-mail или телефон"
                aria-label="Поиск пользователей"
            />
        </form>
    </div>

    @if ($users->isEmpty())
        <div class="va_panel">
            <p class="va_admin-note">Пользователей с такими параметрами нет.</p>
        </div>
    @else
        <div class="va_admin-table-wrap">
            <table class="va_table">
                <thead>
                    <tr>
                        <th>Пользователь</th>
                        <th>Телефон</th>
                        <th>Заказы</th>
                        <th>Заявки</th>
                        <th>Регистрация</th>
                        <th>Роль</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr wire:key="admin-user-{{ $user->id }}">
                            <td>
                                <strong>{{ $user->name }}</strong>
                                <div class="va_table__muted">{{ $user->email }}</div>
                                @if ($user->shop)
                                    <div class="va_table__muted">Магазин: {{ $user->shop->name }}</div>
                                @endif
                            </td>
                            <td>{{ $user->phone ?: '—' }}</td>
                            <td class="va_table__num">{{ $user->orders_count }}</td>
                            <td class="va_table__num">{{ $user->seller_requests_count }}</td>
                            <td class="va_table__nowrap">{{ $user->created_at?->translatedFormat('j M Y') }}</td>
                            <td>
                                <select
                                    class="va_input"
                                    wire:change="updateRole({{ $user->id }}, $event.target.value)"
                                    aria-label="Роль пользователя {{ $user->name }}"
                                    style="height: 36px; min-width: 150px; padding: 6px 10px; font-size: 12.5px;"
                                    @disabled($user->id === auth()->id())
                                >
                                    @foreach ($roles as $userRole)
                                        <option value="{{ $userRole->value }}" @selected($user->role === $userRole)>
                                            {{ $userRole->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role-'.$user->id)
                                    <div class="va_field__error">{{ $message }}</div>
                                @enderror
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $users->links('pagination.marketplace') }}
        </div>
    @endif
</div>
