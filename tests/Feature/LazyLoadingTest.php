<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

it('renders authenticated pages without lazy loading queries', function () {
    $this->seed();

    $admin = User::query()->where('email', 'admin@byclick.by')->firstOrFail();
    $seller = User::query()->where('email', 'seller@byclick.by')->firstOrFail();
    $customer = User::query()->where('email', 'customer@byclick.by')->firstOrFail();

    Model::preventLazyLoading(true);

    try {
        $this->actingAs($admin);

        foreach (['/admin', '/admin/requests', '/admin/shops', '/admin/orders', '/admin/products', '/admin/categories', '/admin/users', '/admin/promocodes'] as $url) {
            $this->get($url)->assertOk();
        }

        $this->actingAs($seller);

        foreach (['/seller/dashboard', '/seller/products', '/seller/orders'] as $url) {
            $this->get($url)->assertOk();
        }

        $this->actingAs($customer);

        foreach (['/profile', '/orders', '/favorites', '/cart', '/checkout'] as $url) {
            $this->get($url)->assertOk();
        }
    } finally {
        Model::preventLazyLoading(false);
    }
});
