<x-layouts.app
    title="Каталог — ByClick"
    description="Каталог ByClick: электроника, одежда, товары для дома, красоты и спорта. Фильтры по цене, рейтингу и наличию, доставка от 1 дня."
    :canonical="route('catalog')"
>
    <div class="va_container">
        {{-- Фильтры, сортировка и результаты живут в Livewire-компоненте --}}
        <livewire:catalog />
    </div>
</x-layouts.app>
