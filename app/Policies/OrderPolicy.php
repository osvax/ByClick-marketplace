<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSeller() || $user->isAdmin();
    }

    /**
     * A seller may view an order only when it contains their products.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isSeller()
            && $order->items()
                ->whereHas('product.shop', fn ($query) => $query->where('user_id', $user->id))
                ->exists();
    }

    public function updateStatus(User $user): bool
    {
        return $user->isAdmin();
    }
}
