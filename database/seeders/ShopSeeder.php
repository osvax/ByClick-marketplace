<?php

namespace Database\Seeders;

use App\Enums\ShopStatus;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Seed the demo seller shop.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);

        $seller = User::query()->where('email', 'seller@byclick.by')->firstOrFail();

        Shop::query()->updateOrCreate(
            ['slug' => 'byclick-store'],
            [
                'user_id' => $seller->id,
                'name' => 'ByClick Store',
                'description' => 'Официальный магазин маркетплейса ByClick: электроника, товары для дома и спорта.',
                'status' => ShopStatus::Active,
                'rating' => 4.9,
                'is_verified' => true,
            ],
        );
    }
}
