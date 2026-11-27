<?php

namespace App\Livewire;

use App\Livewire\Concerns\InteractsWithSellerShop;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class SellerProducts extends Component
{
    use InteractsWithSellerShop, WithPagination;

    public string $search = '';

    /** Inline edit form. */
    public ?int $editingId = null;

    public string $editName = '';

    public string $editSlug = '';

    public string $editPrice = '';

    public string $editOldPrice = '';

    public string $editStock = '0';

    public string $editBrand = '';

    public string $editDescription = '';

    public ?int $editCategoryId = null;

    public bool $editActive = true;

    /** Create form. */
    public bool $showCreate = false;

    public string $newName = '';

    public string $newSlug = '';

    public string $newPrice = '';

    public string $newOldPrice = '';

    public string $newStock = '0';

    public string $newBrand = '';

    public string $newDescription = '';

    public ?int $newCategoryId = null;

    public bool $newActive = true;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Open the edit form for an own product.
     */
    public function edit(int $productId): void
    {
        $product = $this->ownedProduct($productId);
        Gate::authorize('update', $product);

        $this->editingId = $product->id;
        $this->showCreate = false;
        $this->editName = $product->name;
        $this->editSlug = $product->slug;
        $this->editPrice = $this->moneyInput($product->price);
        $this->editOldPrice = $product->old_price !== null ? $this->moneyInput($product->old_price) : '';
        $this->editStock = (string) $product->stock;
        $this->editBrand = $product->brand ?? '';
        $this->editDescription = $product->description ?? '';
        $this->editCategoryId = $product->category_id;
        $this->editActive = $product->is_active;
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->resetValidation();
    }

    /**
     * Save the edited product.
     */
    public function save(): void
    {
        if ($this->editingId === null) {
            return;
        }

        $product = $this->ownedProduct($this->editingId);
        Gate::authorize('update', $product);

        $this->editName = trim($this->editName);
        $this->editSlug = trim($this->editSlug);
        $this->editBrand = trim($this->editBrand);
        $this->editDescription = trim($this->editDescription);

        $this->validate([
            'editName' => ['required', 'string', 'max:255'],
            'editPrice' => ['required', 'numeric', 'min:0'],
            'editOldPrice' => $this->editOldPrice === '' ? ['nullable'] : ['required', 'numeric', 'min:0'],
            'editStock' => ['required', 'integer', 'min:0'],
            'editCategoryId' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where('is_active', true),
            ],
            'editBrand' => ['nullable', 'string', 'max:255'],
            'editDescription' => ['nullable', 'string', 'max:5000'],
            'editActive' => ['boolean'],
        ], attributes: [
            'editName' => 'название',
            'editPrice' => 'цена',
            'editOldPrice' => 'старая цена',
            'editStock' => 'остаток',
            'editCategoryId' => 'категория',
        ]);

        $price = $this->parseMoney($this->editPrice);
        $oldPrice = $this->editOldPrice !== '' ? $this->parseMoney($this->editOldPrice) : null;

        $product->update([
            'name' => $this->editName,
            'slug' => $this->uniqueSlug($this->editName, $this->editSlug, $product->id),
            'price' => $price,
            'old_price' => $oldPrice !== null && $oldPrice > 0 ? $oldPrice : null,
            'stock' => (int) $this->editStock,
            'brand' => $this->editBrand !== '' ? $this->editBrand : null,
            'description' => $this->editDescription !== '' ? $this->editDescription : null,
            'category_id' => $this->editCategoryId,
            'is_active' => $this->editActive,
        ]);

        $this->editingId = null;
        $this->dispatch('notify', message: 'Товар обновлён');
    }

    /**
     * Open the "new product" form.
     */
    public function createProduct(): void
    {
        Gate::authorize('create', Product::class);

        $this->showCreate = true;
        $this->editingId = null;
        $this->resetValidation();
    }

    public function cancelCreate(): void
    {
        $this->showCreate = false;
        $this->resetValidation();
    }

    /**
     * Create a product in the seller's shop.
     */
    public function store(): void
    {
        Gate::authorize('create', Product::class);

        $shop = $this->sellerShop();
        abort_if($shop === null, 403);

        $this->newName = trim($this->newName);
        $this->newSlug = trim($this->newSlug);
        $this->newBrand = trim($this->newBrand);
        $this->newDescription = trim($this->newDescription);

        $this->validate([
            'newName' => ['required', 'string', 'max:255'],
            'newPrice' => ['required', 'numeric', 'min:0'],
            'newOldPrice' => $this->newOldPrice === '' ? ['nullable'] : ['required', 'numeric', 'min:0'],
            'newStock' => ['required', 'integer', 'min:0'],
            'newCategoryId' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where('is_active', true),
            ],
            'newBrand' => ['nullable', 'string', 'max:255'],
            'newDescription' => ['nullable', 'string', 'max:5000'],
            'newActive' => ['boolean'],
        ], attributes: [
            'newName' => 'название',
            'newPrice' => 'цена',
            'newOldPrice' => 'старая цена',
            'newStock' => 'остаток',
            'newCategoryId' => 'категория',
        ]);

        $price = $this->parseMoney($this->newPrice);
        $oldPrice = $this->newOldPrice !== '' ? $this->parseMoney($this->newOldPrice) : null;

        Product::query()->create([
            'shop_id' => $shop->id,
            'category_id' => $this->newCategoryId,
            'slug' => $this->uniqueSlug($this->newName, $this->newSlug),
            'name' => $this->newName,
            'brand' => $this->newBrand !== '' ? $this->newBrand : null,
            'description' => $this->newDescription !== '' ? $this->newDescription : null,
            'price' => $price,
            'old_price' => $oldPrice !== null && $oldPrice > 0 ? $oldPrice : null,
            'stock' => (int) $this->newStock,
            'is_active' => $this->newActive,
        ]);

        $this->showCreate = false;
        $this->reset('newName', 'newSlug', 'newPrice', 'newOldPrice', 'newStock', 'newBrand', 'newDescription', 'newCategoryId');
        $this->newStock = '0';
        $this->newActive = true;

        $this->dispatch('notify', message: 'Товар добавлен');
    }

    /**
     * Hide or publish a product without opening the edit form.
     */
    public function toggleActive(int $productId): void
    {
        $product = $this->ownedProduct($productId);
        Gate::authorize('update', $product);

        $product->update(['is_active' => ! $product->is_active]);

        $this->dispatch('notify', message: $product->is_active ? 'Товар опубликован' : 'Товар скрыт');
    }

    public function render(): View
    {
        $shop = $this->sellerShop();

        $products = $shop === null
            ? null
            : Product::query()
                ->where('shop_id', $shop->id)
                ->when($this->search !== '', fn ($query) => $query
                    ->where('name', 'like', '%'.trim($this->search).'%'))
                ->with('category')
                ->latest('created_at')
                ->paginate(12);

        return view('livewire.seller-products', [
            'shop' => $shop,
            'products' => $products,
            'categories' => Category::query()
                ->active()
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Find a product that belongs to the current seller's shop.
     */
    protected function ownedProduct(int $productId): Product
    {
        $shop = $this->sellerShop();
        abort_if($shop === null, 403);

        return Product::query()
            ->where('shop_id', $shop->id)
            ->findOrFail($productId);
    }

    /**
     * Convert a "49.90" input into kopecks.
     */
    protected function parseMoney(string $value): int
    {
        return (int) round((float) str_replace(',', '.', $value) * 100);
    }

    /**
     * Format kopecks for a money input field.
     */
    protected function moneyInput(int $amount): string
    {
        return number_format($amount / 100, 2, '.', '');
    }

    /**
     * Build a unique slug for a product.
     */
    protected function uniqueSlug(string $name, string $slug, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug !== '' ? $slug : $name);

        if ($base === '') {
            $base = 'product';
        }

        $candidate = $base;
        $suffix = 1;

        while (
            Product::query()
                ->where('slug', $candidate)
                ->when($ignoreId !== null, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $candidate = $base.'-'.++$suffix;
        }

        return $candidate;
    }
}
