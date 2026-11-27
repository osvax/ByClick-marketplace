<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Catalog page with URL-driven filters, sorting and pagination.
 *
 * @property-read Collection<int, Category> $categories
 * @property-read array<int, int> $categoryCounts
 * @property-read Category|null $selectedCategory
 * @property-read array{min: int, max: int} $priceBounds
 * @property-read LengthAwarePaginator<int, Product> $products
 * @property-read array<int, array{key: string, label: string}> $activeFilters
 * @property-read array<int, array{label: string, url: string|null}> $breadcrumbs
 * @property-read string $pageTitle
 * @property-read string $pageSubtitle
 */
class Catalog extends Component
{
    use WithPagination;

    /**
     * Available sorting options: query value => label shown in the toolbar.
     */
    public const SORTS = [
        'popular' => 'По популярности',
        'cheap' => 'Сначала дешёвые',
        'expensive' => 'Сначала дорогие',
        'rating' => 'По рейтингу',
        'discount' => 'По размеру скидки',
    ];

    /**
     * Filters that must reset the paginator to the first page when changed.
     */
    private const FILTERS = ['category', 'sub', 'minPrice', 'maxPrice', 'rating', 'sort', 'tag', 'q', 'inStock'];

    /** Root category slug, e.g. "electronics". */
    #[Url(except: '')]
    public string $category = '';

    /** Child category slug inside the selected root category, e.g. "audio". */
    #[Url(except: '')]
    public string $sub = '';

    /** Minimum price in kopecks (null means no lower bound). */
    #[Url(as: 'price_min', except: null)]
    public ?int $minPrice = null;

    /** Maximum price in kopecks (null means no upper bound). */
    #[Url(as: 'price_max', except: null)]
    public ?int $maxPrice = null;

    /** Minimum rating as a string ("", "4.5" or "4.8") to keep URLs readable. */
    #[Url(except: '')]
    public string $rating = '';

    /** Sorting key, one of self::SORTS. */
    #[Url(except: 'popular')]
    public string $sort = 'popular';

    /** Special tag coming from links like ?tag=sale. */
    #[Url(except: '')]
    public string $tag = '';

    /** Search term passed from the header search form. */
    #[Url(except: '')]
    public string $q = '';

    /** Show only products that are currently in stock. */
    #[Url(as: 'in_stock', except: false)]
    public bool $inStock = false;

    /** Mobile filters drawer state (kept out of the URL). */
    public bool $filtersOpen = false;

    /**
     * Normalise values that can arrive from a hand-written URL.
     */
    public function mount(): void
    {
        $this->sort = array_key_exists($this->sort, self::SORTS) ? $this->sort : 'popular';
        $this->rating = in_array($this->rating, ['', '4.5', '4.8'], true) ? $this->rating : '';
        $this->tag = $this->tag === 'sale' ? 'sale' : '';
        $this->q = trim($this->q);

        // Clamp prices to the real catalog range when both bounds are present.
        $bounds = $this->priceBounds();

        if ($this->minPrice !== null) {
            $this->minPrice = max($bounds['min'], $this->minPrice);
        }

        if ($this->maxPrice !== null) {
            $this->maxPrice = min($bounds['max'], $this->maxPrice);
        }

        if ($this->minPrice !== null && $this->maxPrice !== null && $this->minPrice > $this->maxPrice) {
            [$this->minPrice, $this->maxPrice] = [null, null];
        }
    }

    /**
     * Keep dependent filters consistent and reset pagination after any filter change.
     */
    public function updated(string $property): void
    {
        // A subcategory only makes sense inside the selected root category.
        if ($property === 'category') {
            $this->sub = '';
        }

        if ($property === 'minPrice' && $this->minPrice !== null && $this->maxPrice !== null && $this->minPrice > $this->maxPrice) {
            $this->maxPrice = $this->minPrice;
        }

        if ($property === 'maxPrice' && $this->minPrice !== null && $this->maxPrice !== null && $this->maxPrice < $this->minPrice) {
            $this->minPrice = $this->maxPrice;
        }

        if (in_array($property, self::FILTERS, true)) {
            $this->resetPage();
        }
    }

