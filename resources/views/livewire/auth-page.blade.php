@php
    $isLogin = $tab === 'login';
@endphp

<div class="va_container">
    <x-site.breadcrumbs :items="[['label' => $isLogin ? 'Вход' : 'Регистрация', 'url' => null]]" />

    <div class="va_auth">
        <div class="va_auth__intro">
            <h1>{{ $isLogin ? 'Вход в ByClick' : 'Регистрация в ByClick' }}</h1>
            <p>Сохраняйте корзину между устройствами, следите за заказами и получайте бонусы за отзывы.</p>

            <ul class="va_auth__benefits">
                <li>
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-check" /></svg>
                    Заказы и история покупок в одном месте
                </li>
                <li>
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-check" /></svg>
                    Бонусы до 10% с каждой покупки
                </li>
                <li>
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-check" /></svg>
                    Ранний доступ к распродажам
                </li>
            </ul>
        </div>

        <div class="va_auth__card">
            <div class="va_auth-tabs" role="tablist" aria-label="Вход или регистрация">
                <button
                    type="button"
                    role="tab"
                    id="va-auth-tab-login"
                    aria-selected="{{ $isLogin ? 'true' : 'false' }}"
                    aria-controls="va-auth-panel-login"
                    wire:click="setTab('login')"
                >Вход</button>
                <button
                    type="button"
                    role="tab"
                    id="va-auth-tab-register"
                    aria-selected="{{ $isLogin ? 'false' : 'true' }}"
                    aria-controls="va-auth-panel-register"
                    wire:click="setTab('register')"
                >Регистрация</button>
            </div>

            @if ($isLogin)
                <div id="va-auth-panel-login" role="tabpanel" aria-labelledby="va-auth-tab-login">
                    <form class="va_auth-form" wire:submit="login">
                        <label class="va_field">
                            E-mail
                            <input
                                class="va_input"
                                type="email"
                                wire:model="email"
                                autocomplete="email"
                                placeholder="you@example.com"
                            />
                            @error('email')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="va_field">
                            Пароль
                            <input
                                class="va_input"
                                type="password"
                                wire:model="password"
                                autocomplete="current-password"
                                placeholder="Минимум 8 символов"
                            />
                            @error('password')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="va_check">
                            <input type="checkbox" wire:model="remember" />
                            <span>Запомнить меня на этом устройстве</span>
                        </label>

                        <button class="va_btn va_btn--primary va_btn--block" type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>Войти</span>
                            <span wire:loading>Входим…</span>
                        </button>

                        <div class="va_auth-links">
                            <a href="{{ route('password.request') }}">Забыли пароль?</a>
                            <a href="{{ url('/help') }}">Нужна помощь?</a>
                        </div>
                    </form>

                    <div class="va_auth-social">
                        <button
                            class="va_btn va_btn--outline"
                            type="button"
                            wire:click="demoToast('Демо-режим: вход через соцсети не подключён')"
                        >ВКонтакте</button>
                        <button
                            class="va_btn va_btn--outline"
                            type="button"
                            wire:click="demoToast('Демо-режим: вход через соцсети не подключён')"
                        >Telegram</button>
                    </div>
                </div>
            @else
                <div id="va-auth-panel-register" role="tabpanel" aria-labelledby="va-auth-tab-register">
                    <form class="va_auth-form" wire:submit="register">
                        <label class="va_field">
                            Имя
                            <input
                                class="va_input"
                                type="text"
                                wire:model="name"
                                autocomplete="name"
                                placeholder="Как к вам обращаться"
                            />
                            @error('name')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="va_field">
                            E-mail
                            <input
                                class="va_input"
                                type="email"
                                wire:model="registerEmail"
                                autocomplete="email"
                                placeholder="you@example.com"
                            />
                            @error('registerEmail')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="va_field">
                            Телефон
                            <input
                                class="va_input"
                                type="tel"
                                wire:model="phone"
                                autocomplete="tel"
                                placeholder="+375 29 000-00-00"
                            />
                            @error('phone')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="va_field">
                            Пароль
                            <input
                                class="va_input"
                                type="password"
                                wire:model="registerPassword"
                                autocomplete="new-password"
                                placeholder="Минимум 8 символов"
                            />
                            @error('registerPassword')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="va_field">
                            Повторите пароль
                            <input
                                class="va_input"
                                type="password"
                                wire:model="registerPasswordConfirmation"
                                autocomplete="new-password"
                                placeholder="Ещё раз"
                            />
                            @error('registerPasswordConfirmation')
                                <span class="va_field__error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="va_check">
                            <input type="checkbox" wire:model="agree" />
                            <span>
                                Принимаю
                                <a href="#" wire:click.prevent="demoToast('Демо-режим: условия появятся позже')">условия</a>
                                и
                                <a href="#" wire:click.prevent="demoToast('Демо-режим: политика появится позже')">политику конфиденциальности</a>
                            </span>
                        </label>
                        @error('agree')
                            <span class="va_field__error">{{ $message }}</span>
                        @enderror

                        <button class="va_btn va_btn--primary va_btn--block" type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>Создать аккаунт</span>
                            <span wire:loading>Создаём…</span>
                        </button>

                        <div class="va_auth-links">
                            <span>Уже есть аккаунт?</span>
                            <a href="#" wire:click.prevent="setTab('login')">Войти</a>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
