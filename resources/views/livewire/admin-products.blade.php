@php
    $scale = (int) config('marketplace.currency_scale', 100);
    $currency = config('marketplace.currency', 'Руб');
    $money = fn (int $amount): string => number_format($amount / $scale, 2, ',', ' ').' '.$currency;
@endphp

<div>
    <div class="va_admin-toolbar">
        <form class="va_admin-search" wire:submit.prevent>
            <input
                class="va_input"
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Название или бренд"
                aria-label="Поиск товаров"
            />
        </form>

        <label class="va_field" style="margin: 0;">
            <select class="va_input" wire:model.live="shopId" aria-label="Фильтр по магазину" style="height: 38px; min-width: 200px;">
                <option value="">Все магазины</option>
                @foreach ($shops as $shop)
                    <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                @endforeach
            </select>
        </label>
    </div>

    @if ($editingId !== null)
        <form class="va_panel" wire:submit="save" style="margin-bottom: 18px;">
            <h2 class="va_panel__title">Быстрое редактирование</h2>

            <div class="va_form-grid">
                <label class="va_field">
                    Цена, {{ $currency }}
                    <input class="va_input" type="text" inputmode="decimal" wire:model="editPrice" />
                    @error('editPrice')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="va_field">
                    Остаток, шт.
                    <input class="va_input" type="number" min="0" wire:model="editStock" />
                    @error('editStock')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="va_check">
                    <input type="checkbox" wire:model="editActive" />
                    <span>Опубликован</span>
                </label>
            </div>

            <div class="va_admin-actions">
                <button class="va_btn va_btn--primary va_btn--sm" type="submit">Сохранить</button>
                <button class="va_btn va_btn--outline va_btn--sm" type="button" wire:click="cancelEdit">Отмена</button>
            </div>
        </form>
    @endif

    @if ($products->isEmpty())
        <div class="va_panel">
            <p class="va_admin-note">Товаров с такими параметрами нет.</p>
        </div>
    @else
        <div class="va_admin-table-wrap">
            <table class="va_table">
                <thead>
                    <tr>
                        <th>Товар</th>
                        <th>Магазин</th>
                        <th>Категория</th>
                        <th>Цена</th>
                        <th>Остаток</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr wire:key="admin-product-{{ $product->id }}">
                            <td>
                                <a href="{{ url('/product/'.$product->slug) }}" target="_blank" rel="noopener">
                                    {{ $product->name }}
                                </a>
                                @if ($product->brand)
                                    <div class="va_table__muted">{{ $product->brand }}</div>
                                @endif
                            </td>
                            <td>{{ $product->shop?->name ?? '—' }}</td>
                            <td>{{ $product->category?->name ?? '—' }}</td>
                            <td class="va_table__num">{{ $money($product->price) }}</td>
                            <td class="va_table__num">{{ $product->stock }} шт.</td>
                            <td>
                                <span class="va_status {{ $product->is_active ? 'va_status--done' : 'va_status--cancel' }}">
                                    {{ $product->is_active ? 'Опубликован' : 'Скрыт' }}
                                </span>
                            </td>
                            <td>
                                <div class="va_admin-actions">
                                    <button class="va_btn va_btn--outline va_btn--sm" type="button" wire:click="edit({{ $product->id }})">
                                        Изменить
                                    </button>
                                    <button class="va_btn va_btn--ghost va_btn--sm" type="button" wire:click="toggleActive({{ $product->id }})">
                                        {{ $product->is_active ? 'Скрыть' : 'Опубликовать' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $products->links('pagination.marketplace') }}
        </div>
    @endif
</div>
