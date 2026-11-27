<?php

namespace App\Livewire;

use App\Livewire\Concerns\InteractsWithSellerShop;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class SellerOrders extends Component
{
    use InteractsWithSellerShop, WithPagination;

    public function render(): View
    {
        $shop = $this->sellerShop();

        // Only orders that contain at least one product from this seller.
        $orders = $shop === null
            ? null
            : Order::query()
                ->whereHas('items.product', fn ($query) => $query->where('shop_id', $shop->id))
                ->with([
                    'items' => fn ($query) => $query
                        ->whereHas('product', fn ($product) => $product->where('shop_id', $shop->id))
                        ->with(['product.mainImage', 'product.category']),
                ])
                ->latest('created_at')
                ->paginate(10);

        return view('livewire.seller-orders', [
            'shop' => $shop,
            'orders' => $orders,
        ]);
    }
}
