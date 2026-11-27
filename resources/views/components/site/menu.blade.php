<div class="va_menu" data-va-menu aria-hidden="true">
    <div class="va_menu__overlay" data-va-menu-close></div>
    <div class="va_menu__panel" role="dialog" aria-modal="true" aria-label="Меню">
        <div class="va_menu__head">
            <a class="va_logo" href="{{ route('home') }}" aria-label="ByClick — на главную">
                <img class="va_logo__mark" src="{{ asset('brand/byclick-mark.svg') }}" alt="" width="38" height="38" />
                <span class="va_logo__text">
                    <span class="va_logo__name"><span class="va_logo__by">By</span>Click</span>
                    <span class="va_logo__tagline">Купи в один клик!</span>
                </span>
            </a>
            <button class="va_icon-btn" type="button" aria-label="Закрыть меню" data-va-menu-close>
                <svg class="va_icon" aria-hidden="true"><use href="#va-i-close" /></svg>
            </button>
        </div>

        <nav class="va_menu__nav" aria-label="Мобильное меню">
            <p class="va_menu__group">Каталог</p>
            <ul class="va_menu__list">
                @foreach ($categories as $category)
                    <li><a href="{{ url('/catalog?category='.$category->slug) }}">{{ $category->name }}</a></li>
                @endforeach
            </ul>

            <p class="va_menu__group">Сервисы</p>
            <ul class="va_menu__list">
                <li><a href="{{ url('/profile') }}">Мой профиль</a></li>
                <li><a href="{{ url('/orders') }}">Мои заказы</a></li>
                <li><a href="{{ url('/favorites') }}">Избранное</a></li>
                <li><a href="{{ url('/help') }}">Помощь</a></li>
                <li><a href="{{ url('/delivery') }}">Пункты выдачи</a></li>
                <li><a href="{{ url('/seller') }}">Стать продавцом</a></li>
            </ul>
        </nav>

        @auth
            <a class="va_btn va_btn--primary va_btn--block" href="{{ url('/profile') }}">Мой профиль</a>
            <form class="va_menu__logout" method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="va_btn va_btn--outline va_btn--block" type="submit">Выйти</button>
            </form>
        @else
            <a class="va_btn va_btn--primary va_btn--block" href="{{ route('login') }}">Войти или зарегистрироваться</a>
        @endauth
    </div>
</div>
