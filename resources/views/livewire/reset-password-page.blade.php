<div class="va_container">
    <x-site.breadcrumbs :items="[
        ['label' => 'Вход', 'url' => route('login')],
        ['label' => 'Новый пароль', 'url' => null],
    ]" />

    <div class="va_auth">
        <div class="va_auth__intro">
            <h1>Новый пароль</h1>
            <p>Придумайте новый пароль для аккаунта. После сохранения вы сразу войдёте в систему.</p>

            <ul class="va_auth__benefits">
                <li>
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-check" /></svg>
                    Минимум 8 символов
                </li>
                <li>
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-check" /></svg>
                    Не используйте пароль от других сайтов
                </li>
            </ul>
        </div>

        <div class="va_auth__card">
            <form class="va_auth-form" wire:submit="resetPassword">
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
                    Новый пароль
                    <input
                        class="va_input"
                        type="password"
                        wire:model="password"
                        autocomplete="new-password"
                        placeholder="Минимум 8 символов"
                    />
                    @error('password')
                        <span class="va_field__error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="va_field">
                    Повторите пароль
                    <input
                        class="va_input"
                        type="password"
                        wire:model="passwordConfirmation"
                        autocomplete="new-password"
                        placeholder="Ещё раз"
                    />
                </label>

                <button class="va_btn va_btn--primary va_btn--block" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>Сохранить пароль</span>
                    <span wire:loading>Сохраняем…</span>
                </button>

                <div class="va_auth-links">
                    <a href="{{ route('password.request') }}">Запросить новую ссылку</a>
                    <a href="{{ route('login') }}">Вернуться ко входу</a>
                </div>
            </form>
        </div>
    </div>
</div>