    /**
     * Toggle the "sale only" filter used by the ?tag=sale links.
     */
    public function toggleSale(): void
    {
        $this->tag = $this->tag === 'sale' ? '' : 'sale';
        $this->resetPage();
    }

    /**
     * Remove a single active filter chip.
     */
    public function removeFilter(string $key): void
    {
        switch ($key) {
            case 'category':
                $this->category = '';
                $this->sub = '';
                break;
            case 'sub':
                $this->sub = '';
                break;
            case 'minPrice':
                $this->minPrice = null;
                break;
            case 'maxPrice':
                $this->maxPrice = null;
                break;
            case 'rating':
                $this->rating = '';
                break;
            case 'tag':
                $this->tag = '';
                break;
            case 'inStock':
                $this->inStock = false;
                break;
            case 'q':
                $this->q = '';
                break;
        }

        $this->resetPage();
    }

    /**
     * Reset every filter and the sorting back to defaults.
     */
    public function resetFilters(): void
    {
        $this->category = '';
        $this->sub = '';
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->rating = '';
        $this->sort = 'popular';
        $this->tag = '';
        $this->q = '';
        $this->inStock = false;
        $this->filtersOpen = false;

        $this->resetPage();
    }

    public function openFilters(): void
    {
        $this->filtersOpen = true;
    }

    public function closeFilters(): void
    {
        $this->filtersOpen = false;
    }

    /**
     * Catalog tree used by the filters panel.
     *
     * @return Collection<int, Category>
     */
    #[Computed]
    public function categories(): Collection
    {
        return Category::query()
            ->roots()
            ->active()
            ->ordered()
            ->with(['children' => fn ($query) => $query->where('is_active', true)])
            ->get();
    }

    /**
     * Active product totals per root category (including child categories).
     *
     * @return array<int, int>
     */
    #[Computed]
    public function categoryCounts(): array
    {
        return Category::activeProductCounts();
    }

    /**
     * Currently selected root category, if any.
     */
    #[Computed]
    public function selectedCategory(): ?Category
    {
        return $this->category === '' ? null : $this->categories->firstWhere('slug', $this->category);
    }

    /**
     * Real price range of the active catalog, used by the range slider.
     *
     * @return array{min: int, max: int}
     */
    #[Computed]
    public function priceBounds(): array
    {
        $range = Product::query()
            ->active()
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return [
            'min' => (int) ($range->min_price ?? 0),
            'max' => (int) ($range->max_price ?? 0),
        ];
    }

    /**
     * Filtered and paginated products.
     *
     * @return LengthAwarePaginator<int, Product>
     */
    #[Computed]
    public function products(): LengthAwarePaginator
    {
        return $this->filteredQuery()
            ->paginate((int) config('marketplace.catalog.per_page', 12))
            ->withQueryString();
    }

    /**
     * Chips describing the currently applied filters.
     *
     * @return array<int, array{key: string, label: string}>
     */
    #[Computed]
    public function activeFilters(): array
    {
        $chips = [];
        $root = $this->selectedCategory;

        if ($root !== null) {
            $chips[] = ['key' => 'category', 'label' => $root->name];
        }

        if ($root !== null && $this->sub !== '') {
            $child = $root->children->firstWhere('slug', $this->sub);

            if ($child !== null) {
                $chips[] = ['key' => 'sub', 'label' => $child->name];
            }
        }

        $bounds = $this->priceBounds;

        if ($this->minPrice !== null && $this->minPrice > $bounds['min']) {
            $chips[] = ['key' => 'minPrice', 'label' => 'Цена от '.$this->formatPrice($this->minPrice)];
        }

        if ($this->maxPrice !== null && $this->maxPrice < $bounds['max']) {
            $chips[] = ['key' => 'maxPrice', 'label' => 'Цена до '.$this->formatPrice($this->maxPrice)];
        }

        if ($this->rating !== '') {
            $chips[] = ['key' => 'rating', 'label' => 'Рейтинг '.$this->rating.' и выше'];
        }

        if ($this->tag === 'sale') {
            $chips[] = ['key' => 'tag', 'label' => 'Только со скидкой'];
        }

        if ($this->inStock) {
            $chips[] = ['key' => 'inStock', 'label' => 'В наличии'];
        }

        if ($this->q !== '') {
            $chips[] = ['key' => 'q', 'label' => 'Поиск: '.$this->q];
        }

        return $chips;
    }

