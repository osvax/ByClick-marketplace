<?php

namespace App\Livewire;

use App\Livewire\Concerns\RepeatsOrders;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class OrdersPage extends Component
{
    use RepeatsOrders, WithPagination;

    public function render(): View
    {
        $orders = Order::query()
            ->where('user_id', (int) auth()->id())
            ->with(['items.product.mainImage', 'items.product.category'])
            ->latest('created_at')
            ->paginate(10);

        return view('livewire.orders-page', [
            'orders' => $orders,
        ]);
    }
}
