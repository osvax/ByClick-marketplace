@php
    $scale = (int) config('marketplace.currency_scale', 100);
    $currency = config('marketplace.currency', 'Руб');
    $money = fn (int $amount): string => number_format($amount / $scale, 2, ',', ' ').' '.$currency;
    $types = \App\Enums\PromoType::cases();
@endphp

<div>
    <div class="va_admin-toolbar">
        <form class="va_admin-search" wire:submit.prevent>
            <input
                class="va_input"
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Поиск по коду"
                aria-label="Поиск промокодов"
            />
        </form>

        <button class="va_btn va_btn--primary va_btn--sm" type="button" wire:click="createPromoCode">
            + Добавить промокод
        </button>
    </div>

    @if ($showCreate)
        <form class="va_panel" wire:submit="store" style="margin-bottom: 18px;">
            <h2 class="va_panel__title">Новый промокод</h2>

            <div class="va_form-grid">
                <label class="va_field">
                    Код
                    <input class="va_input" type="text" wire:model="code" placeholder="SALE10" style="text-transform: uppercase;" />
                    @error('code')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="va_field">
                    Тип скидки
                    <select class="va_input" wire:model.live="type">
                        @foreach ($types as $promoType)
                            <option value="{{ $promoType->value }}">{{ $promoType->label() }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="va_field">
                    Значение
                    <input
                        class="va_input"
                        type="text"
                        inputmode="decimal"
                        wire:model="value"
                        placeholder="{{ $type === 'percent' ? '10' : '5.00' }}"
                    />
                    <span class="va_field__hint">
                        {{ $type === 'percent' ? 'Процент от суммы заказа' : 'Сумма скидки в '.$currency }}
                    </span>
                    @error('value')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="va_field">
                    Минимальная сумма, {{ $currency }}
                    <input class="va_input" type="text" inputmode="decimal" wire:model="minTotal" placeholder="0.00" />
                    @error('minTotal')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="va_field">
                    Лимит использований
                    <input class="va_input" type="number" min="1" wire:model="usageLimit" placeholder="Без лимита" />
                    @error('usageLimit')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="va_field">
                    Действует до
                    <input class="va_input" type="date" wire:model="endsAt" />
                    @error('endsAt')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <div class="va_admin-actions">
                <button class="va_btn va_btn--primary va_btn--sm" type="submit">Создать</button>
                <button class="va_btn va_btn--outline va_btn--sm" type="button" wire:click="cancelCreate">Отмена</button>
            </div>
        </form>
    @endif

    @if ($promoCodes->isEmpty())
        <div class="va_panel">
            <p class="va_admin-note">Промокоды не найдены.</p>
        </div>
    @else
        <div class="va_admin-table-wrap">
            <table class="va_table">
                <thead>
                    <tr>
                        <th>Код</th>
                        <th>Скидка</th>
                        <th>Мин. сумма</th>
                        <th>Использовано</th>
                        <th>Действует до</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($promoCodes as $promoCode)
                        <tr wire:key="admin-promo-{{ $promoCode->id }}">
                            <td class="va_table__num">{{ $promoCode->code }}</td>
                            <td>
                                @if ($promoCode->type === \App\Enums\PromoType::Percent)
                                    {{ $promoCode->value }}%
                                @else
                                    {{ $money($promoCode->value) }}
                                @endif
                            </td>
                            <td>{{ $money($promoCode->min_total) }}</td>
                            <td class="va_table__num">
                                {{ $promoCode->used_count }}
                                @if ($promoCode->usage_limit !== null)
                                    / {{ $promoCode->usage_limit }}
                                @endif
                            </td>
                            <td class="va_table__nowrap">{{ $promoCode->ends_at?->translatedFormat('j M Y') ?? 'Бессрочно' }}</td>
                            <td>
                                <span class="va_status {{ $promoCode->is_active ? 'va_status--done' : 'va_status--cancel' }}">
                                    {{ $promoCode->is_active ? 'Активен' : 'Выключен' }}
                                </span>
                            </td>
                            <td>
                                <button
                                    class="va_btn va_btn--outline va_btn--sm"
                                    type="button"
                                    wire:click="toggleActive({{ $promoCode->id }})"
                                >
                                    {{ $promoCode->is_active ? 'Выключить' : 'Включить' }}
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $promoCodes->links('pagination.marketplace') }}
        </div>
    @endif
</div>
