@props(['product'])

@php
    $category = $product->category?->parent ?? $product->category;
    $url = url('/product/'.$product->slug);
    $image = $product->mainImage?->url() ?? asset('images/products/placeholder.svg');
@endphp

<article {{ $attributes->class(['va_product-card']) }}>
    <div class="va_product-card__media">
        <div class="va_product-card__badges">
            @if ($product->hasDiscount())
                <x-site.badge variant="sale">−{{ $product->discountPercent() }}%</x-site.badge>
            @endif
            @if ($product->sold_count >= 300)
                <x-site.badge variant="hit">Хит</x-site.badge>
            @endif
        </div>

        <button class="va_product-card__fav" type="button" aria-label="Добавить в избранное" aria-pressed="false" data-va-fav>
            <svg class="va_icon" aria-hidden="true"><use href="#va-i-heart" /></svg>
        </button>

        <a class="va_product-card__link" href="{{ $url }}" aria-label="{{ $product->name }}">
            <img
                class="va_product-card__img"
                src="{{ $image }}"
                alt="{{ $product->name }}"
                width="480"
                height="480"
                loading="lazy"
            />
        </a>
    </div>

    <div class="va_product-card__body">
        <p class="va_product-card__meta">{{ $category?->name }}</p>
        <h3 class="va_product-card__title"><a href="{{ $url }}">{{ $product->name }}</a></h3>

        <x-site.rating :value="$product->rating" :count="$product->reviews_count" />

        <p class="va_product-card__prices">
            <x-site.price :amount="$product->price" />
            @if ($product->hasDiscount())
                <x-site.price :amount="$product->old_price" :old="true" />
            @endif
        </p>

        <p class="va_product-card__delivery">{{ $product->stock > 0 ? 'Завтра, бесплатно' : 'Под заказ' }}</p>

        <button class="va_btn va_btn--cart" type="button" data-va-add="{{ $product->id }}">
            <svg class="va_icon" aria-hidden="true"><use href="#va-i-cart" /></svg>
            <span>В корзину</span>
        </button>
    </div>
</article>
