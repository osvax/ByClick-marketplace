@php
    $scale = (int) config('marketplace.currency_scale', 100);
    $currency = config('marketplace.currency', 'BYN');
    $money = fn (int $amount): string => number_format($amount / $scale, 2, ',', ' ').' '.$currency;

    $specs = $product->specs ?? [];
    $rootCategory = $product->category?->parent ?? $product->category;
    $galleryUrls = $gallery->pluck('url')->values();
    $galleryAlts = $gallery->pluck('alt')->values();

    // «Покупают вместе»: текущий товар + два связанных.
    $bundleItems = collect([$product])->concat($bundleProducts);
    $bundlePrices = $bundleItems->mapWithKeys(fn ($item) => [$item->id => $item->price]);
    $bundleSelected = $bundleProducts->pluck('id')->values();
@endphp

<x-layouts.app
    :title="$product->name.' — купить в ByClick'"
    :description="\Illuminate\Support\Str::limit(strip_tags($product->description ?? 'Товар в ByClick'), 160)"
    :canonical="route('product.show', $product)"
>
    <div class="va_container">
        <x-site.breadcrumbs :items="$breadcrumbs" />

        {{-- Корневой x-data хранит выбранную вкладку для всего блока товара --}}
        <div class="va_product-page" x-data="{ tab: 'desc' }">
            <div class="va_product-top">
                {{-- Галерея: миниатюры переключают главное изображение --}}
                <div class="va_gallery" x-data="{ active: 0, images: @js($galleryUrls) }">
                    <div class="va_gallery__thumbs" aria-label="Изображения товара">
                        @foreach ($gallery as $image)
                            <button
                                class="va_gallery__thumb"
                                :class="{ 'is-active': active === {{ $loop->index }} }"
                                type="button"
                                @click="active = {{ $loop->index }}"
                                aria-label="Фото {{ $loop->iteration }}: {{ $image['alt'] }}"
                            >
                                <img src="{{ $image['url'] }}" alt="" width="74" height="74" />
                            </button>
                        @endforeach
                    </div>

                    <div class="va_gallery__main">
                        @if ($product->hasDiscount())
                            <x-site.badge variant="sale">−{{ $product->discountPercent() }}%</x-site.badge>
                        @endif

                        <img
                            :src="images[active]"
                            src="{{ $galleryUrls->first() }}"
                            alt="{{ $product->name }}"
                            width="640"
                            height="640"
                        />

                        <button
                            class="va_gallery__expand"
                            type="button"
                            aria-label="Открыть изображение на весь экран"
                            @click="$refs.lightbox.showModal()"
                        >
                            <svg class="va_icon" aria-hidden="true"><use href="#va-i-zoom" /></svg>
                        </button>
                    </div>

                    {{-- Лайтбокс на нативном dialog --}}
                    <dialog
                        class="va_lightbox"
                        x-ref="lightbox"
                        aria-label="Просмотр изображения"
                        @click="if ($event.target === $refs.lightbox) $refs.lightbox.close()"
                    >
                        <div class="va_lightbox__inner">
                            <button class="va_lightbox__close" type="button" @click="$refs.lightbox.close()" aria-label="Закрыть">
                                <svg class="va_icon" aria-hidden="true"><use href="#va-i-close" /></svg>
                            </button>
                            <figure class="va_lightbox__figure">
                                <img :src="images[active]" alt="{{ $product->name }}" />
                                <figcaption>{{ $product->name }}</figcaption>
                            </figure>
                        </div>
                    </dialog>
                </div>

                <div class="va_product-info">
                    <p class="va_product-info__meta">
                        {{ $rootCategory?->name }}
                        @if (! empty($specs['Артикул']))
                            · Артикул {{ $specs['Артикул'] }}
                        @endif
                        @if ($product->shop)
                            · Продавец {{ $product->shop->name }}
                        @endif
                    </p>

                    <h1 class="va_page-title">{{ $product->name }}</h1>

                    <div class="va_product-info__rating">
                        <a class="va_rating va_rating--link" href="#reviews">
                            <svg class="va_rating__star" aria-hidden="true"><use href="#va-i-star" /></svg>
                            <span class="va_rating__value">{{ number_format((float) $product->rating, 1) }}</span>
                            <span class="va_rating__count">
                                {{ number_format($product->reviews_count, 0, ',', ' ') }}
                                <x-site.plural :count="$product->reviews_count" one="отзыв" few="отзыва" many="отзывов" />
                            </span>
                        </a>
                        <span class="va_product-info__sold">{{ number_format($product->sold_count, 0, ',', ' ') }} раз купили</span>
                    </div>

                    @if ($product->shop)
                        <div class="va_seller-row">
                            <span class="va_seller-row__logo">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($product->shop->name, 0, 2)) }}</span>
                            <div class="va_seller-row__body">
                                <p class="va_seller-row__name">{{ $product->shop->name }}</p>
                                <p class="va_seller-row__meta">
                                    <span class="va_seller-row__star">★</span>
                                    {{ number_format((float) $product->shop->rating, 1) }}
                                    @if ($product->shop->is_verified)
                                        · проверенный продавец
                                    @endif
                                </p>
                            </div>
                            <button class="va_link-btn" type="button" data-va-demo-toast="Страница продавца появится позже">Перейти в магазин</button>
                        </div>
                    @endif

                    <div class="va_product-info__prices">
                        <span class="va_product-info__price">{{ $money($product->price) }}</span>
                        @if ($product->hasDiscount())
                            <span class="va_product-info__old">{{ $money($product->old_price) }}</span>
                            <span class="va_product-info__sale">−{{ $product->discountPercent() }}%</span>
                        @endif
                    </div>

                    <div class="va_product-info__short">
                        <h2>О товаре</h2>
                        <ul>
                            @foreach (collect($specs)->take(4) as $name => $value)
                                <li>{{ $name }}: {{ $value }}</li>
                            @endforeach
                        </ul>
                        <button class="va_link-btn" type="button" @click="tab = 'specs'; $el.closest('.va_product-page').querySelector('#details').scrollIntoView({ behavior: 'smooth' })">
                            Все характеристики
                        </button>
                    </div>

                    {{-- Блок покупки: количество и демо-кнопки до появления корзины --}}
                    <div class="va_buybox" x-data="{ qty: 1 }">
                        <div class="va_buybox__head">
                            <p class="va_buybox__label">Покупка</p>
                            <p class="va_buybox__stock">
                                <span class="va_buybox__dot"></span>
                                @if ($product->stock > 0)
                                    В наличии · осталось {{ $product->stock }} шт.
                                @else
                                    Нет в наличии
                                @endif
                            </p>
                        </div>

                        <div class="va_buybox__controls">
                            <div class="va_qty va_qty--lg">
                                <button class="va_qty__btn" type="button" aria-label="Уменьшить количество" @click="qty = Math.max(1, qty - 1)" :disabled="qty <= 1">
                                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-minus" /></svg>
                                </button>
                                <span class="va_qty__value" x-text="qty">1</span>
                                <button class="va_qty__btn" type="button" aria-label="Увеличить количество" @click="qty = Math.min({{ max(1, $product->stock) }}, qty + 1)" :disabled="qty >= {{ max(1, $product->stock) }}">
                                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-plus" /></svg>
                                </button>
                            </div>

                            <button
                                class="va_btn va_btn--primary va_btn--buy"
                                type="button"
                                data-va-add="{{ $product->id }}"
                                :data-va-qty="qty"
                                @disabled($product->stock === 0)
                            >
                                <svg class="va_icon" aria-hidden="true"><use href="#va-i-cart" /></svg>
                                В корзину
                            </button>

                            <button
                                class="va_btn va_btn--icon"
                                type="button"
                                aria-pressed="false"
                                aria-label="Добавить в избранное"
                                data-va-demo-toast="Демо-режим: избранное появится на следующем шаге"
                            >
                                <svg class="va_icon" aria-hidden="true"><use href="#va-i-heart" /></svg>
                            </button>
                        </div>

                        <button
                            class="va_btn va_btn--wide"
                            type="button"
                            data-va-demo-toast="Демо-режим: оформление заказа появится позже"
                            @disabled($product->stock === 0)
                        >
                            Купить сейчас
                        </button>

                        <ul class="va_buybox__delivery">
                            <li>
                                <svg class="va_icon" aria-hidden="true"><use href="#va-i-truck" /></svg>
                                <span><strong>Доставка завтра</strong><small>бесплатно, в пункт выдачи или курьером</small></span>
                            </li>
                            <li>
                                <svg class="va_icon" aria-hidden="true"><use href="#va-i-return" /></svg>
                                <span><strong>Возврат 30 дней</strong><small>без объяснения причин</small></span>
                            </li>
                        </ul>
                    </div>

                    <ul class="va_product-info__benefits">
                        <li><svg class="va_icon" aria-hidden="true"><use href="#va-i-check" /></svg>Оригинал, гарантия 12 месяцев</li>
                        <li><svg class="va_icon" aria-hidden="true"><use href="#va-i-check" /></svg>Оплата при получении</li>
                        <li><svg class="va_icon" aria-hidden="true"><use href="#va-i-check" /></svg>Бонусы за отзыв</li>
                        <li><svg class="va_icon" aria-hidden="true"><use href="#va-i-check" /></svg>Проверенный продавец</li>
                    </ul>
                </div>
            </div>

            {{-- Комплект: сумма пересчитывается на клиенте, цены приходят из БД --}}
            @if ($bundleProducts->isNotEmpty())
                <section class="va_section va_bundle-section" aria-labelledby="va-bundle-title">
                    <header class="va_section__head">
                        <div>
                            <h2 class="va_section__title" id="va-bundle-title">Покупают вместе</h2>
                            <p class="va_section__sub">Соберите комплект — доставка одного заказа</p>
                        </div>
                        @if ($bundleDiscount > 0)
                            <span class="va_bundle__save">Выгода {{ $money($bundleDiscount) }}</span>
                        @endif
                    </header>

                    <div
                        class="va_bundle"
                        x-data="{
                            selected: @js($bundleSelected),
                            prices: @js($bundlePrices),
                            base: {{ $product->price }},
                            get total() {
                                return this.base + this.selected.reduce((sum, id) => sum + this.prices[id], 0);
                            },
                            get count() {
                                return this.selected.length + 1;
                            },
                            plural(value) {
                                const mod10 = value % 10;
                                const mod100 = value % 100;
                                if (mod10 === 1 && mod100 !== 11) return 'товар';
                                if (mod10 >= 2 && mod10 <= 4 && (mod100 < 10 || mod100 >= 20)) return 'товара';
                                return 'товаров';
                            },
                            money(value) {
                                return new Intl.NumberFormat('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value / 100) + ' {{ $currency }}';
                            },
                        }"
                    >
                        <div class="va_bundle__items">
                            <label class="va_bundle-item">
                                <input class="va_bundle-item__check" type="checkbox" checked disabled aria-label="Текущий товар" />
                                <span class="va_bundle-item__media">
                                    <img src="{{ $galleryUrls->first() }}" alt="{{ $product->name }}" width="120" height="120" loading="lazy" />
                                </span>
                                <span class="va_bundle-item__body">
                                    <span class="va_bundle-item__name">{{ $product->name }}</span>
                                    <span class="va_bundle-item__price">{{ $money($product->price) }}</span>
                                </span>
                                <span class="va_bundle-item__tag">Этот товар</span>
                            </label>

                            @foreach ($bundleProducts as $item)
                                <span class="va_bundle__plus" aria-hidden="true">+</span>

                                <label class="va_bundle-item">
                                    <input
                                        class="va_bundle-item__check"
                                        type="checkbox"
                                        value="{{ $item->id }}"
                                        x-model="selected"
                                        aria-label="Добавить «{{ $item->name }}»"
                                    />
                                    <span class="va_bundle-item__media">
                                        <img
                                            src="{{ $item->mainImage?->url() ?? asset('images/products/placeholder.svg') }}"
                                            alt="{{ $item->name }}"
                                            width="120"
                                            height="120"
                                            loading="lazy"
                                        />
                                    </span>
                                    <span class="va_bundle-item__body">
                                        <span class="va_bundle-item__name">{{ $item->name }}</span>
                                        <span class="va_bundle-item__price">{{ $money($item->price) }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <aside class="va_bundle__summary">
                            <p class="va_bundle__summary-label">
                                <span x-text="count">1</span>
                                <span x-text="plural(count)">товар</span>
                            </p>
                            <p class="va_bundle__total" x-text="money(total)">{{ $money($product->price) }}</p>
                            <button
                                class="va_btn va_btn--primary va_btn--block"
                                type="button"
                                @click="window.Livewire.dispatch('cart-add-bundle', { productIds: [{{ $product->id }}, ...selected] })"
                            >Добавить в корзину</button>
                            <p class="va_bundle__hint">Можно выбрать только нужные товары</p>
                        </aside>
                    </div>
                </section>
            @endif

            {{-- Описание и характеристики: переключаются через Alpine --}}
            <div class="va_product-tabs" id="details">
                <div class="va_product-tabs__list" role="tablist" aria-label="Информация о товаре">
                    <button type="button" role="tab" id="va-tab-desc" aria-controls="va-panel-desc" @click="tab = 'desc'" :aria-selected="tab === 'desc' ? 'true' : 'false'">
                        Описание
                    </button>
                    <button type="button" role="tab" id="va-tab-specs" aria-controls="va-panel-specs" @click="tab = 'specs'" :aria-selected="tab === 'specs' ? 'true' : 'false'">
                        Характеристики
                    </button>
                </div>

                <div class="va_product-tabs__panel" role="tabpanel" id="va-panel-desc" aria-labelledby="va-tab-desc" :hidden="tab !== 'desc'">
                    <h3>{{ $product->name }}</h3>
                    <p>{{ $product->description }}</p>

                    @if (! empty($specs))
                        <ul>
                            @foreach ($specs as $name => $value)
                                <li>{{ $name }}: {{ $value }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="va_product-tabs__panel" role="tabpanel" id="va-panel-specs" aria-labelledby="va-tab-specs" hidden :hidden="tab !== 'specs'">
                    <table class="va_specs">
                        <tbody>
                            @foreach ($specs as $name => $value)
                                <tr>
                                    <td>{{ $name }}</td>
                                    <td>{{ $value }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Отзывы --}}
            <section class="va_section va_reviews-section" id="reviews" aria-labelledby="va-reviews-title">
                <header class="va_section__head">
                    <div>
                        <h2 class="va_section__title" id="va-reviews-title">Отзывы о товаре</h2>
                        <p class="va_section__sub">
                            {{ number_format($reviewCount, 0, ',', ' ') }}
                            <x-site.plural :count="$reviewCount" one="отзыв" few="отзыва" many="отзывов" />
                            @if ($reviewCount > 0)
                                · {{ $recommendedPercent }}% рекомендуют
                            @endif
                        </p>
                    </div>
                    <button class="va_btn va_btn--outline" type="button" data-va-demo-toast="Демо-режим: форма отзыва появится после авторизации">Написать отзыв</button>
                </header>

                <div class="va_reviews-layout">
                    <aside class="va_reviews-summary" aria-label="Сводка оценок">
                        <div class="va_reviews-summary__score">
                            <b>{{ number_format((float) $product->rating, 1) }}</b>
                            <span class="va_rating__star" aria-hidden="true">
                                <svg class="va_icon"><use href="#va-i-star" /></svg>
                            </span>
                            <span>{{ number_format($product->reviews_count, 0, ',', ' ') }} оценок</span>
                        </div>

                        <ul class="va_reviews-bars">
                            @foreach ($ratingBreakdown as $star => $count)
                                @php($percent = $reviewCount > 0 ? (int) round($count / $reviewCount * 100) : 0)
                                <li>
                                    <span>{{ $star }}</span>
                                    <span class="va_reviews-bars__track"><i style="width: {{ $percent }}%"></i></span>
                                    <span>{{ $percent }}%</span>
                                </li>
                            @endforeach
                        </ul>

                        @if ($reviewCount > 0)
                            <div class="va_reviews-summary__pros">
                                <p><strong>{{ $recommendedPercent }}%</strong> рекомендуют товар</p>
                            </div>
                        @endif
                    </aside>

                    <div class="va_reviews-list" x-data="{ reviewFilter: 'all' }">
                        @if ($reviewCount > 0)
                            <div class="va_reviews-filter" role="group" aria-label="Фильтр отзывов">
                                <button class="va_chip-btn" type="button" :class="{ 'is-active': reviewFilter === 'all' }" @click="reviewFilter = 'all'" :aria-pressed="reviewFilter === 'all'">Все</button>
                                <button class="va_chip-btn" type="button" :class="{ 'is-active': reviewFilter === '5' }" @click="reviewFilter = '5'" :aria-pressed="reviewFilter === '5'">5 звёзд</button>
                                <button class="va_chip-btn" type="button" :class="{ 'is-active': reviewFilter === '4' }" @click="reviewFilter = '4'" :aria-pressed="reviewFilter === '4'">4 звезды</button>
                            </div>

                            @foreach ($reviews as $review)
                                <article class="va_review" :hidden="reviewFilter !== 'all' && reviewFilter !== '{{ $review->rating }}'">
                                    <header class="va_review__head">
                                        <span class="va_review__avatar">{{ $review->user?->initials() ?? 'П' }}</span>
                                        <div class="va_review__author">
                                            <p class="va_review__name">{{ $review->user?->name ?? 'Покупатель' }}</p>
                                            <p class="va_review__meta">
                                                {{ $review->created_at?->translatedFormat('j F Y') }} · покупка подтверждена
                                            </p>
                                        </div>
                                        <span class="va_review__rating">
                                            <svg class="va_rating__star" aria-hidden="true"><use href="#va-i-star" /></svg>
                                            {{ number_format((float) $review->rating, 1) }}
                                        </span>
                                    </header>

                                    @if ($review->title)
                                        <p class="va_review__text"><strong>{{ $review->title }}</strong></p>
                                    @endif
                                    <p class="va_review__text">{{ $review->text }}</p>

                                    @if ($review->pros || $review->cons)
                                        <ul class="va_review__points">
                                            @if ($review->pros)
                                                <li><b>Плюсы:</b> {{ $review->pros }}</li>
                                            @endif
                                            @if ($review->cons)
                                                <li><b>Минусы:</b> {{ $review->cons }}</li>
                                            @endif
                                        </ul>
                                    @endif

                                    <footer class="va_review__foot">
                                        <button
                                            class="va_review__helpful"
                                            type="button"
                                            data-va-demo-toast="Спасибо! Голос учтён в демо-режиме"
                                        >
                                            <svg class="va_icon" aria-hidden="true"><use href="#va-i-thumb" /></svg>
                                            Полезно <span>{{ $review->helpful_count }}</span>
                                        </button>
                                        <button class="va_link-btn" type="button" data-va-demo-toast="Демо-режим: ответы на отзывы появятся позже">Ответить</button>
                                    </footer>
                                </article>
                            @endforeach
                        @else
                            <div class="va_catalog-empty">
                                <span class="va_catalog-empty__icon">
                                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-message" /></svg>
                                </span>
                                <p class="va_catalog-empty__title">Отзывов пока нет</p>
                                <p class="va_catalog-empty__text">Станьте первым, кто поделится впечатлениями о товаре.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Вопросы и ответы продавца --}}
            <section class="va_section" id="questions" aria-labelledby="va-questions-title">
                <header class="va_section__head">
                    <div>
                        <h2 class="va_section__title" id="va-questions-title">Вопросы о товаре</h2>
                        <p class="va_section__sub">
                            {{ number_format($product->questions->count(), 0, ',', ' ') }}
                            <x-site.plural :count="$product->questions->count()" one="вопрос" few="вопроса" many="вопросов" />
                            · продавец отвечает за 2 часа
                        </p>
                    </div>
                </header>

                <div class="va_questions">
                    @forelse ($product->questions as $question)
                        <article class="va_question">
                            <p class="va_question__text"><b>Вопрос:</b> {{ $question->text }}</p>

                            @if ($question->answer)
                                <p class="va_question__answer"><b>Ответ продавца:</b> {{ $question->answer }}</p>
                            @endif

                            <p class="va_question__meta">
                                {{ $question->user?->name ?? 'Гость' }}
                                · {{ $question->created_at?->translatedFormat('j F') }}
                                ·
                                <button class="va_link-btn" type="button" data-va-demo-toast="Спасибо! Голос учтён в демо-режиме">
                                    <svg class="va_icon" aria-hidden="true"><use href="#va-i-thumb" /></svg>
                                    Полезно
                                </button>
                            </p>
                        </article>
                    @empty
                        <p class="va_section__sub">Вопросов пока нет — задайте первый.</p>
                    @endforelse
                </div>

                <form class="va_question-form" data-va-demo-form data-message="Демо-режим: вопрос отправлен продавцу">
                    <label class="va_field">
                        Ваш вопрос
                        <input
                            class="va_input"
                            type="text"
                            name="question"
                            placeholder="Например, подойдут ли наушники для звонков"
                            required
                        />
                    </label>
                    <button class="va_btn va_btn--primary" type="submit">Отправить</button>
                </form>
            </section>

            {{-- Похожие товары --}}
            @if ($related->isNotEmpty())
                <section class="va_section" aria-labelledby="va-recommend-title">
                    <header class="va_section__head">
                        <h2 class="va_section__title" id="va-recommend-title">Рекомендуем</h2>
                        @if ($rootCategory)
                            <a class="va_section__link" href="{{ url('/catalog?category='.$rootCategory->slug) }}">
                                Все товары
                                <svg class="va_icon" aria-hidden="true"><use href="#va-i-arrow-right" /></svg>
                            </a>
                        @endif
                    </header>

                    <div class="va_products">
                        @foreach ($related as $item)
                            <x-site.product-card :product="$item" />
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>

    {{-- Микроразметка для поисковых систем --}}
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => $galleryUrls->all(),
            'description' => \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 300),
            'sku' => $specs['Артикул'] ?? null,
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand ?? $product->shop?->name ?? 'ByClick',
            ],
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => $currency,
                'price' => number_format($product->price / $scale, 2, '.', ''),
                'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => route('product.show', $product),
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
</x-layouts.app>
