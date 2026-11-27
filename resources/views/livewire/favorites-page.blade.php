@php
    $count = $products->count();
@endphp

<div class="va_favorites-page">
    <x-site.breadcrumbs :items="[['label' => 'Избранное', 'url' => null]]" />

    <div class="va_cart-page__head">
        <div>
            <h1 class="va_page-title">Избранное</h1>
            <p class="va_page-sub">
                В избранном: <b>{{ $count }}</b>
                · сохраняется @auth на вашем аккаунте @else в этом браузере @endauth
            </p>
        </div>

        @if ($count > 0)
            <button class="va_link-btn" type="button" wire:click="clear">Очистить избранное</button>
        @endif
    </div>

    @if ($count === 0)
        <div class="va_cart-page__empty va_favorites-empty">
            <span class="va_cart-empty__icon">
                <svg class="va_icon" aria-hidden="true"><use href="#va-i-heart" /></svg>
            </span>
            <p class="va_cart-empty__title">В избранном пока пусто</p>
            <p class="va_cart-empty__text">Нажимайте на сердечко в карточках товаров — всё сохранится здесь.</p>
            <a class="va_btn va_btn--primary" href="{{ route('catalog') }}">Перейти в каталог</a>
        </div>
    @else
        <div class="va_products">
            @foreach ($products as $product)
                <x-site.product-card :product="$product" wire:key="favorite-{{ $product->id }}" />
            @endforeach
        </div>
    @endif
</div>
