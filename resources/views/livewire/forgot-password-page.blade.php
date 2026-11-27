<div class="va_container">
    <x-site.breadcrumbs :items="[
        ['label' => 'Вход', 'url' => route('login')],
        ['label' => 'Восстановление пароля', 'url' => null],
    ]" />

    <div class="va_auth">
        <div class="va_auth__intro">
            <h1>Восстановление пароля</h1>
            <p>Укажите e-mail, на который зарегистрирован аккаунт. Мы отправим ссылку для создания нового пароля.</p>

            <ul class="va_auth__benefits">
                <li>
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-check" /></svg>
                    Ссылка действует 60 минут
                </li>
                <li>
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-check" /></svg>
                    Пароль можно изменить в любой момент
                </li>
            </ul>
        </div>

        <div class="va_auth__card">
            @if ($status)
                <p class="va_summary__message">{{ $status }}</p>
            @endif

            <form class="va_auth-form" wire:submit="sendResetLink">
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

                <button class="va_btn va_btn--primary va_btn--block" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>Отправить ссылку</span>
                    <span wire:loading>Отправляем…</span>
                </button>

                <div class="va_auth-links">
                    <a href="{{ route('login') }}">Вернуться ко входу</a>
                </div>
            </form>
        </div>
    </div>
</div>
