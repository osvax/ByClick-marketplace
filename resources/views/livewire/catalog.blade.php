<div>
    {{-- Хлебные крошки и заголовок зависят от выбранной категории --}}
    <x-site.breadcrumbs :items="$this->breadcrumbs" />

    <div class="va_page-head">
        <h1 class="va_page-title">{{ $this->pageTitle }}</h1>
        <p class="va_page-sub">{{ $this->pageSubtitle }}</p>
    </div>

    <div class="va_catalog-layout">
        {{-- Панель фильтров: на мобильных превращается в выдвижной drawer --}}
        <aside class="va_filters @if ($filtersOpen) is-open @endif" aria-label="Фильтры">
            <div class="va_filters__overlay" wire:click="closeFilters"></div>

            <div class="va_filters__panel">
                <div class="va_filters__head">
                    <h2 class="va_filters__title">Фильтры</h2>
                    <button class="va_icon-btn" type="button" aria-label="Закрыть фильтры" wire:click="closeFilters">
                        <svg class="va_icon" aria-hidden="true"><use href="#va-i-close" /></svg>
                    </button>
                </div>

                <form class="va_filters__form" wire:submit.prevent>
                    <fieldset class="va_filter">
                        <legend class="va_filter__legend">Категория</legend>

                        <label class="va_filter__option">
                            <input type="radio" name="category" value="" wire:model.live="category" />
                            <span>Все категории</span>
                        </label>

                        @foreach ($this->categories as $category)
                            <label class="va_filter__option" wire:key="filter-category-{{ $category->id }}">
                                <input type="radio" name="category" value="{{ $category->slug }}" wire:model.live="category" />
                                <span>
                                    {{ $category->name }}
                                    <span class="va_filter__count">{{ $this->categoryCounts[$category->id] ?? 0 }}</span>
                                </span>
                            </label>
                        @endforeach
                    </fieldset>

                    {{-- Подкатегории появляются только после выбора раздела --}}
                    @if ($selected = $this->selectedCategory)
                        @if ($selected->children->isNotEmpty())
                            <fieldset class="va_filter">
                                <legend class="va_filter__legend">Подкатегория</legend>

                                <label class="va_filter__option">
                                    <input type="radio" name="sub" value="" wire:model.live="sub" />
                                    <span>Все</span>
                                </label>

                                @foreach ($selected->children as $child)
                                    <label class="va_filter__option" wire:key="filter-sub-{{ $child->id }}">
                                        <input type="radio" name="sub" value="{{ $child->slug }}" wire:model.live="sub" />
                                        <span>{{ $child->name }}</span>
                                    </label>
                                @endforeach
                            </fieldset>
                        @endif
                    @endif

                    {{-- Цена: внутри храним копейки, пользователю показываем BYN --}}
                    @php
                        $bounds = $this->priceBounds;
                        $min = $this->minPrice ?? $bounds['min'];
                        $max = $this->maxPrice ?? $bounds['max'];
                        $span = max(1, $bounds['max'] - $bounds['min']);
                        $left = (($min - $bounds['min']) / $span) * 100;
                        $right = 100 - (($max - $bounds['min']) / $span) * 100;
                    @endphp

                    <fieldset class="va_filter">
                        <legend class="va_filter__legend">Цена, {{ config('marketplace.currency', 'BYN') }}</legend>

                        <div class="va_range">
                            <div class="va_range__values">
                                <output class="va_range__value">{{ $this->formatPrice($min) }}</output>
                                <output class="va_range__value">{{ $this->formatPrice($max) }}</output>
                            </div>

                            <div class="va_range__slider">
                                <span class="va_range__track"></span>
                                <span class="va_range__fill" style="left: {{ $left }}%; right: {{ $right }}%"></span>

                                <input
                                    type="range"
                                    min="{{ $bounds['min'] }}"
                                    max="{{ $bounds['max'] }}"
                                    step="100"
                                    value="{{ $min }}"
                                    data-va-range-min-input
                                    aria-label="Минимальная цена"
                                    wire:model.live.debounce.300ms.number="minPrice"
                                />
                                <input
                                    type="range"
                                    min="{{ $bounds['min'] }}"
                                    max="{{ $bounds['max'] }}"
                                    step="100"
                                    value="{{ $max }}"
                                    data-va-range-max-input
                                    aria-label="Максимальная цена"
                                    wire:model.live.debounce.300ms.number="maxPrice"
                                />
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="va_filter">
                        <legend class="va_filter__legend">Рейтинг</legend>

                        @foreach (['' => 'Любой', '4.5' => '4.5 и выше', '4.8' => '4.8 и выше'] as $value => $label)
                            <label class="va_filter__option" wire:key="filter-rating-{{ $value ?: 'any' }}">
                                <input type="radio" name="rating" value="{{ $value }}" wire:model.live="rating" />
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </fieldset>

                    <fieldset class="va_filter">
                        <legend class="va_filter__legend">Дополнительно</legend>

                        <label class="va_filter__option">
                            <input type="checkbox" wire:click="toggleSale" @checked($tag === 'sale') />
                            <span>Только со скидкой</span>
                        </label>

                        <label class="va_filter__option">
                            <input type="checkbox" wire:model.live="inStock" />
                            <span>В наличии</span>
                        </label>
                    </fieldset>

                    <div class="va_filters__foot">
                        <button class="va_btn va_btn--outline" type="button" wire:click="resetFilters">Сбросить</button>
                        <button class="va_btn va_btn--primary" type="button" wire:click="closeFilters">
                            Показать {{ $this->products->total() }}
                        </button>
                    </div>
                </form>
            </div>
        </aside>

        <div class="va_catalog-main">
            <div class="va_toolbar">
                <button class="va_btn va_btn--outline va_toolbar__filters" type="button" wire:click="openFilters">
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-filter" /></svg>
                    Фильтры
                </button>

                <p class="va_toolbar__count">
                    <b>{{ $this->products->total() }}</b>
                    <x-site.plural
                        :count="$this->products->total()"
                        one="товар"
                        few="товара"
                        many="товаров"
                    />
                </p>

                <label class="va_toolbar__sort">
                    <span class="va_visually-hidden">Сортировка</span>
                    <select class="va_select" wire:model.live="sort" aria-label="Сортировка товаров">
                        @foreach (\App\Livewire\Catalog::SORTS as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            {{-- Активные фильтры --}}
            @if (count($this->activeFilters) > 0)
                <div class="va_chips">
                    @foreach ($this->activeFilters as $filter)
                        <span class="va_chip" wire:key="chip-{{ $filter['key'] }}">
                            {{ $filter['label'] }}
                            <button
                                class="va_chip__remove"
                                type="button"
                                aria-label="Убрать фильтр «{{ $filter['label'] }}»"
                                wire:click="removeFilter('{{ $filter['key'] }}')"
                            >
                                <svg class="va_icon" aria-hidden="true"><use href="#va-i-close" /></svg>
                            </button>
                        </span>
                    @endforeach

                    <button class="va_chip" type="button" wire:click="resetFilters">Сбросить всё</button>
                </div>
            @endif

            @if ($this->products->isEmpty())
                <div class="va_catalog-empty">
                    <span class="va_catalog-empty__icon">
                        <svg class="va_icon" aria-hidden="true"><use href="#va-i-search" /></svg>
                    </span>
                    <p class="va_catalog-empty__title">Ничего не нашлось</p>
                    <p class="va_catalog-empty__text">Попробуйте изменить фильтры или сбросить их — в каталоге точно есть что-то интересное.</p>
                    <button class="va_btn va_btn--primary" type="button" wire:click="resetFilters">Сбросить фильтры</button>
                </div>
            @else
                <div class="va_products">
                    @foreach ($this->products as $product)
                        <x-site.product-card :product="$product" wire:key="product-{{ $product->id }}" />
                    @endforeach
                </div>

                {{ $this->products->links('pagination.marketplace') }}
            @endif
        </div>
    </div>
</div>