    /**
     * Breadcrumbs for the catalog page.
     *
     * @return array<int, array{label: string, url: string|null}>
     */
    #[Computed]
    public function breadcrumbs(): array
    {
        $root = $this->selectedCategory;

        if ($root === null) {
            return [['label' => 'Каталог', 'url' => null]];
        }

        $child = $this->sub !== '' ? $root->children->firstWhere('slug', $this->sub) : null;

        $items = [
            ['label' => 'Каталог', 'url' => url('/catalog')],
            [
                'label' => $root->name,
                'url' => $child !== null ? url('/catalog?category='.$root->slug) : null,
            ],
        ];

        if ($child !== null) {
            $items[] = ['label' => $child->name, 'url' => null];
        }

        return $items;
    }

    /**
     * H1 for the current filter state.
     */
    #[Computed]
    public function pageTitle(): string
    {
        $root = $this->selectedCategory;

        if ($root === null) {
            return 'Каталог';
        }

        if ($this->sub !== '') {
            $child = $root->children->firstWhere('slug', $this->sub);

            if ($child !== null) {
                return $child->name;
            }
        }

        return $root->name;
    }

    /**
     * Subtitle for the current filter state.
     */
    #[Computed]
    public function pageSubtitle(): string
    {
        $root = $this->selectedCategory;

        if ($root === null) {
            return 'Товары от проверенных продавцов, доставка от 1 дня и возврат 30 дней';
        }

        return 'Товары категории «'.$root->name.'» от проверенных продавцов';
    }

    /**
     * Format kopecks for human-readable labels.
     */
    public function formatPrice(int $amount): string
    {
        $scale = (int) config('marketplace.currency_scale', 100);

        return number_format($amount / $scale, 2, ',', ' ').' '.config('marketplace.currency', 'BYN');
    }

    /**
     * Build the catalog query from the current filter state.
     *
     * @return Builder<Product>
     */
    protected function filteredQuery(): Builder
    {
        return Product::query()
            ->active()
            ->with(['category.parent', 'mainImage'])
            ->when($this->category !== '', fn (Builder $query) => $query->whereHas(
                'category.parent',
                fn (Builder $parent) => $parent->where('slug', $this->category),
            ))
            ->when($this->sub !== '', fn (Builder $query) => $query->whereHas(
                'category',
                fn (Builder $category) => $category->where('slug', $this->sub),
            ))
            ->when($this->minPrice !== null, fn (Builder $query) => $query->where('price', '>=', $this->minPrice))
            ->when($this->maxPrice !== null, fn (Builder $query) => $query->where('price', '<=', $this->maxPrice))
            ->when($this->rating !== '', fn (Builder $query) => $query->where('rating', '>=', (float) $this->rating))
            ->when($this->inStock, fn (Builder $query) => $query->where('stock', '>', 0))
            ->when($this->tag === 'sale', fn (Builder $query) => $query
                ->whereNotNull('old_price')
                ->whereColumn('old_price', '>', 'price'))
            ->when($this->q !== '', function (Builder $query): void {
                // search_text is pre-normalized to lowercase, so this works
                // the same on MySQL and SQLite regardless of input case.
                $query->where('search_text', 'like', '%'.mb_strtolower($this->q).'%');
            })
            ->tap(fn (Builder $query) => $this->applySorting($query));
    }

    /**
     * Apply the selected sorting strategy.
     *
     * @param  Builder<Product>  $query
     */
    protected function applySorting(Builder $query): void
    {
        match ($this->sort) {
            'cheap' => $query->orderBy('price'),
            'expensive' => $query->orderByDesc('price'),
            'rating' => $query->orderByDesc('rating')->orderByDesc('reviews_count'),
            'discount' => $query->orderByRaw(
                'CASE WHEN old_price IS NULL OR old_price = 0 THEN 0 ELSE (old_price - price) * 1.0 / old_price END DESC',
            ),
            default => $query->orderByDesc('sold_count')->orderByDesc('rating'),
        };
    }
}
