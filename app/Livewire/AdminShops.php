<?php

namespace App\Livewire;

use App\Enums\ShopStatus;
use App\Models\Shop;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class AdminShops extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = 'all';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setStatus(string $status): void
    {
        if (! in_array($status, ['all', 'active', 'pending', 'suspended'], true)) {
            return;
        }

        $this->status = $status;
        $this->resetPage();
    }

    /**
     * Suspend or re-activate a shop.
     */
    public function toggleStatus(int $shopId): void
    {
        $shop = Shop::query()->findOrFail($shopId);
        Gate::authorize('update', $shop);

        $newStatus = $shop->status === ShopStatus::Active
            ? ShopStatus::Suspended
            : ShopStatus::Active;

        $shop->update(['status' => $newStatus]);

        $this->dispatch('notify', message: $newStatus === ShopStatus::Active
            ? 'Магазин активирован'
            : 'Магазин заблокирован');
    }

    /**
     * Toggle the verified badge of a shop.
     */
    public function toggleVerified(int $shopId): void
    {
        $shop = Shop::query()->findOrFail($shopId);
        Gate::authorize('update', $shop);

        $shop->update(['is_verified' => ! $shop->is_verified]);

        $this->dispatch('notify', message: $shop->is_verified
            ? 'Магазин отмечен как проверенный'
            : 'Отметка проверенного магазина снята');
    }

    public function render(): View
    {
        Gate::authorize('viewAny', Shop::class);

        $shops = Shop::query()
            ->with('user')
            ->withCount('products')
            ->when($this->status !== 'all', fn ($query) => $query->where('status', $this->status))
            ->when($this->search !== '', function ($query): void {
                $term = '%'.trim($this->search).'%';

                $query->where(function ($query) use ($term): void {
                    $query->where('name', 'like', $term)
                        ->orWhereHas('user', fn ($user) => $user
                            ->where('name', 'like', $term)
                            ->orWhere('email', 'like', $term));
                });
            })
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.admin-shops', compact('shops'));
    }
}
