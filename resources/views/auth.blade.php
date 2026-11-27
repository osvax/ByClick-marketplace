<x-layouts.app
    title="{{ request()->routeIs('register') ? 'Регистрация' : 'Вход' }} — ByClick"
    description="Вход и регистрация на ByClick: заказы, избранное и корзина сохраняются в вашем аккаунте."
    :canonical="request()->routeIs('register') ? route('register') : route('login')"
>
    <livewire:auth-page />
</x-layouts.app>
