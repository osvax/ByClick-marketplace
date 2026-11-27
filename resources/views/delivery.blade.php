@php
    $scale = (int) config('marketplace.currency_scale', 100);
    $currency = config('marketplace.currency', 'BYN');
    $money = fn (int $amount): string => number_format($amount / $scale, 2, ',', ' ').' '.$currency;
    $courierPrice = (int) config('marketplace.delivery.courier_price', 500);
    $freeFrom = (int) config('marketplace.delivery.free_from', 10000);
@endphp

<x-layouts.app
    title="Доставка — ByClick"
    description="Доставка ByClick: курьером, в пункт выдачи или постамат. Пункты выдачи в городах Беларуси, бесплатно от 100 Руб."
    :canonical="route('delivery')"
>
    <div class="va_container">
        <x-site.breadcrumbs :items="[['label' => 'Доставка', 'url' => null]]" />

        <section class="va_info-hero">
            <h1>Доставка за 1 день</h1>
            <p>
                Курьером, в пункт выдачи или постамат — по всей Беларуси.
                Бесплатно от {{ $money($freeFrom) }}, отслеживание в личном кабинете.
            </p>
            <a class="va_btn va_btn--light" href="{{ route('catalog') }}">Выбрать товары</a>
        </section>

        <section class="va_section" aria-labelledby="va-delivery-title">
            <header class="va_section__head">
                <h2 class="va_section__title" id="va-delivery-title">Способы доставки</h2>
            </header>

            <div class="va_features">
                <article class="va_feature">
                    <span class="va_feature__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-truck" /></svg></span>
                    <h3 class="va_feature__title">Курьером</h3>
                    <p class="va_feature__text">Завтра с 10:00 до 22:00. Позвоним за час до приезда.</p>
                    <p class="va_feature__price">
                        {{ $money($courierPrice) }} · бесплатно от {{ $money($freeFrom) }}
                    </p>
                </article>

                <article class="va_feature">
                    <span class="va_feature__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-pin" /></svg></span>
                    <h3 class="va_feature__title">Пункт выдачи</h3>
                    <p class="va_feature__text">Более 2 000 точек. Заказ хранится 7 дней, примерка — 15 минут.</p>
                    <p class="va_feature__price">бесплатно</p>
                </article>

                <article class="va_feature">
                    <span class="va_feature__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-home" /></svg></span>
                    <h3 class="va_feature__title">Постамат</h3>
                    <p class="va_feature__text">Круглосуточно, по коду из приложения. Хранение — 3 дня.</p>
                    <p class="va_feature__price">бесплатно</p>
                </article>
            </div>
        </section>

        <section class="va_section" aria-labelledby="va-pickup-title">
            <header class="va_section__head">
                <h2 class="va_section__title" id="va-pickup-title">Популярные пункты выдачи</h2>
                <button
                    class="va_section__link"
                    type="button"
                    data-va-demo-toast="Карта пунктов выдачи появится в следующей версии"
                >Показать на карте</button>
            </header>

            @if ($points->isEmpty())
                <p class="va_feature__text">Пункты выдачи скоро появятся.</p>
            @else
                <div class="va_features">
                    @foreach ($points as $point)
                        <article class="va_panel">
                            <h3 class="va_panel__title">{{ $point->city }}, {{ $point->address }}</h3>
                            <p class="va_feature__text">
                                {{ $point->schedule ?: 'Уточните график работы' }}
                                · {{ $point->type->label() }}
                            </p>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="va_section" aria-labelledby="va-delivery-faq-title">
            <header class="va_section__head">
                <h2 class="va_section__title" id="va-delivery-faq-title">Частые вопросы о доставке</h2>
                <a class="va_section__link" href="{{ route('help') }}">
                    Вся помощь
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-arrow-right" /></svg>
                </a>
            </header>

            <div class="va_faq">
                <details class="va_faq__item" open>
                    <summary>Можно ли изменить адрес после оформления?</summary>
                    <div class="va_faq__content">
                        <p>Да, пока заказ не передан курьеру. Напишите в поддержку — поменяем адрес или способ получения.</p>
                    </div>
                </details>
                <details class="va_faq__item">
                    <summary>Что если меня не будет дома?</summary>
                    <div class="va_faq__content">
                        <p>Курьер позвонит и согласует новое время. Можно попросить оставить заказ в пункте выдачи рядом с домом.</p>
                    </div>
                </details>
                <details class="va_faq__item">
                    <summary>Доставляете ли в другие города?</summary>
                    <div class="va_faq__content">
                        <p>Да, по всей Беларуси. Срок зависит от региона: обычно 1–3 дня.</p>
                    </div>
                </details>
            </div>
        </section>
    </div>
</x-layouts.app>
