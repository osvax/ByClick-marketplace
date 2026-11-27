<?php

use App\Models\PromoCode;

it('filters out inactive and not started promo codes', function () {
    $active = PromoCode::factory()->create();
    PromoCode::factory()->inactive()->create();
    PromoCode::factory()->create(['starts_at' => now()->addDay()]);
    PromoCode::factory()->create(['ends_at' => now()->subDay()]);

    expect(PromoCode::query()->active()->pluck('id')->all())->toBe([$active->id]);
});
