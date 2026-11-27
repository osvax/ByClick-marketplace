@if ($submitted)
    <div class="va_panel">
        <h3 class="va_panel__title">
            <span class="va_panel__num">✓</span>
            Заявка отправлена
        </h3>
        <p class="va_feature__text">
            Спасибо! Мы свяжемся с вами в течение рабочего дня, чтобы обсудить подключение магазина.
        </p>
        <div>
            <a class="va_btn va_btn--primary" href="{{ route('catalog') }}">Продолжить покупки</a>
        </div>
    </div>
@else
    <form class="va_panel" wire:submit="submit">
        <div class="va_form-grid">
            <label class="va_field">
                Имя
                <input class="va_input" type="text" wire:model="name" autocomplete="name" placeholder="Как к вам обращаться" />
                @error('name')
                    <span class="va_field__error">{{ $message }}</span>
                @enderror
            </label>

            <label class="va_field">
                Телефон
                <input class="va_input" type="tel" wire:model="phone" autocomplete="tel" placeholder="+375 29 000-00-00" />
                @error('phone')
                    <span class="va_field__error">{{ $message }}</span>
                @enderror
            </label>

            <label class="va_field">
                E-mail
                <input class="va_input" type="email" wire:model="email" autocomplete="email" placeholder="you@example.com" />
                @error('email')
                    <span class="va_field__error">{{ $message }}</span>
                @enderror
            </label>

            <label class="va_field">
                Категория
                <select class="va_input" wire:model="categoryId">
                    <option value="">Выберите категорию</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('categoryId')
                    <span class="va_field__error">{{ $message }}</span>
                @enderror
            </label>

            <label class="va_field va_form-grid--full">
                Комментарий
                <textarea
                    class="va_input va_input--area"
                    wire:model="comment"
                    placeholder="Ссылка на магазин, объём продаж"
                ></textarea>
                @error('comment')
                    <span class="va_field__error">{{ $message }}</span>
                @enderror
            </label>
        </div>

        <label class="va_check">
            <input type="checkbox" wire:model="agree" />
            <span>
                Согласен на обработку персональных данных согласно
                <a href="{{ url('/help#privacy') }}">политике конфиденциальности</a>
            </span>
        </label>
        @error('agree')
            <span class="va_field__error">{{ $message }}</span>
        @enderror

        <div>
            <button class="va_btn va_btn--primary" type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove>Отправить заявку</span>
                <span wire:loading>Отправляем…</span>
            </button>
        </div>
    </form>
@endif
