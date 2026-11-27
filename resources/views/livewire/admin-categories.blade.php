<div>
    <div class="va_admin-toolbar">
        <form class="va_admin-search" wire:submit.prevent>
            <input
                class="va_input"
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Название категории"
                aria-label="Поиск категорий"
            />
        </form>

        <button class="va_btn va_btn--primary va_btn--sm" type="button" wire:click="createCategory">
            + Добавить категорию
        </button>
    </div>

    @if ($showCreate)
        <form class="va_panel" wire:submit="store" style="margin-bottom: 18px;">
            <h2 class="va_panel__title">Новая категория</h2>

            <div class="va_form-grid">
                <label class="va_field">
                    Название
                    <input class="va_input" type="text" wire:model="newName" />
                    @error('newName')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="va_field">
                    Родительская категория
                    <select class="va_input" wire:model="newParentId">
                        <option value="">Корневая категория</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('newParentId')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="va_field">
                    Порядок
                    <input class="va_input" type="number" min="0" wire:model="newSort" />
                    @error('newSort')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="va_check">
                    <input type="checkbox" wire:model="newActive" />
                    <span>Показывать в каталоге</span>
                </label>
            </div>

            <div class="va_admin-actions">
                <button class="va_btn va_btn--primary va_btn--sm" type="submit">Создать</button>
                <button class="va_btn va_btn--outline va_btn--sm" type="button" wire:click="cancelCreate">Отмена</button>
            </div>
        </form>
    @endif

    @if ($editingId !== null)
        <form class="va_panel" wire:submit="save" style="margin-bottom: 18px;">
            <h2 class="va_panel__title">Редактирование категории</h2>

            <div class="va_form-grid">
                <label class="va_field">
                    Название
                    <input class="va_input" type="text" wire:model="editName" />
                    @error('editName')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="va_field">
                    Порядок
                    <input class="va_input" type="number" min="0" wire:model="editSort" />
                    @error('editSort')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="va_check">
                    <input type="checkbox" wire:model="editActive" />
                    <span>Показывать в каталоге</span>
                </label>
            </div>

            <div class="va_admin-actions">
                <button class="va_btn va_btn--primary va_btn--sm" type="submit">Сохранить</button>
                <button class="va_btn va_btn--outline va_btn--sm" type="button" wire:click="cancelEdit">Отмена</button>
            </div>
        </form>
    @endif

    @if ($categories->isEmpty())
        <div class="va_panel">
            <p class="va_admin-note">Категории не найдены.</p>
        </div>
    @else
        <div class="va_admin-table-wrap">
            <table class="va_table">
                <thead>
                    <tr>
                        <th>Категория</th>
                        <th>Родитель</th>
                        <th>Товары</th>
                        <th>Порядок</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr wire:key="admin-category-{{ $category->id }}">
                            <td>
                                <strong>{{ $category->name }}</strong>
                                <div class="va_table__muted">{{ $category->slug }}</div>
                            </td>
                            <td>{{ $category->parent?->name ?? '—' }}</td>
                            <td class="va_table__num">{{ $category->products_count }}</td>
                            <td class="va_table__num">{{ $category->sort_order }}</td>
                            <td>
                                <span class="va_status {{ $category->is_active ? 'va_status--done' : 'va_status--cancel' }}">
                                    {{ $category->is_active ? 'Показана' : 'Скрыта' }}
                                </span>
                            </td>
                            <td>
                                <div class="va_admin-actions">
                                    <button class="va_btn va_btn--outline va_btn--sm" type="button" wire:click="edit({{ $category->id }})">
                                        Изменить
                                    </button>
                                    <button class="va_btn va_btn--ghost va_btn--sm" type="button" wire:click="toggleActive({{ $category->id }})">
                                        {{ $category->is_active ? 'Скрыть' : 'Показать' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
