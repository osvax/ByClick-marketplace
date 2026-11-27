<?php

namespace App\Policies;

use App\Models\SellerRequest;
use App\Models\User;

class SellerRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function review(User $user, SellerRequest $sellerRequest): bool
    {
        return $user->isAdmin();
    }
}
