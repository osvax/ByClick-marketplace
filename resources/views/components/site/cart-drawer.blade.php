<div class="va_drawer" data-va-drawer aria-hidden="true">
    <div class="va_drawer__overlay" data-va-cart-close></div>
    <aside class="va_drawer__panel" role="dialog" aria-modal="true" aria-labelledby="va-cart-title">
        <header class="va_drawer__head">
            <h2 class="va_drawer__title" id="va-cart-title">
                Корзина <span class="va_drawer__count" data-va-cart-count-text>0</span>
            </h2>
            <button class="va_icon-btn" type="button" aria-label="Закрыть корзину" data-va-cart-close>
                <svg class="va_icon" aria-hidden="true"><use href="#va-i-close" /></svg>
            </button>
        </header>

        <div class="va_drawer__body" data-va-cart-body>
            <div class="va_cart-empty" data-va-cart-empty>
                <span class="va_cart-empty__icon"><svg class="va_icon" aria-hidden="true"><use href="#va-i-cart" /></svg></span>
                <p class="va_cart-empty__title">Пока пусто</p>
                <p class="va_cart-empty__text">Добавьте товары из подборок — они появятся здесь.</p>
                <button class="va_btn va_btn--primary" type="button" data-va-cart-close>Перейти к покупкам</button>
            </div>
            <ul class="va_cart-list" data-va-cart-list></ul>
        </div>

        <footer class="va_drawer__foot" data-va-cart-foot hidden>
            <div class="va_drawer__row">
                <span>Товары</span>
                <span data-va-cart-subtotal>0</span>
            </div>
            <div class="va_drawer__row">
                <span>Доставка</span>
                <span class="va_drawer__free">бесплатно</span>
            </div>
            <div class="va_drawer__row va_drawer__row--total">
                <span>Итого</span>
                <span data-va-cart-total>0</span>
            </div>
            <a class="va_btn va_btn--primary va_btn--block" href="{{ url('/cart') }}">Перейти в корзину</a>
        </footer>
    </aside>
</div>
