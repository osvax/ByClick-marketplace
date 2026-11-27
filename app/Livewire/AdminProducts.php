<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Shop;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class AdminProducts extends Component
{
    use WithPagination;

    public string $search = '';

    public string $shopId = '';

    /** Inline moderation form. */
    public ?int $editingId = null;

    public string $editPrice = '';

    public string $editStock = '0';

    public bool $editActive = true;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedShopId(): void
    {
        $this->resetPage();
    }

    /**
     * Open the quick edit form for a product.
     */
    public function edit(int $productId): void
    {
        $product = Product::query()->findOrFail($productId);
        Gate::authorize('update', $product);

        $this->editingId = $product->id;
        $this->editPrice = number_format($product->price / 100, 2, '.', '');
        $this->editStock = (string) $product->stock;
        $this->editActive = $product->is_active;
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->resetValidation();
    }

    /**
     * Save price, stock and publication state.
     */
    public function save(): void
    {
        if ($this->editingId === null) {
            return;
        }

        $product = Product::query()->findOrFail($this->editingId);
        Gate::authorize('update', $product);

        $this->validate([
            'editPrice' => ['required', 'numeric', 'min:0'],
            'editStock' => ['required', 'integer', 'min:0'],
            'editActive' => ['boolean'],
        ], attributes: [
            'editPrice' => 'цена',
            'editStock' => 'остаток',
        ]);

        $product->update([
            'price' => (int) round((float) str_replace(',', '.', $this->editPrice) * 100),
            'stock' => (int) $this->editStock,
            'is_active' => $this->editActive,
        ]);

        $this->editingId = null;
        $this->dispatch('notify', message: 'Товар обновлён');
    }

    /**
     * Hide or publish a product.
     */
    public function toggleActive(int $productId): void
    {
        $product = Product::query()->findOrFail($productId);
        Gate::authorize('update', $product);

        $product->update(['is_active' => ! $product->is_active]);

        $this->dispatch('notify', message: $product->is_active ? 'Товар опубликован' : 'Товар скрыт');
    }

    public function render(): View
    {
        Gate::authorize('viewAny', Product::class);

        $products = Product::query()
            ->with(['shop', 'category'])
            ->when($this->search !== '', function ($query): void {
                $term = '%'.trim($this->search).'%';

                $query->where(function ($query) use ($term): void {
                    $query->where('name', 'like', $term)
                        ->orWhere('brand', 'like', $term);
                });
            })
            ->when($this->shopId !== '', fn ($query) => $query->where('shop_id', (int) $this->shopId))
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.admin-products', [
            'products' => $products,
            'shops' => Shop::query()->orderBy('name')->get(),
        ]);
    }
}
