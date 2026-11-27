@props(['items' => []])

@if (count($items) > 0)
    <nav class="va_breadcrumbs" aria-label="Хлебные крошки">
        <ol class="va_breadcrumbs__list">
            <li><a href="{{ route('home') }}">Главная</a></li>
            @foreach ($items as $item)
                <li>
                    @if (! empty($item['url']))
                        <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    @else
                        <span aria-current="page">{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
