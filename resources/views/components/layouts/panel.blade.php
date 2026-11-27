@props([
    'title',
    'heading',
    'description' => null,
    'section' => 'admin',
])

<x-layouts.app :title="$title" :description="$description ?? $heading">
    <div class="va_container">
        <x-site.breadcrumbs :items="[
            ['label' => $section === 'admin' ? 'Админка' : 'Кабинет продавца', 'url' => null],
            ['label' => $heading, 'url' => null],
        ]" />

        <div class="va_profile">
            <aside class="va_profile__aside">
                <div class="va_profile-card">
                    <div class="va_profile-card__avatar" aria-hidden="true">{{ auth()->user()->initials() }}</div>
                    <p class="va_profile-card__name">{{ auth()->user()->name }}</p>
                    <p class="va_profile-card__email">{{ auth()->user()->email }}</p>
                    <p class="va_profile-card__role">{{ auth()->user()->role->label() }}</p>
                </div>

                <details class="va_profile-menu" open>
                    <summary class="va_profile-menu__summary">
                        Меню
                        <svg class="va_icon" aria-hidden="true"><use href="#va-i-chevron-down" /></svg>
                    </summary>

                    <nav class="va_profile-menu__body" aria-label="Меню панели">
                        @if ($section === 'admin')
                            <div class="va_profile-group">
                                <p class="va_profile-group__title">Управление</p>
                                <ul>
                                    <li><a class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">Обзор</a></li>
                                    <li><a class="{{ request()->routeIs('admin.requests') ? 'is-active' : '' }}" href="{{ route('admin.requests') }}">Заявки продавцов</a></li>
                                    <li><a class="{{ request()->routeIs('admin.shops') ? 'is-active' : '' }}" href="{{ route('admin.shops') }}">Магазины</a></li>
                                    <li><a class="{{ request()->routeIs('admin.orders') ? 'is-active' : '' }}" href="{{ route('admin.orders') }}">Заказы</a></li>
                                    <li><a class="{{ request()->routeIs('admin.products') ? 'is-active' : '' }}" href="{{ route('admin.products') }}">Товары</a></li>
                                    <li><a class="{{ request()->routeIs('admin.categories') ? 'is-active' : '' }}" href="{{ route('admin.categories') }}">Категории</a></li>
                                    <li><a class="{{ request()->routeIs('admin.users') ? 'is-active' : '' }}" href="{{ route('admin.users') }}">Пользователи</a></li>
                                    <li><a class="{{ request()->routeIs('admin.promocodes') ? 'is-active' : '' }}" href="{{ route('admin.promocodes') }}">Промокоды</a></li>
                                </ul>
                            </div>
                        @else
                            <div class="va_profile-group">
                                <p class="va_profile-group__title">Магазин</p>
                                <ul>
                                    <li><a class="{{ request()->routeIs('seller.dashboard') ? 'is-active' : '' }}" href="{{ route('seller.dashboard') }}">Обзор</a></li>
                                    <li><a class="{{ request()->routeIs('seller.products') ? 'is-active' : '' }}" href="{{ route('seller.products') }}">Товары и остатки</a></li>
                                    <li><a class="{{ request()->routeIs('seller.orders') ? 'is-active' : '' }}" href="{{ route('seller.orders') }}">Заказы</a></li>
                                </ul>
                            </div>
                        @endif

                        <div class="va_profile-group">
                            <p class="va_profile-group__title">Сайт</p>
                            <ul>
                                <li><a href="{{ route('profile') }}">Личный кабинет</a></li>
                                <li><a href="{{ route('home') }}">На главную</a></li>
                            </ul>
                        </div>
                    </nav>
                </details>
            </aside>

            <div class="va_profile__main">
                <div class="va_page-head">
                    <h1 class="va_page-title">{{ $heading }}</h1>
                    @if ($description)
                        <p class="va_page-sub">{{ $description }}</p>
                    @endif
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</x-layouts.app>
