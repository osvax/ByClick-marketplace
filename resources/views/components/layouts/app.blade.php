@props([
    'title' => 'ByClick — маркетплейс нужных вещей',
    'description' => 'ByClick — маркетплейс: электроника, одежда, товары для дома, красоты и спорта. Доставка от 1 дня, возврат 30 дней, бонусы за покупки.',
    'canonical' => null,
    'ogType' => 'website',
    'ogImage' => null,
    'jsonLd' => null,
])

@php
    $defaultJsonLd = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                'name' => 'ByClick',
                'url' => url('/'),
                'logo' => asset('brand/byclick-mark.svg'),
            ],
            [
                '@type' => 'WebSite',
                'name' => 'ByClick',
                'url' => url('/'),
                'inLanguage' => 'ru-RU',
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => route('catalog').'?q={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ],
        ],
    ];

    $schema = $jsonLd ?? $defaultJsonLd;
    $schemaJson = json_encode(
        $schema,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT,
    );
@endphp

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

        <meta property="og:site_name" content="ByClick" />
        <meta property="og:locale" content="ru_RU" />
        <meta property="og:type" content="{{ $ogType }}" />
        <meta property="og:title" content="{{ $title }}" />
        <meta property="og:description" content="{{ $description }}" />
        @if ($canonical)
            <meta property="og:url" content="{{ $canonical }}" />
        @endif
        <meta property="og:image" content="{{ $ogImage ?? asset('brand/byclick-mark.svg') }}" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="{{ $title }}" />
        <meta name="twitter:description" content="{{ $description }}" />
        <meta name="twitter:image" content="{{ $ogImage ?? asset('brand/byclick-mark.svg') }}" />

        @if ($schemaJson)
            <script type="application/ld+json">{!! $schemaJson !!}</script>
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
