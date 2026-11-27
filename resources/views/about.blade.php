@php
    $scale = (int) config('marketplace.currency_scale', 100);
    $currency = config('marketplace.currency', 'BYN');
    $money = fn (int $amount): string => number_format($amount / $scale, 2, ',', ' ').' '.$currency;
    $courierPrice = (int) config('marketplace.delivery.courier_price', 500);
    $freeFrom = (int) config('marketplace.delivery.free_from', 10000);
@endphp

<x-layouts.app
    title="О ByClick — маркетплейс нужных вещей"
    description="О ByClick: проверенные продавцы, быстрая доставка по Беларуси, честные цены и поддержка 24/7."
    :canonical="route('about')"
>
    <div class="va_container">
        <x-site.breadcrumbs :items="[['label' => 'О ByClick', 'url' => null]]" />

        <section class="va_info-hero">
            <h1>Маркетплейс, где всё в один клик</h1>
            <p>
                ByClick собирает проверенных продавцов и нужные вещи в одном месте.
                Без бесконечных вкладок: нашёл, щёлкнул, получил.
            </p>
            <a class="va_btn va_btn--light" href="{{ route('catalog') }}">Посмотреть каталог</a>
        </section>

        <div class="va_stats">
            <div class="va_stat">
                <p class="va_stat__value">2 400</p>
                <p class="va_stat__label">продавцов</p>
            </div>
            <div class="va_stat">
                <p class="va_stat__value">1,2 млн</p>
                <p class="va_stat__label">товаров</p>
            </div>
            <div class="va_stat">
                <p class="va_stat__value">300+</p>
                <p class="va_stat__label">городов доставки</p>
            </div>
            <div class="va_stat">
                <p class="va_stat__value">4,8</p>
                <p class="va_stat__label">средний рейтинг</p>
            </div>
        </div>

        <section class="va_section" aria-labelledby="va-about-values">
            <header class="va_section__head">
                <h2 class="va_section__title" id="va-about-values">Во что мы верим</h2>
            </header>

            <div class="va_features">
                <article class="va_feature">
                    <span class="va_feature__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-check" /></svg></span>
                    <h3 class="va_feature__title">Честные цены</h3>
                    <p class="va_feature__text">Показываем реальную цену и историю скидки. Без мелкого шрифта.</p>
                </article>
                <article class="va_feature">
                    <span class="va_feature__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-truck" /></svg></span>
                    <h3 class="va_feature__title">Быстро и рядом</h3>
                    <p class="va_feature__text">Доставка от 1 дня: курьером, в пункт выдачи или постамат по всей Беларуси.</p>
                </article>
                <article class="va_feature">
                    <span class="va_feature__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-support" /></svg></span>
                    <h3 class="va_feature__title">Люди, а не боты</h3>
                    <p class="va_feature__text">Поддержка 24/7 отвечает по-человечески и решает вопрос с первого раза.</p>
                </article>
            </div>
        </section>

        <section class="va_section" aria-labelledby="va-about-contact">
            <header class="va_section__head">
                <h2 class="va_section__title" id="va-about-contact">Контакты</h2>
            </header>

            <div class="va_checkout-layout" id="contacts">
                <div class="va_panel">
                    <h3 class="va_panel__title">Головной офис</h3>
                    <ul class="va_contact-list">
                        <li class="va_contact-item">
                            <svg class="va_icon" aria-hidden="true"><use href="#va-i-pin" /></svg>
                            Минск, пр-т Независимости, 18
                        </li>
                        <li class="va_contact-item">
                            <svg class="va_icon" aria-hidden="true"><use href="#va-i-support" /></svg>
                            <a href="tel:+375290000000">+375 (29) 000-00-00</a>
                        </li>
                        <li class="va_contact-item">
                            <svg class="va_icon" aria-hidden="true"><use href="#va-i-check" /></svg>
                            <a href="mailto:hello@byclick.by">hello@byclick.by</a>
                        </li>
                    </ul>
                    <p class="va_feature__text">Пн–Пт 10:00–19:00, поддержка — круглосуточно.</p>
                </div>

                <div class="va_panel" id="career">
                    <h3 class="va_panel__title">Карьера</h3>
                    <p class="va_feature__text">
                        Мы ищем фронтендеров, аналитиков и менеджеров категорий.
                        Присылайте резюме на <a href="mailto:job@byclick.by">job@byclick.by</a>.
                    </p>
                    <p class="va_feature__text">Удалёнка, гибкий график, обучение за счёт компании.</p>
                </div>
            </div>

            <div class="va_panel va_about-press" id="press">
                <h3 class="va_panel__title">Пресс-центр</h3>
                <p class="va_feature__text">
                    По вопросам публикаций и интервью: <a href="mailto:press@byclick.by">press@byclick.by</a>.
                    Логотипы и фирменные цвета — в папке <code>brand/</code> проекта.
                </p>
            </div>
        </section>

        <section class="va_section" aria-label="Доставка и оплата">
            <div class="va_finance-card">
                <div class="va_finance-card__body">
                    <h3>Доставка от {{ $money($courierPrice) }}</h3>
                    <p>Бесплатно от {{ $money($freeFrom) }}. Курьер, пункт выдачи или постамат — выбирайте удобный способ.</p>
                    <a class="va_btn va_btn--primary" href="{{ route('delivery') }}">Условия доставки</a>
                </div>
                <div class="va_finance-card__art" aria-hidden="true">
                    <svg class="va_icon"><use href="#va-i-truck" /></svg>
                    <span>ByClick</span>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
