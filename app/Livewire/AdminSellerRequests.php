<?php

namespace App\Livewire;

use App\Enums\SellerRequestStatus;
use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Models\SellerRequest;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class AdminSellerRequests extends Component
{
    use WithPagination;

    public string $status = 'new';

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Switch the status filter.
     */
    public function setStatus(string $status): void
    {
        if (! in_array($status, ['new', 'approved', 'rejected', 'all'], true)) {
            return;
        }

        $this->status = $status;
        $this->resetPage();
    }

    /**
     * Approve a seller request, open a shop and promote the user to seller.
     */
    public function approve(int $requestId): void
    {
        $sellerRequest = SellerRequest::query()->with('user')->findOrFail($requestId);
        Gate::authorize('review', $sellerRequest);

        if ($sellerRequest->status !== SellerRequestStatus::New) {
            $this->dispatch('notify', message: 'Заявка уже обработана');

            return;
        }

        $user = $sellerRequest->user;

        if ($user === null) {
            $this->addError('review-'.$sellerRequest->id, 'Заявка оставлена гостем: сначала нужно зарегистрировать пользователя.');

            return;
        }

        DB::transaction(function () use ($sellerRequest, $user): void {
            $shopName = trim((string) ($sellerRequest->shop_name ?: $user->name));

            if ($user->shop !== null) {
                $user->shop->update([
                    'name' => $shopName,
                    'status' => ShopStatus::Active,
                ]);
            } else {
                Shop::query()->create([
                    'user_id' => $user->id,
                    'name' => $shopName,
                    'slug' => $this->uniqueShopSlug($shopName),
                    'status' => ShopStatus::Active,
                    'rating' => 0,
                    'is_verified' => false,
                ]);
            }

            if ($user->role === UserRole::Customer) {
                $user->update(['role' => UserRole::Seller]);
            }

            $sellerRequest->update(['status' => SellerRequestStatus::Approved]);
        });

        $this->dispatch('notify', message: 'Заявка одобрена, магазин активирован');
    }

    /**
     * Reject a seller request.
     */
    public function reject(int $requestId): void
    {
        $sellerRequest = SellerRequest::query()->findOrFail($requestId);
        Gate::authorize('review', $sellerRequest);

        if ($sellerRequest->status !== SellerRequestStatus::New) {
            $this->dispatch('notify', message: 'Заявка уже обработана');

            return;
        }

        $sellerRequest->update(['status' => SellerRequestStatus::Rejected]);

        $this->dispatch('notify', message: 'Заявка отклонена');
    }

    public function render(): View
    {
        Gate::authorize('viewAny', SellerRequest::class);

        $requests = SellerRequest::query()
            ->with(['user', 'category'])
            ->when($this->status !== 'all', fn ($query) => $query->where('status', $this->status))
            ->when($this->search !== '', function ($query): void {
                $term = '%'.trim($this->search).'%';

                $query->where(function ($query) use ($term): void {
                    $query->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('shop_name', 'like', $term);
                });
            })
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.admin-seller-requests', [
            'requests' => $requests,
            'counts' => [
                'new' => SellerRequest::query()->where('status', SellerRequestStatus::New)->count(),
                'approved' => SellerRequest::query()->where('status', SellerRequestStatus::Approved)->count(),
                'rejected' => SellerRequest::query()->where('status', SellerRequestStatus::Rejected)->count(),
            ],
        ]);
    }

    /**
     * Build a unique shop slug from a human-readable name.
     */
    protected function uniqueShopSlug(string $name): string
    {
        $base = Str::slug($name);

        if ($base === '') {
            $base = 'shop';
        }

        $candidate = $base;
        $suffix = 1;

        while (Shop::query()->where('slug', $candidate)->exists()) {
            $candidate = $base.'-'.++$suffix;
        }

        return $candidate;
    }
}
