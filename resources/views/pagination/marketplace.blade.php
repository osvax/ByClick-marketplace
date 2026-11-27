@if ($paginator->hasPages())
    <nav class="va_pagination" aria-label="Страницы каталога">
        {{-- Назад --}}
        <button
            class="va_pagination__item"
            type="button"
            aria-label="Предыдущая страница"
            @disabled($paginator->onFirstPage())
            wire:click="previousPage('page')"
        >
            <svg class="va_icon" aria-hidden="true"><use href="#va-i-chevron-left" /></svg>
        </button>

        {{-- Первая страница и многоточие --}}
        @if ($paginator->currentPage() > 3)
            <button class="va_pagination__item" type="button" wire:click="gotoPage(1, 'page')">1</button>
            @if ($paginator->currentPage() > 4)
                <span class="va_pagination__dots">…</span>
            @endif
        @endif

        {{-- Окно вокруг текущей страницы --}}
        @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
            <button
                class="va_pagination__item @if ($page === $paginator->currentPage()) is-active @endif"
                type="button"
                @if ($page === $paginator->currentPage()) aria-current="page" @endif
                wire:click="gotoPage({{ $page }}, 'page')"
            >{{ $page }}</button>
        @endforeach

        {{-- Последняя страница и многоточие --}}
        @if ($paginator->currentPage() < $paginator->lastPage() - 2)
            @if ($paginator->currentPage() < $paginator->lastPage() - 3)
                <span class="va_pagination__dots">…</span>
            @endif
            <button class="va_pagination__item" type="button" wire:click="gotoPage({{ $paginator->lastPage() }}, 'page')">
                {{ $paginator->lastPage() }}
            </button>
        @endif

        {{-- Вперёд --}}
        <button
            class="va_pagination__item"
            type="button"
            aria-label="Следующая страница"
            @disabled(! $paginator->hasMorePages())
            wire:click="nextPage('page')"
        >
            <svg class="va_icon" aria-hidden="true"><use href="#va-i-chevron-right" /></svg>
        </button>
    </nav>
@endif
