<?php

namespace App\Policies;

use App\Models\PromoCode;
use App\Models\User;

class PromoCodePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, PromoCode $promoCode): bool
    {
        return $user->isAdmin();
    }
}
