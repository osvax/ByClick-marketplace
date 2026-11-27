<x-layouts.app
    title="Страница не найдена — ByClick"
    description="Страница не найдена. Вернитесь на главную или перейдите в каталог ByClick."
>
    <div class="va_container">
        <div class="va_cart-page__empty">
            <span class="va_cart-empty__icon">
                <svg class="va_icon" aria-hidden="true"><use href="#va-i-search" /></svg>
            </span>
            <p class="va_cart-empty__title">Страница не найдена</p>
            <p class="va_cart-empty__text">
                Возможно, ссылка устарела или товар больше не продаётся.
                Проверьте адрес или начните с каталога.
            </p>
            <div class="va_cart-empty__actions">
                <a class="va_btn va_btn--primary" href="{{ route('home') }}">На главную</a>
                <a class="va_btn va_btn--outline" href="{{ route('catalog') }}">В каталог</a>
            </div>
        </div>
    </div>
</x-layouts.app>
