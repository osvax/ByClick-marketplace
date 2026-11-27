<x-layouts.app
    title="ByClick — маркетплейс нужных вещей"
    description="ByClick — маркетплейс: электроника, одежда, товары для дома, красоты и спорта. Доставка от 1 дня, возврат 30 дней, бонусы за покупки."
    :canonical="url('/')"
>
    <div class="va_container">
        {{-- Герой --}}
        <section class="va_hero" aria-label="Акции и предложения">
            <div class="va_hero__slider" data-va-slider>
                <div class="va_hero__track" data-va-track>
                    <article class="va_slide va_slide--violet is-active" data-va-slide>
                        <div class="va_slide__content">
                            <p class="va_slide__eyebrow">Техника недели</p>
                            <h1 class="va_slide__title">Звук, который держит ритм</h1>
                            <p class="va_slide__text">Беспроводные наушники и колонки со скидками до 40%.</p>
                            <a class="va_btn va_btn--light" href="{{ url('/catalog?tag=sale') }}">Смотреть технику</a>
                        </div>
                        <img class="va_slide__art" src="{{ asset('images/hero-1.svg') }}" alt="" aria-hidden="true" width="640" height="480" />
                    </article>

                    <article class="va_slide va_slide--sand" data-va-slide inert>
                        <div class="va_slide__content">
                            <p class="va_slide__eyebrow">Уютный дом</p>
                            <h2 class="va_slide__title">Свет, тепло и немного магии</h2>
                            <p class="va_slide__text">Лампы, текстиль и посуда для долгих вечеров — до −35%.</p>
                            <a class="va_btn va_btn--dark" href="{{ url('/catalog?category=home') }}">Обновить интерьер</a>
                        </div>
                        <img class="va_slide__art" src="{{ asset('images/hero-2.svg') }}" alt="" aria-hidden="true" width="640" height="480" loading="lazy" />
                    </article>

                    <article class="va_slide va_slide--mint" data-va-slide inert>
                        <div class="va_slide__content">
                            <p class="va_slide__eyebrow">Спорт рядом</p>
                            <h2 class="va_slide__title">Собери свой ритм</h2>
                            <p class="va_slide__text">Кроссовки, гаджеты и экипировка для тренировок в удобном темпе.</p>
                            <a class="va_btn va_btn--dark" href="{{ url('/catalog?category=sport') }}">Перейти в спорт</a>
                        </div>
                        <img class="va_slide__art" src="{{ asset('images/hero-3.svg') }}" alt="" aria-hidden="true" width="640" height="480" loading="lazy" />
                    </article>
                </div>

                <button class="va_slider__arrow va_slider__arrow--prev" type="button" aria-label="Предыдущий слайд" data-va-slide-prev>
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-chevron-left" /></svg>
                </button>
                <button class="va_slider__arrow va_slider__arrow--next" type="button" aria-label="Следующий слайд" data-va-slide-next>
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-chevron-right" /></svg>
                </button>

                <div class="va_slider__dots" role="tablist" aria-label="Слайды" data-va-dots>
                    <button class="va_slider__dot is-active" type="button" role="tab" aria-label="Слайд 1" aria-selected="true" data-va-dot="0"></button>
                    <button class="va_slider__dot" type="button" role="tab" aria-label="Слайд 2" aria-selected="false" data-va-dot="1"></button>
                    <button class="va_slider__dot" type="button" role="tab" aria-label="Слайд 3" aria-selected="false" data-va-dot="2"></button>
                </div>
            </div>

            <aside class="va_hero__aside">
                <article class="va_mini-promo va_mini-promo--violet">
                    <p class="va_mini-promo__eyebrow">Программа лояльности</p>
                    <h2 class="va_mini-promo__title">Бонусы за отзывы</h2>
                    <p class="va_mini-promo__text">Возвращаем до 10% баллами за каждую покупку.</p>
                    <a class="va_link-arrow" href="#bonus">
                        Как это работает
                        <svg class="va_icon" aria-hidden="true"><use href="#va-i-arrow-right" /></svg>
                    </a>
                </article>
                <article class="va_mini-promo va_mini-promo--mint">
                    <p class="va_mini-promo__eyebrow">Быстрая доставка</p>
                    <h2 class="va_mini-promo__title">Доставка за 1 день</h2>
                    <p class="va_mini-promo__text">Курьером, в пункты выдачи и постаматы.</p>
                    <a class="va_link-arrow" href="{{ url('/delivery') }}">
                        Подробнее
                        <svg class="va_icon" aria-hidden="true"><use href="#va-i-arrow-right" /></svg>
                    </a>
                </article>
            </aside>
        </section>

        {{-- Преимущества --}}
        <section class="va_advantages" aria-label="Преимущества">
            <ul class="va_advantages__list">
                <li class="va_advantage">
                    <span class="va_advantage__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-truck" /></svg></span>
                    <span class="va_advantage__body"><strong>Доставка от 1 дня</strong><span>Курьером и в пункты выдачи</span></span>
                </li>
                <li class="va_advantage">
                    <span class="va_advantage__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-return" /></svg></span>
                    <span class="va_advantage__body"><strong>Возврат 30 дней</strong><span>Без лишних вопросов</span></span>
                </li>
                <li class="va_advantage">
                    <span class="va_advantage__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-support" /></svg></span>
                    <span class="va_advantage__body"><strong>Поддержка 24/7</strong><span>Отвечаем в чате и по телефону</span></span>
                </li>
                <li class="va_advantage">
                    <span class="va_advantage__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-gift" /></svg></span>
                    <span class="va_advantage__body"><strong>Бонусы за покупки</strong><span>До 10% баллами на счёт</span></span>
                </li>
            </ul>
        </section>

        {{-- Акции --}}
        <section class="va_section" id="sale" aria-labelledby="va-sale-title">
            <header class="va_section__head">
                <h2 class="va_section__title" id="va-sale-title">Акции и скидки</h2>
                <a class="va_section__link" href="{{ url('/catalog?tag=sale') }}">
                    Все акции
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-arrow-right" /></svg>
                </a>
            </header>

            <div class="va_products">
                @foreach ($saleProducts as $product)
                    <x-site.product-card :product="$product" />
                @endforeach
            </div>
        </section>

        {{-- Распродажа с таймером --}}
        <section class="va_promo-wide" aria-labelledby="va-promo-title">
            <div class="va_promo-wide__content">
                <p class="va_promo-wide__eyebrow">Только сегодня</p>
                <h2 class="va_promo-wide__title" id="va-promo-title">Распродажа выходного дня</h2>
                <p class="va_promo-wide__text">Скидки до 60% на избранные товары. Цены вернутся к обычным, когда закончится таймер.</p>
                <div class="va_countdown" data-va-countdown aria-label="До конца распродажи">
                    <div class="va_countdown__cell"><span class="va_countdown__value" data-va-cd="h">00</span><span class="va_countdown__label">часов</span></div>
                    <span class="va_countdown__sep">:</span>
                    <div class="va_countdown__cell"><span class="va_countdown__value" data-va-cd="m">00</span><span class="va_countdown__label">минут</span></div>
                    <span class="va_countdown__sep">:</span>
                    <div class="va_countdown__cell"><span class="va_countdown__value" data-va-cd="s">00</span><span class="va_countdown__label">секунд</span></div>
                </div>
                <a class="va_btn va_btn--light" href="{{ url('/catalog?tag=sale') }}">Забрать скидку</a>
            </div>
            <img class="va_promo-wide__art" src="{{ asset('images/promo-wide.svg') }}" alt="" aria-hidden="true" width="640" height="480" loading="lazy" />
        </section>

        {{-- Категории --}}
        <section class="va_section" id="categories" aria-labelledby="va-cat-title">
            <header class="va_section__head">
                <h2 class="va_section__title" id="va-cat-title">Категории</h2>
                <a class="va_section__link" href="{{ url('/catalog') }}">
                    Все категории
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-arrow-right" /></svg>
                </a>
            </header>

            <ul class="va_categories">
                @foreach ($categories as $category)
                    @php($count = $categoryCounts[$category->id] ?? 0)
                    <li>
                        <a class="va_category" href="{{ url('/catalog?category='.$category->slug) }}">
                            <span class="va_category__icon">
                                <svg class="va_icon" aria-hidden="true"><use href="#va-i-{{ $category->icon ?? 'grid' }}" /></svg>
                            </span>
                            <span class="va_category__name">{{ $category->name }}</span>
                            <span class="va_category__count">
                                {{ number_format($count, 0, ',', ' ') }}
                                <x-site.plural :count="$count" one="товар" few="товара" many="товаров" />
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Хиты недели --}}
        <section class="va_section" id="hits" aria-labelledby="va-hits-title">
            <header class="va_section__head">
                <h2 class="va_section__title" id="va-hits-title">Хиты недели</h2>
                <a class="va_section__link" href="{{ url('/catalog') }}">
                    Смотреть все
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-arrow-right" /></svg>
                </a>
            </header>

            <div class="va_products">
                @foreach ($hitProducts as $product)
                    <x-site.product-card :product="$product" />
                @endforeach
            </div>
        </section>

        {{-- Бонусная программа --}}
        <section class="va_bonus-banner" id="bonus" aria-labelledby="va-bonus-title">
            <div class="va_bonus-banner__content">
                <h2 class="va_bonus-banner__title" id="va-bonus-title">Копите баллы за каждую покупку</h2>
                <p class="va_bonus-banner__text">1 балл = 1 рубль. Оплачивайте до 30% заказа бонусами и возвращайте до 10% с каждой покупки.</p>
            </div>
            <a class="va_btn va_btn--light" href="{{ url('/profile') }}">Стать участником</a>
        </section>

        {{-- Рекомендуем --}}
        <section class="va_section" id="recommend" aria-labelledby="va-rec-title">
            <header class="va_section__head">
                <h2 class="va_section__title" id="va-rec-title">Рекомендуем для вас</h2>
                <a class="va_section__link" href="{{ url('/catalog') }}">
                    Обновить подборку
                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-arrow-right" /></svg>
                </a>
            </header>

            <div class="va_products">
                @foreach ($recommended as $product)
                    <x-site.product-card :product="$product" />
                @endforeach
            </div>
        </section>
    </div>
</x-layouts.app>
