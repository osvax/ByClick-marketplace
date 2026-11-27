<?php

namespace App\Livewire;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class AdminOrders extends Component
{
    use WithPagination;

    public string $status = 'all';

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setStatus(string $status): void
    {
        if ($status !== 'all' && OrderStatus::tryFrom($status) === null) {
            return;
        }

        $this->status = $status;
        $this->resetPage();
    }

    /**
     * Change the order status from the admin panel.
     */
    public function updateStatus(int $orderId, string $status): void
    {
        Gate::authorize('updateStatus', Order::class);

        $newStatus = OrderStatus::tryFrom($status);

        if ($newStatus === null) {
            return;
        }

        $order = Order::query()->findOrFail($orderId);
        $order->update(['status' => $newStatus]);

        $this->dispatch('notify', message: 'Статус заказа обновлён: '.$newStatus->label());
    }

    public function render(): View
    {
        Gate::authorize('viewAny', Order::class);

        $orders = Order::query()
            ->with('user')
            ->when($this->status !== 'all', fn ($query) => $query->where('status', $this->status))
            ->when($this->search !== '', function ($query): void {
                $term = '%'.trim($this->search).'%';

                $query->where(function ($query) use ($term): void {
                    $query->where('number', 'like', $term)
                        ->orWhere('customer_name', 'like', $term)
                        ->orWhere('customer_phone', 'like', $term)
                        ->orWhere('customer_email', 'like', $term);
                });
            })
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.admin-orders', [
            'orders' => $orders,
            'statuses' => OrderStatus::cases(),
        ]);
    }
}
