<?php

namespace App\Livewire;

use App\Enums\OrderStatus;
use App\Livewire\Concerns\InteractsWithSellerShop;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SellerDashboard extends Component
{
    use InteractsWithSellerShop;

    public function render(): View
    {
        $shop = $this->sellerShop();

        if ($shop === null) {
            return view('livewire.seller-dashboard', [
                'shop' => null,
            ]);
        }

        $productsQuery = Product::query()->where('shop_id', $shop->id);

        $stats = [
            'products' => (clone $productsQuery)->count(),
            'active' => (clone $productsQuery)->where('is_active', true)->count(),
            'low_stock' => (clone $productsQuery)->where('stock', '<=', 3)->where('is_active', true)->count(),
            'orders' => Order::query()
                ->whereHas('items.product', fn ($query) => $query->where('shop_id', $shop->id))
                ->count(),
            'revenue' => (int) OrderItem::query()
                ->whereHas('product', fn ($query) => $query->where('shop_id', $shop->id))
                ->whereHas('order', fn ($query) => $query->where('status', '!=', OrderStatus::Cancelled))
                ->sum('total'),
        ];

        $lowStock = Product::query()
            ->where('shop_id', $shop->id)
            ->where('is_active', true)
            ->where('stock', '<=', 3)
            ->orderBy('stock')
            ->limit(5)
            ->get();

        $recentOrders = Order::query()
            ->whereHas('items.product', fn ($query) => $query->where('shop_id', $shop->id))
            ->with([
                'items' => fn ($query) => $query
                    ->whereHas('product', fn ($product) => $product->where('shop_id', $shop->id))
                    ->with('product.mainImage'),
            ])
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('livewire.seller-dashboard', [
            'shop' => $shop,
            'stats' => $stats,
            'lowStock' => $lowStock,
            'recentOrders' => $recentOrders,
        ]);
    }
}
