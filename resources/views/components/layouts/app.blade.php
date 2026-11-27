@props([
    'title' => 'ByClick — маркетплейс нужных вещей',
    'description' => 'ByClick — маркетплейс: электроника, одежда, товары для дома, красоты и спорта. Доставка от 1 дня, возврат 30 дней, бонусы за покупки.',
    'canonical' => null,
])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#6A3BFF" />

        <title>{{ $title }}</title>
        <meta name="description" content="{{ $description }}" />
        @if ($canonical)
            <link rel="canonical" href="{{ $canonical }}" />
        @endif

        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml" />

        @vite(['resources/scss/main.scss', 'resources/js/app.js'])
        @livewireStyles
    </head>

    <body class="va_page">
        <a class="va_skip-link" href="#va-main">Перейти к содержимому</a>

        <x-site.sprite />
        <x-site.header />

        <main class="va_main" id="va-main">
            {{ $slot }}
        </main>

        <x-site.footer />
        <x-site.menu />
        <x-site.cart-drawer />
        <livewire:favorite-sync />
        <x-site.toast />

        @livewireScripts
    </body>
</html>
