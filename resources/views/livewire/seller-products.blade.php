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
                Управление товарами откроется после одобрения заявки продавца.
            </p>
            <div>
                <a class="va_btn va_btn--primary" href="{{ route('seller') }}">Отправить заявку</a>
            </div>
        </div>
    @else
        <div class="va_admin-toolbar">
            <form class="va_admin-search" wire:submit.prevent>
                <input
                    class="va_input"
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Поиск по названию"
                    aria-label="Поиск товаров"
                />
            </form>

            <button class="va_btn va_btn--primary va_btn--sm" type="button" wire:click="createProduct">
                + Добавить товар
            </button>
        </div>

        @if ($showCreate)
            <form class="va_panel" wire:submit="store" style="margin-bottom: 18px;">
                <h2 class="va_panel__title">Новый товар</h2>

                <div class="va_form-grid">
                    <label class="va_field">
                        Название
                        <input class="va_input" type="text" wire:model="newName" />
                        @error('newName')
                            <span class="va_field__error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="va_field">
                        Бренд
                        <input class="va_input" type="text" wire:model="newBrand" />
                        @error('newBrand')
                            <span class="va_field__error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="va_field">
                        Категория
                        <select class="va_input" wire:model="newCategoryId">
                            <option value="">Выберите категорию</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('newCategoryId')
                            <span class="va_field__error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="va_field">
                        Слаг (необязательно)
                        <input class="va_input" type="text" wire:model="newSlug" placeholder="Автоматически из названия" />
                        @error('newSlug')
                            <span class="va_field__error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="va_field">
                        Цена, {{ $currency }}
                        <input class="va_input" type="text" inputmode="decimal" wire:model="newPrice" placeholder="49.90" />
                        @error('newPrice')
                            <span class="va_field__error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="va_field">
                        Старая цена, {{ $currency }}
                        <input class="va_input" type="text" inputmode="decimal" wire:model="newOldPrice" placeholder="59.90" />
                        @error('newOldPrice')
                            <span class="va_field__error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="va_field">
                        Остаток, шт.
                        <input class="va_input" type="number" min="0" wire:model="newStock" />
                        @error('newStock')
                            <span class="va_field__error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="va_check">
                        <input type="checkbox" wire:model="newActive" />
                        <span>Опубликовать сразу</span>
                    </label>

                    <label class="va_field va_form-grid--full">
                        Описание
                        <textarea class="va_input va_input--area" wire:model="newDescription"></textarea>
                        @error('newDescription')
                            <span class="va_field__error">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                <div class="va_admin-actions">
                    <button class="va_btn va_btn--primary va_btn--sm" type="submit" wire:loading.attr="disabled">
                        Сохранить товар
                    </button>
                    <button class="va_btn va_btn--outline va_btn--sm" type="button" wire:click="cancelCreate">
                        Отмена
                    </button>
                </div>
            </form>
        @endif

        @if ($editingId !== null)
            <form class="va_panel" wire:submit="save" style="margin-bottom: 18px;">
                <h2 class="va_panel__title">Редактирование товара</h2>

                <div class="va_form-grid">
                    <label class="va_field">
                        Название
                        <input class="va_input" type="text" wire:model="editName" />
                        @error('editName')
                            <span class="va_field__error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="va_field">
                        Бренд
                        <input class="va_input" type="text" wire:model="editBrand" />
                        @error('editBrand')
                            <span class="va_field__error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="va_field">
                        Категория
                        <select class="va_input" wire:model="editCategoryId">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('editCategoryId')
                            <span class="va_field__error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="va_field">
                        Слаг
                        <input class="va_input" type="text" wire:model="editSlug" />
                        @error('editSlug')
                            <span class="va_field__error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="va_field">
                        Цена, {{ $currency }}
                        <input class="va_input" type="text" inputmode="decimal" wire:model="editPrice" />
                        @error('editPrice')
                            <span class="va_field__error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="va_field">
                        Старая цена, {{ $currency }}
                        <input class="va_input" type="text" inputmode="decimal" wire:model="editOldPrice" />
                        @error('editOldPrice')
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

                    <label class="va_field va_form-grid--full">
                        Описание
                        <textarea class="va_input va_input--area" wire:model="editDescription"></textarea>
                        @error('editDescription')
                            <span class="va_field__error">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                <div class="va_admin-actions">
                    <button class="va_btn va_btn--primary va_btn--sm" type="submit" wire:loading.attr="disabled">
                        Сохранить изменения
                    </button>
                    <button class="va_btn va_btn--outline va_btn--sm" type="button" wire:click="cancelEdit">
                        Отмена
                    </button>
                </div>
            </form>
        @endif

        @if ($products->isEmpty())
            <div class="va_panel">
                <p class="va_admin-note">
                    @if ($search !== '')
                        По запросу «{{ $search }}» товары не найдены.
                    @else
                        В магазине пока нет товаров. Добавьте первый товар, чтобы начать продажи.
                    @endif
                </p>
            </div>
        @else
            <div class="va_admin-table-wrap">
                <table class="va_table">
                    <thead>
                        <tr>
                            <th>Товар</th>
                            <th>Категория</th>
                            <th>Цена</th>
                            <th>Остаток</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr wire:key="seller-product-{{ $product->id }}">
                                <td>
                                    <a href="{{ url('/product/'.$product->slug) }}" target="_blank" rel="noopener">
                                        {{ $product->name }}
                                    </a>
                                    @if ($product->brand)
                                        <div class="va_table__muted">{{ $product->brand }}</div>
                                    @endif
                                </td>
                                <td>{{ $product->category?->name ?? '—' }}</td>
                                <td class="va_table__num">{{ $money($product->price) }}</td>
                                <td class="va_table__num">
                                    <span class="{{ $product->stock <= 3 ? 'va_status va_status--cancel' : '' }}">
                                        {{ $product->stock }} шт.
                                    </span>
                                </td>
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
    @endif
</div>
