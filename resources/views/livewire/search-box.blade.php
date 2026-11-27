@php
    $scale = (int) config('marketplace.currency_scale', 100);
    $currency = config('marketplace.currency', 'BYN');
    $money = fn (int $amount): string => number_format($amount / $scale, 2, ',', ' ').' '.$currency;
@endphp

<form
    class="va_search"
    role="search"
    action="{{ route('catalog') }}"
    method="get"
    autocomplete="off"
    wire:submit="search"
    x-data
    x-on:click.outside="$wire.closeSuggestions()"
    x-on:keydown.escape="$wire.closeSuggestions()"
>
    <label class="va_visually-hidden" for="va-search-input">Поиск по каталогу</label>

    <div class="va_search__field">
        <svg class="va_search__icon va_icon" aria-hidden="true"><use href="#va-i-search" /></svg>
        <input
            class="va_search__input"
            id="va-search-input"
            type="search"
            name="q"
            wire:model.live.debounce.300ms="query"
            x-on:focus="$wire.openSuggestions()"
            placeholder="Искать товары, бренды или категории"
            role="combobox"
            aria-autocomplete="list"
            aria-controls="va-search-suggest"
            aria-expanded="{{ $open ? 'true' : 'false' }}"
        />
        <button class="va_search__submit" type="submit" aria-label="Найти">
            <svg class="va_icon" aria-hidden="true"><use href="#va-i-search" /></svg>
        </button>
    </div>

    @if ($open)
        <div class="va_search__suggest" id="va-search-suggest" role="listbox" aria-label="Подсказки поиска">
            @if ($suggestions->isEmpty())
                <p class="va_suggest__empty">
                    Ничего не нашлось. Попробуйте «наушники», «лампа» или «дом».
                </p>
            @else
                @foreach ($suggestions as $product)
                    <a
                        class="va_suggest__item"
                        href="{{ url('/product/'.$product->slug) }}"
                        role="option"
                    >
                        <img
                            class="va_suggest__img"
                            src="{{ $product->mainImage?->url() ?? asset('images/products/placeholder.svg') }}"
                            alt=""
                            width="44"
                            height="44"
                            loading="lazy"
                        />
                        <span class="va_suggest__body">
                            <span class="va_suggest__name">{{ $product->name }}</span>
                            <span class="va_suggest__meta">
                                {{ $product->category?->name }}
                                @if ($product->brand)
                                    · {{ $product->brand }}
                                @endif
                            </span>
                        </span>
                        <span class="va_suggest__price">{{ $money($product->price) }}</span>
                    </a>
                @endforeach
            @endif

            @if ($term !== '')
                <a class="va_suggest__all" href="{{ route('catalog', ['q' => $term]) }}">
                    Показать все результаты
                </a>
            @endif
        </div>
    @endif
</form>
