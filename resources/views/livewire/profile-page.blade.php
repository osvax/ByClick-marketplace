@php
    $scale = (int) config('marketplace.currency_scale', 100);
    $currency = config('marketplace.currency', 'BYN');
    $money = fn (int $amount): string => number_format($amount / $scale, 2, ',', ' ').' '.$currency;
@endphp

<div class="va_container">
    <x-site.breadcrumbs :items="[['label' => 'Личный кабинет', 'url' => null]]" />

    <div class="va_profile" id="top">
        <aside class="va_profile__aside">
            <div class="va_profile-card">
                <div class="va_profile-card__avatar" aria-hidden="true">{{ $user->initials() }}</div>
                <p class="va_profile-card__name">{{ $user->name }}</p>
                <p class="va_profile-card__email">{{ $user->email }}</p>
                <a class="va_profile-card__edit" href="#settings">Изменить профиль</a>
            </div>

            <details class="va_profile-menu" open>
                <summary class="va_profile-menu__summary">
                    Меню профиля
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-chevron-down" /></svg>
                </summary>
                <nav class="va_profile-menu__body" aria-label="Меню профиля">
                    <div class="va_profile-group">
                        <p class="va_profile-group__title">Покупки</p>
                        <ul>
                            <li><a class="is-active" href="{{ route('profile') }}" aria-current="page">Главная</a></li>
                            <li><a href="{{ route('orders') }}">Мои заказы</a></li>
                            <li>
                                <a href="{{ route('cart') }}">
                                    Корзина
                                    <span class="va_profile-group__badge">{{ $stats['cart'] }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('favorites') }}">
                                    Избранное
                                    <span class="va_profile-group__badge">{{ $stats['favorites'] }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="va_profile-group">
                        <p class="va_profile-group__title">Финансы</p>
                        <ul>
                            <li><a href="#finance">Мои финансы</a></li>
                            <li><a href="{{ route('orders') }}">История покупок</a></li>
                        </ul>
                    </div>

                    <div class="va_profile-group">
                        <p class="va_profile-group__title">Настройки</p>
                        <ul>
                            <li><a href="#settings">Профиль и контакты</a></li>
                            <li><a href="#security">Безопасность</a></li>
                        </ul>
                    </div>

                    <div class="va_profile-group">
                        <p class="va_profile-group__title">Помощь</p>
                        <ul>
                            <li><a href="{{ url('/help') }}">Помощь и поддержка</a></li>
                        </ul>
                    </div>
                </nav>
            </details>
        </aside>

        <div class="va_profile__main">
            <section class="va_profile-banner" aria-labelledby="va-profile-banner-title">
                <div class="va_profile-banner__content">
                    <p class="va_profile-banner__eyebrow">Личный кабинет</p>
                    <h1 id="va-profile-banner-title">Здравствуйте, {{ $user->name }}!</h1>
                    <p>Здесь собраны ваши заказы, избранное и настройки аккаунта. Спасибо, что выбираете ByClick.</p>
                    <a class="va_btn va_btn--light" href="{{ route('orders') }}">Мои заказы</a>
                </div>
                <div class="va_profile-banner__art" aria-hidden="true">
                    <svg class="va_icon"><use href="#va-i-gift" /></svg>
                </div>
            </section>

            <div class="va_profile-stats">
                <div class="va_profile-stat">
                    <span class="va_profile-stat__label">Заказы</span>
                    <strong class="va_profile-stat__value">{{ $stats['orders'] }}</strong>
                </div>
                <div class="va_profile-stat">
                    <span class="va_profile-stat__label">Потрачено</span>
                    <strong class="va_profile-stat__value">{{ $money($stats['spent']) }}</strong>
                </div>
                <div class="va_profile-stat">
                    <span class="va_profile-stat__label">Избранное</span>
                    <strong class="va_profile-stat__value">{{ $stats['favorites'] }}</strong>
                </div>
                <div class="va_profile-stat">
                    <span class="va_profile-stat__label">В корзине</span>
                    <strong class="va_profile-stat__value">{{ $stats['cart'] }}</strong>
                </div>
            </div>

            @if ($recentOrders->isNotEmpty())
                <section class="va_section va_profile-section" aria-labelledby="va-recent-orders">
                    <header class="va_section__head">
                        <h2 class="va_section__title" id="va-recent-orders">Последние заказы</h2>
                        <a class="va_section__link" href="{{ route('orders') }}">
                            Все заказы
                            <svg class="va_icon" aria-hidden="true"><use href="#va-i-arrow-right" /></svg>
                        </a>
                    </header>

                    <div class="va_orders">
                        @foreach ($recentOrders as $order)
                            <x-site.order-card :order="$order" wire:key="profile-order-{{ $order->id }}" />
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="va_section va_profile-section" id="finance" aria-labelledby="va-finance-title">
                <header class="va_section__head">
                    <h2 class="va_section__title" id="va-finance-title">Мои финансы</h2>
                </header>

                <div class="va_finance-card">
                    <div class="va_finance-card__body">
                        <h3>Покупки на ByClick</h3>
                        <p>
                            Всего покупок на <b>{{ $money($stats['spent']) }}</b>,
                            средний чек — <b>{{ $money($stats['average']) }}</b>.
                            @if ($lastOrder)
                                Последний заказ: <b>{{ $lastOrder->number }}</b>.
                            @endif
                        </p>
                        <a class="va_btn va_btn--primary" href="{{ route('orders') }}">История заказов</a>
                    </div>
                    <div class="va_finance-card__art" aria-hidden="true">
                        <svg class="va_icon"><use href="#va-i-card" /></svg>
                        <span>ByClick</span>
                    </div>
                </div>

                <p class="va_profile-note">
                    Оплата и доставка работают в демо-режиме. История заказов сохраняется в вашем аккаунте.
                </p>
            </section>

            <section class="va_section va_profile-section" id="settings" aria-labelledby="va-settings-title">
                <header class="va_section__head">
                    <h2 class="va_section__title" id="va-settings-title">Профиль и контакты</h2>
                </header>

                <form class="va_panel" wire:submit="updateProfile">
                    <div class="va_form-grid">
                        <label class="va_field">
                            Имя
                            <input class="va_input" type="text" wire:model="name" autocomplete="name" />
                            @error('name')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="va_field">
                            Телефон
                            <input class="va_input" type="tel" wire:model="phone" autocomplete="tel" />
                            @error('phone')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="va_field va_form-grid--full">
                            E-mail
                            <input class="va_input" type="email" wire:model="email" autocomplete="email" />
                            @error('email')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>

                    <div>
                        <button class="va_btn va_btn--primary" type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>Сохранить изменения</span>
                            <span wire:loading>Сохраняем…</span>
                        </button>
                    </div>
                </form>
            </section>

            <section class="va_section va_profile-section" id="security" aria-labelledby="va-security-title">
                <header class="va_section__head">
                    <h2 class="va_section__title" id="va-security-title">Безопасность</h2>
                </header>

                <form class="va_panel" wire:submit="updatePassword">
                    <div class="va_form-grid">
                        <label class="va_field va_form-grid--full">
                            Текущий пароль
                            <input class="va_input" type="password" wire:model="currentPassword" autocomplete="current-password" />
                            @error('currentPassword')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="va_field">
                            Новый пароль
                            <input class="va_input" type="password" wire:model="newPassword" autocomplete="new-password" />
                            @error('newPassword')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="va_field">
                            Повторите пароль
                            <input class="va_input" type="password" wire:model="newPasswordConfirmation" autocomplete="new-password" />
                            @error('newPasswordConfirmation')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>

                    <div>
                        <button class="va_btn va_btn--primary" type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>Обновить пароль</span>
                            <span wire:loading>Обновляем…</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
