<x-layouts.app
    title="Помощь — ByClick"
    description="Помощь ByClick: как заказать, оплатить, вернуть товар, гарантия и поддержка 24/7."
    :canonical="route('help')"
>
    <div class="va_container">
        <x-site.breadcrumbs :items="[['label' => 'Помощь', 'url' => null]]" />

        <div class="va_page-head">
            <h1 class="va_page-title">Помощь</h1>
            <p class="va_page-sub">Ответы на частые вопросы, оплата, доставка и возврат</p>

            <form
                class="va_help-search"
                data-va-demo-form
                data-message="Демо-режим: поиск по помощи не подключён"
            >
                <input
                    class="va_input"
                    type="search"
                    name="q"
                    placeholder="Например, как вернуть товар"
                    aria-label="Поиск по помощи"
                    required
                />
                <button class="va_btn va_btn--primary" type="submit">Найти</button>
            </form>
        </div>

        <div class="va_features va_features--four">
            <a class="va_feature" href="#pay">
                <span class="va_feature__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-truck" /></svg></span>
                <h2 class="va_feature__title">Заказ и доставка</h2>
                <p class="va_feature__text">Как оформить, изменить или отменить заказ</p>
            </a>
            <a class="va_feature" href="#pay">
                <span class="va_feature__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-card" /></svg></span>
                <h2 class="va_feature__title">Оплата</h2>
                <p class="va_feature__text">Карты, оплата при получении</p>
            </a>
            <a class="va_feature" href="#return">
                <span class="va_feature__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-return" /></svg></span>
                <h2 class="va_feature__title">Возврат</h2>
                <p class="va_feature__text">30 дней на возврат и обмен</p>
            </a>
            <a class="va_feature" href="#support">
                <span class="va_feature__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-support" /></svg></span>
                <h2 class="va_feature__title">Поддержка</h2>
                <p class="va_feature__text">Телефон и почта — 24/7</p>
            </a>
        </div>

        <section class="va_section" id="pay" aria-labelledby="va-help-pay">
            <header class="va_section__head">
                <h2 class="va_section__title" id="va-help-pay">Заказ и оплата</h2>
            </header>

            <div class="va_faq">
                <details class="va_faq__item" id="how" open>
                    <summary>Как сделать заказ</summary>
                    <div class="va_faq__content">
                        <p>Выберите товар, добавьте в корзину и нажмите «Оформить заказ». Укажите контакты и способ доставки — мы пришлём подтверждение на почту.</p>
                    </div>
                </details>
                <details class="va_faq__item" id="payment">
                    <summary>Какие способы оплаты доступны</summary>
                    <div class="va_faq__content">
                        <p>Карты БЕЛКАРТ, Visa и Mastercard, а также оплата наличными или картой при получении. Все платежи проходят через защищённое соединение.</p>
                    </div>
                </details>
                <details class="va_faq__item">
                    <summary>Можно ли оплатить бонусами</summary>
                    <div class="va_faq__content">
                        <p>Да, баллами можно закрыть до 30% заказа. Баллы начисляются за покупки и отзывы.</p>
                    </div>
                </details>
            </div>
        </section>

        <section class="va_section" id="return" aria-labelledby="va-help-return">
            <header class="va_section__head">
                <h2 class="va_section__title" id="va-help-return">Возврат и гарантия</h2>
            </header>

            <div class="va_faq">
                <details class="va_faq__item">
                    <summary>Как вернуть товар</summary>
                    <div class="va_faq__content">
                        <p>В течение 30 дней с момента получения. Оформите возврат в личном кабинете или напишите в поддержку — заберём курьером или примем в пункте выдачи.</p>
                    </div>
                </details>
                <details class="va_faq__item" id="warranty">
                    <summary>Что с гарантией</summary>
                    <div class="va_faq__content">
                        <p>На технику действует гарантия от 1 года. Сохраните чек — он приходит на почту после оплаты.</p>
                    </div>
                </details>
            </div>
        </section>

        <section class="va_section" id="support" aria-labelledby="va-help-support">
            <header class="va_section__head">
                <h2 class="va_section__title" id="va-help-support">Поддержка</h2>
            </header>

            <div class="va_checkout-layout">
                <div class="va_panel">
                    <h3 class="va_panel__title">Связаться с нами</h3>
                    <ul class="va_contact-list">
                        <li class="va_contact-item">
                            <svg class="va_icon" aria-hidden="true"><use href="#va-i-support" /></svg>
                            <a href="tel:+375290000000">+375 (29) 000-00-00</a>
                        </li>
                        <li class="va_contact-item">
                            <svg class="va_icon" aria-hidden="true"><use href="#va-i-check" /></svg>
                            <a href="mailto:hello@byclick.by">hello@byclick.by</a>
                        </li>
                    </ul>
                    <p class="va_feature__text">Круглосуточно, без выходных. Среднее время ответа — 3 минуты.</p>
                </div>

                <form
                    class="va_panel"
                    id="feedback"
                    data-va-demo-form
                    data-message="Демо-режим: сообщение не отправлено"
                >
                    <h3 class="va_panel__title">Обратная связь</h3>
                    <div class="va_form-grid">
                        <label class="va_field">
                            Имя
                            <input class="va_input" type="text" name="name" required />
                        </label>
                        <label class="va_field">
                            E-mail
                            <input class="va_input" type="email" name="email" required />
                        </label>
                        <label class="va_field va_form-grid--full">
                            Сообщение
                            <textarea class="va_input va_input--area" name="message" required placeholder="Опишите ситуацию"></textarea>
                        </label>
                    </div>
                    <button class="va_btn va_btn--primary" type="submit">Отправить</button>
                </form>
            </div>
        </section>

        <section class="va_section" id="docs" aria-labelledby="va-help-docs">
            <header class="va_section__head">
                <h2 class="va_section__title" id="va-help-docs">Документы</h2>
            </header>

            <div class="va_faq">
                <details class="va_faq__item" id="terms">
                    <summary>Пользовательское соглашение</summary>
                    <div class="va_faq__content">
                        <p>Демонстрационный документ. Перед реальным запуском замените его на юридически проверенный текст: правила использования площадки, права и обязанности сторон, порядок разрешения споров.</p>
                    </div>
                </details>
                <details class="va_faq__item" id="privacy">
                    <summary>Политика конфиденциальности</summary>
                    <div class="va_faq__content">
                        <p>Демонстрационный документ. Опишите, какие данные собираются, зачем, как хранятся и как пользователь может их удалить, с учётом законодательства Республики Беларусь.</p>
                    </div>
                </details>
            </div>
        </section>
    </div>
</x-layouts.app>
