<?php

namespace App\Livewire;

use App\Enums\OrderStatus;
use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Models\Order;
use App\Models\Product;
use App\Models\SellerRequest;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function render(): View
    {
        $stats = [
            'users' => User::count(),
            'sellers' => User::query()->where('role', UserRole::Seller)->count(),
            'shops' => Shop::count(),
            'pending_shops' => Shop::query()->where('status', ShopStatus::Pending)->count(),
            'products' => Product::count(),
            'active_products' => Product::query()->where('is_active', true)->count(),
            'orders' => Order::count(),
            'revenue' => (int) Order::query()->where('status', '!=', OrderStatus::Cancelled)->sum('total'),
        ];

        return view('livewire.admin-dashboard', [
            'stats' => $stats,
            'pendingRequests' => SellerRequest::query()
                ->pending()
                ->with(['user', 'category'])
                ->latest('created_at')
                ->limit(5)
                ->get(),
            'recentOrders' => Order::query()
                ->with('user')
                ->latest('created_at')
                ->limit(6)
                ->get(),
        ]);
    }
}
