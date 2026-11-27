<footer class="va_footer">
    <div class="va_container">
        <div class="va_footer__top">
            <div class="va_footer__brand">
                <a class="va_logo va_logo--footer" href="{{ route('home') }}" aria-label="ByClick — на главную">
                    <img class="va_logo__mark" src="{{ asset('brand/byclick-mark.svg') }}" alt="" width="38" height="38" />
                    <span class="va_logo__text">
                        <span class="va_logo__name"><span class="va_logo__by">By</span>Click</span>
                        <span class="va_logo__tagline">Купи в один клик!</span>
                    </span>
                </a>
                <p class="va_footer__tagline">Маркетплейс нужных вещей. Собираем лучшее от проверенных продавцов — быстро, удобно и по честной цене.</p>
                <div class="va_footer__social" aria-label="Социальные сети">
                    <a class="va_social" href="#vk" aria-label="ВКонтакте">ВК</a>
                    <a class="va_social" href="#tg" aria-label="Telegram">TG</a>
                    <a class="va_social" href="#yt" aria-label="YouTube">YT</a>
                </div>
            </div>

            <nav class="va_footer__col" aria-labelledby="va-footer-buyers">
                <h3 class="va_footer__title" id="va-footer-buyers">Покупателям</h3>
                <ul class="va_footer__list">
                    <li><a href="{{ url('/help#how') }}">Как сделать заказ</a></li>
                    <li><a href="{{ url('/help#payment') }}">Оплата</a></li>
                    <li><a href="{{ url('/delivery') }}">Доставка</a></li>
                    <li><a href="{{ url('/help#return') }}">Возврат и обмен</a></li>
                    <li><a href="{{ url('/help#warranty') }}">Гарантия</a></li>
                </ul>
            </nav>

            <nav class="va_footer__col" aria-labelledby="va-footer-company">
                <h3 class="va_footer__title" id="va-footer-company">Компания</h3>
                <ul class="va_footer__list">
                    <li><a href="{{ url('/about') }}">О ByClick</a></li>
                    <li><a href="{{ url('/about#career') }}">Карьера</a></li>
                    <li><a href="{{ url('/seller') }}">Партнёрам</a></li>
                    <li><a href="{{ url('/about#press') }}">Пресс-центр</a></li>
                    <li><a href="{{ url('/about#contacts') }}">Контакты</a></li>
                </ul>
            </nav>

            <nav class="va_footer__col" aria-labelledby="va-footer-help">
                <h3 class="va_footer__title" id="va-footer-help">Помощь</h3>
                <ul class="va_footer__list">
                    <li><a href="{{ url('/help') }}">Частые вопросы</a></li>
                    <li><a href="{{ url('/help#support') }}">Поддержка 24/7</a></li>
                    <li><a href="{{ url('/delivery') }}">Пункты выдачи</a></li>
                    <li><a href="{{ url('/help#feedback') }}">Обратная связь</a></li>
                </ul>
            </nav>

            <div class="va_footer__col">
                <h3 class="va_footer__title">Мы на связи</h3>
                <a class="va_footer__phone" href="tel:+375290000000">+375 (29) 000-00-00</a>
                <p class="va_footer__note">Бесплатно по Беларуси, круглосуточно</p>
                <a class="va_footer__mail" href="mailto:hello@byclick.by">hello@byclick.by</a>
            </div>
        </div>

        <div class="va_footer__bottom">
            <p class="va_footer__copy">© {{ now()->year }} ByClick. Демонстрационный проект, все товары и бренды вымышлены.</p>
            <ul class="va_footer__pay" aria-label="Способы оплаты">
                <li class="va_pay">Мир</li>
                <li class="va_pay">Visa</li>
                <li class="va_pay">Mastercard</li>
                <li class="va_pay">БЕЛКАРТ</li>
            </ul>
            <ul class="va_footer__legal">
                <li><a href="{{ url('/help#terms') }}">Пользовательское соглашение</a></li>
                <li><a href="{{ url('/help#privacy') }}">Политика конфиденциальности</a></li>
            </ul>
        </div>
    </div>
</footer>
