<x-layouts.app
    title="Продавцам — ByClick"
    description="Продавайте на ByClick: своя витрина, логистика, выплаты каждую неделю и 1,2 млн покупателей. Оставьте заявку на подключение."
    :canonical="route('seller')"
>
    <div class="va_container">
        <x-site.breadcrumbs :items="[['label' => 'Продавцам', 'url' => null]]" />

        <section class="va_info-hero">
            <h1>Продавайте на ByClick</h1>
            <p>
                Своя витрина, готовая логистика и 1,2 млн покупателей.
                Подключение за один день, комиссия — от 5%.
            </p>
            <a class="va_btn va_btn--light" href="#seller-form">Оставить заявку</a>
        </section>

        <div class="va_features">
            <article class="va_feature">
                <span class="va_feature__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-cart" /></svg></span>
                <h3 class="va_feature__title">Своя витрина</h3>
                <p class="va_feature__text">Брендированная страница магазина, отзывы и аналитика продаж.</p>
            </article>
            <article class="va_feature">
                <span class="va_feature__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-truck" /></svg></span>
                <h3 class="va_feature__title">Логистика на нас</h3>
                <p class="va_feature__text">Забираем заказы со склада и доставляем по всей Беларуси.</p>
            </article>
            <article class="va_feature">
                <span class="va_feature__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-gift" /></svg></span>
                <h3 class="va_feature__title">Выплаты каждую неделю</h3>
                <p class="va_feature__text">Прозрачные отчёты и деньги на счёт без задержек.</p>
            </article>
        </div>

        <section class="va_section" aria-labelledby="va-seller-steps">
            <header class="va_section__head">
                <h2 class="va_section__title" id="va-seller-steps">Как начать</h2>
            </header>

            <div class="va_steps">
                <article class="va_step">
                    <span class="va_step__num">1</span>
                    <h3 class="va_step__title">Заявка</h3>
                    <p class="va_step__text">Расскажите о магазине и категории товаров.</p>
                </article>
                <article class="va_step">
                    <span class="va_step__num">2</span>
                    <h3 class="va_step__title">Договор</h3>
                    <p class="va_step__text">Подписываем документы онлайн и подключаем кабинет.</p>
                </article>
                <article class="va_step">
                    <span class="va_step__num">3</span>
                    <h3 class="va_step__title">Продажи</h3>
                    <p class="va_step__text">Загружаете товары — мы приводим покупателей.</p>
                </article>
            </div>
        </section>

        <section class="va_section" id="seller-form" aria-labelledby="va-seller-form-title">
            <header class="va_section__head">
                <h2 class="va_section__title" id="va-seller-form-title">Заявка на подключение</h2>
            </header>

            <livewire:seller-request-form />
        </section>
    </div>
</x-layouts.app>
