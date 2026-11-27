<?php

namespace App\Livewire\Concerns;

use App\Models\Shop;

trait InteractsWithSellerShop
{
    /**
     * Get the shop of the authenticated seller, if it exists.
     */
    protected function sellerShop(): ?Shop
    {
        return Shop::query()
            ->where('user_id', (int) auth()->id())
            ->first();
    }
}
