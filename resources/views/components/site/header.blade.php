<header class="va_header" data-va-header>
    <div class="va_topbar">
        <div class="va_container va_topbar__inner">
            <button class="va_topbar__city" type="button" data-va-demo-toast="Выбор города появится в следующей версии">
                <svg class="va_icon" aria-hidden="true"><use href="#va-i-pin" /></svg>
                <span>Минск</span>
            </button>
            <nav class="va_topbar__nav" aria-label="Сервисы">
                <a class="va_topbar__link" href="{{ url('/delivery') }}">Пункты выдачи</a>
                <a class="va_topbar__link" href="{{ url('/seller') }}">Стать продавцом</a>
                <a class="va_topbar__link" href="{{ url('/help') }}">Помощь</a>
                <a class="va_topbar__link" href="{{ url('/orders') }}">Статус заказа</a>
            </nav>
        </div>
    </div>

    <div class="va_header__main">
        <div class="va_container va_header__inner">
            <button class="va_burger" type="button" aria-label="Открыть меню" aria-expanded="false" data-va-menu-open>
                <svg class="va_icon" aria-hidden="true"><use href="#va-i-menu" /></svg>
            </button>

            <a class="va_logo" href="{{ route('home') }}" aria-label="ByClick — на главную">
                <img class="va_logo__mark" src="{{ asset('brand/byclick-mark.svg') }}" alt="" width="38" height="38" />
                <span class="va_logo__text">
                    <span class="va_logo__name"><span class="va_logo__by">By</span>Click</span>
                    <span class="va_logo__tagline">Купи в один клик!</span>
                </span>
            </a>

            <div class="va_catalog-wrap">
                <button
                    class="va_btn va_btn--catalog"
                    type="button"
                    aria-expanded="false"
                    aria-controls="va-catalog-panel"
                    data-va-catalog-btn
                >
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-menu" /></svg>
                    <span>Каталог</span>
                    <svg class="va_icon va_icon--down" aria-hidden="true"><use href="#va-i-chevron-down" /></svg>
                </button>

                <div class="va_catalog-panel" id="va-catalog-panel" hidden data-va-catalog-panel>
                    <div class="va_catalog-panel__layout">
                        <div class="va_catalog-panel__main" role="tablist" aria-label="Категории каталога" data-va-catalog-main>
                            @foreach ($categories as $index => $category)
                                <button
                                    class="va_catalog-panel__cat @if ($index === 0) is-active @endif"
                                    type="button"
                                    role="tab"
                                    id="va-cat-tab-{{ $category->slug }}"
                                    aria-controls="va-catalog-sub"
                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                    tabindex="{{ $index === 0 ? 0 : -1 }}"
                                    data-va-cat-index="{{ $index }}"
                                >
                                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-{{ $category->icon ?? 'grid' }}" /></svg>
                                    <span>{{ $category->name }}</span>
                                    <svg class="va_icon va_catalog-panel__chevron" aria-hidden="true"><use href="#va-i-chevron-right" /></svg>
                                </button>
                            @endforeach
                        </div>

                        <div
                            class="va_catalog-panel__sub"
                            id="va-catalog-sub"
                            role="tabpanel"
                            aria-label="Подкатегории"
                            data-va-catalog-sub
                        >
                            @foreach ($categories as $index => $category)
                                <div data-va-cat-panel @if ($index !== 0) hidden @endif>
                                    <div class="va_catalog-panel__subhead">
                                        <h3>{{ $category->name }}</h3>
                                        <a class="va_catalog-panel__all" href="{{ url('/catalog?category='.$category->slug) }}">
                                            Все товары
                                            <svg class="va_icon" aria-hidden="true"><use href="#va-i-arrow-right" /></svg>
                                        </a>
                                    </div>
                                    <div class="va_catalog-panel__sublist">
                                        @foreach ($category->children as $child)
                                            <a
                                                class="va_catalog-panel__subitem"
                                                href="{{ url('/catalog?category='.$category->slug.'&sub='.$child->slug) }}"
                                            >{{ $child->name }}</a>
                                        @endforeach
                                    </div>
                                    <p class="va_catalog-panel__note">Более 1,2 млн товаров от проверенных продавцов. Доставка от 1 дня.</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Поиск с живыми подсказками на Livewire --}}
            <livewire:search-box />

            <div class="va_actions">
                @auth
                    <div
                        class="va_account"
                        x-data="{ open: false }"
                        x-on:click.outside="open = false"
                        x-on:keydown.escape="open = false"
                    >
                        <button
                            class="va_action"
                            type="button"
                            x-on:click="open = ! open"
                            :aria-expanded="open"
                            aria-haspopup="true"
                            aria-label="Меню профиля"
                        >
                            <svg class="va_icon" aria-hidden="true"><use href="#va-i-user" /></svg>
                            <span class="va_action__label">Профиль</span>
                        </button>

                        <div class="va_account__panel" x-show="open" x-cloak>
                            <p class="va_account__name">{{ auth()->user()->name }}</p>
                            <p class="va_account__email">{{ auth()->user()->email }}</p>
                            <a class="va_account__link" href="{{ url('/profile') }}">Профиль</a>
                            <a class="va_account__link" href="{{ url('/orders') }}">Мои заказы</a>
                            @if (auth()->user()->isSeller() || auth()->user()->isAdmin())
                                <a class="va_account__link" href="{{ route('seller.dashboard') }}">Кабинет продавца</a>
                            @endif
                            @if (auth()->user()->isAdmin())
                                <a class="va_account__link" href="{{ route('admin.dashboard') }}">Админка</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="va_account__logout" type="submit">Выйти</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a class="va_action" href="{{ route('login') }}">
                        <svg class="va_icon" aria-hidden="true"><use href="#va-i-user" /></svg>
                        <span class="va_action__label">Войти</span>
                    </a>
                @endauth

                <a class="va_action va_action--orders" href="{{ auth()->check() ? url('/orders') : route('login') }}">
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-check" /></svg>
                    <span class="va_action__label">Заказы</span>
                </a>
                <a class="va_action va_action--fav" href="{{ route('favorites') }}">
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-heart" /></svg>
                    <span class="va_action__label">Избранное</span>
                    <span class="va_action__count" data-va-fav-count @if ($favoritesCount === 0) hidden @endif>{{ $favoritesCount }}</span>
                </a>
                <button class="va_action va_action--cart" type="button" data-va-cart-open aria-label="Открыть корзину">
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-cart" /></svg>
                    <span class="va_action__label">Корзина</span>
                    <span class="va_action__count" data-va-cart-count @if ($cartCount === 0) hidden @endif>{{ $cartCount }}</span>
                </button>
            </div>
        </div>
    </div>

    <nav class="va_nav" aria-label="Категории">
        <div class="va_container">
            <ul class="va_nav__list">
                @foreach ($categories as $category)
                    <li>
                        <a class="va_nav__link" href="{{ url('/catalog?category='.$category->slug) }}">{{ $category->name }}</a>
                    </li>
                @endforeach
                <li><a class="va_nav__link va_nav__link--accent" href="{{ url('/catalog?tag=sale') }}">Скидки</a></li>
            </ul>
        </div>
    </nav>
</header>
