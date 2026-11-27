<?php

namespace Database\Seeders;

use App\Enums\PromoType;
use App\Models\PromoCode;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    /**
     * Seed the demo promo code from the marketplace template.
     */
    public function run(): void
    {
        PromoCode::query()->updateOrCreate(
            ['code' => 'BYCLICK10'],
            [
                'type' => PromoType::Percent,
                'value' => 10,
                'min_total' => 0,
                'starts_at' => null,
                'ends_at' => null,
                'usage_limit' => null,
                'is_active' => true,
            ],
        );
    }
}
