<?php

namespace Database\Seeders;

use App\Enums\DeliveryMethod;
use App\Models\DeliveryPoint;
use Illuminate\Database\Seeder;

class DeliveryPointSeeder extends Seeder
{
    /**
     * Demo pickup points and postamats.
     *
     * @var array<int, array{city: string, address: string, schedule: string, type: DeliveryMethod}>
     */
    private const POINTS = [
        [
            'city' => 'Минск',
            'address' => 'пр-т Независимости, 18',
            'schedule' => 'Ежедневно 09:00–21:00',
            'type' => DeliveryMethod::Pickup,
        ],
        [
            'city' => 'Минск',
            'address' => 'ул. Немига, 5',
            'schedule' => 'Круглосуточно',
            'type' => DeliveryMethod::Postamat,
        ],
        [
            'city' => 'Гомель',
            'address' => 'ул. Советская, 34',
            'schedule' => 'Ежедневно 09:00–20:00',
            'type' => DeliveryMethod::Pickup,
        ],
        [
            'city' => 'Брест',
            'address' => 'ул. Гоголя, 12',
            'schedule' => 'Круглосуточно',
            'type' => DeliveryMethod::Postamat,
        ],
        [
            'city' => 'Витебск',
            'address' => 'пр-т Фрунзе, 21',
            'schedule' => 'Ежедневно 10:00–20:00',
            'type' => DeliveryMethod::Pickup,
        ],
    ];

    /**
     * Seed the demo delivery points.
     */
    public function run(): void
    {
        foreach (self::POINTS as $point) {
            DeliveryPoint::query()->updateOrCreate(
                ['city' => $point['city'], 'address' => $point['address']],
                [
                    'schedule' => $point['schedule'],
                    'type' => $point['type'],
                    'is_active' => true,
                ],
            );
        }
    }
}
