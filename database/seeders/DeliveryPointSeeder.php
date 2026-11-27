<?php

namespace Database\Seeders;

use App\Enums\DeliveryMethod;
use App\Models\DeliveryPoint;
use Illuminate\Database\Seeder;

class DeliveryPointSeeder extends Seeder
{
    /**
     * Demo pickup points and postamats with coordinates for the map.
     *
     * @var array<int, array{city: string, address: string, latitude: float, longitude: float, schedule: string, type: DeliveryMethod}>
     */
    private const POINTS = [
        [
            'city' => 'Минск',
            'address' => 'пр-т Независимости, 18',
            'latitude' => 53.9045,
            'longitude' => 27.5615,
            'schedule' => 'Ежедневно 09:00–21:00',
            'type' => DeliveryMethod::Pickup,
        ],
        [
            'city' => 'Минск',
            'address' => 'ул. Немига, 5',
            'latitude' => 53.9028,
            'longitude' => 27.5537,
            'schedule' => 'Круглосуточно',
            'type' => DeliveryMethod::Postamat,
        ],
        [
            'city' => 'Гомель',
            'address' => 'ул. Советская, 34',
            'latitude' => 52.4412,
            'longitude' => 30.9878,
            'schedule' => 'Ежедневно 09:00–20:00',
            'type' => DeliveryMethod::Pickup,
        ],
        [
            'city' => 'Брест',
            'address' => 'ул. Гоголя, 12',
            'latitude' => 52.0933,
            'longitude' => 23.6855,
            'schedule' => 'Круглосуточно',
            'type' => DeliveryMethod::Postamat,
        ],
        [
            'city' => 'Витебск',
            'address' => 'пр-т Фрунзе, 21',
            'latitude' => 55.1904,
            'longitude' => 30.2049,
            'schedule' => 'Ежедневно 10:00–20:00',
            'type' => DeliveryMethod::Pickup,
        ],
        [
            'city' => 'Гродно',
            'address' => 'ул. Советская, 8',
            'latitude' => 53.6694,
            'longitude' => 23.8131,
            'schedule' => 'Ежедневно 09:00–21:00',
            'type' => DeliveryMethod::Pickup,
        ],
        [
            'city' => 'Могилёв',
            'address' => 'ул. Первомайская, 57',
            'latitude' => 53.9006,
            'longitude' => 30.3314,
            'schedule' => 'Ежедневно 09:00–20:00',
            'type' => DeliveryMethod::Pickup,
        ],
        [
            'city' => 'Бобруйск',
            'address' => 'ул. Социалистическая, 44',
            'latitude' => 53.1384,
            'longitude' => 29.2214,
            'schedule' => 'Круглосуточно',
            'type' => DeliveryMethod::Postamat,
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
                    'latitude' => $point['latitude'],
                    'longitude' => $point['longitude'],
                    'schedule' => $point['schedule'],
                    'type' => $point['type'],
                    'is_active' => true,
                ],
            );
        }
    }
}
