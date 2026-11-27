<?php

namespace App\Livewire;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Invisible component present on every page.
 *
 * Product cards and the product page only dispatch a browser-level
 * "favorite-toggle" event; this component persists the change and broadcasts
 * the new state back to the UI.
 */
class FavoriteSync extends Component
{
    #[On('favorite-toggle')]
    public function toggle(int $productId): void
    {
        $product = Product::query()->active()->find($productId);

        if ($product === null) {
            return;
        }

        $active = Favorite::toggle($product);

        $this->dispatch(
            'favorites-updated',
            count: Favorite::countForCurrent(),
            productId: $product->id,
            active: $active,
        );

        $this->dispatch(
            'notify',
            message: $active ? 'Добавлено в избранное' : 'Удалено из избранного',
        );
    }

    public function render(): View
    {
        return view('livewire.favorite-sync');
    }
}
