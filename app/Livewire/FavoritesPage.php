<?php

namespace App\Livewire;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class FavoritesPage extends Component
{
    /**
     * Reload the grid when any heart on the page is toggled.
     *
     * Parameters mirror the "favorites-updated" event so Livewire can call
     * this listener with the broadcast payload.
     */
    #[On('favorites-updated')]
    public function refreshPage(int $count = 0, ?int $productId = null, bool $active = false): void
    {
        // The render method reads fresh data; no extra work is needed here.
    }

    /**
     * Remove every favorite of the current visitor.
     */
    public function clear(): void
    {
        Favorite::clearForCurrent();

        $this->dispatch('favorites-updated', count: 0, productId: null, active: false);
        $this->dispatch('notify', message: 'Избранное очищено');
    }

    public function render(): View
    {
        /** @var Collection<int, Product> $products */
        $products = Favorite::query()
            ->forCurrentOwner()
            ->with(['product.mainImage', 'product.category.parent', 'product.shop'])
            ->latest()
            ->get()
            ->pluck('product')
            ->filter(fn ($product): bool => $product !== null && $product->is_active)
            ->values();

        return view('livewire.favorites-page', [
            'products' => $products,
        ]);
    }
}
