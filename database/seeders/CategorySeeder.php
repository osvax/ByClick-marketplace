<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Catalog tree from the marketplace template.
     *
     * @var array<int, array{slug: string, name: string, icon: string, subs: array<int, array{slug: string, name: string}>}>
     */
    private const CATEGORIES = [
        [
            'slug' => 'electronics',
            'name' => 'Электроника',
            'icon' => 'device',
            'subs' => [
                ['slug' => 'audio', 'name' => 'Наушники и колонки'],
                ['slug' => 'gadgets', 'name' => 'Смарт-часы и гаджеты'],
                ['slug' => 'keyboards', 'name' => 'Клавиатуры и мыши'],
                ['slug' => 'cameras', 'name' => 'Фото и видео'],
                ['slug' => 'tv', 'name' => 'ТВ и проекторы'],
                ['slug' => 'accessories', 'name' => 'Аксессуары и зарядка'],
            ],
        ],
        [
            'slug' => 'clothes',
            'name' => 'Одежда и обувь',
            'icon' => 'shirt',
            'subs' => [
                ['slug' => 'clothes', 'name' => 'Одежда'],
                ['slug' => 'shoes', 'name' => 'Обувь'],
                ['slug' => 'bags', 'name' => 'Сумки и рюкзаки'],
                ['slug' => 'accessories', 'name' => 'Аксессуары'],
            ],
        ],
        [
            'slug' => 'home',
            'name' => 'Дом и сад',
            'icon' => 'home',
            'subs' => [
                ['slug' => 'tableware', 'name' => 'Посуда'],
                ['slug' => 'lighting', 'name' => 'Освещение'],
                ['slug' => 'plants', 'name' => 'Растения и кашпо'],
                ['slug' => 'textiles', 'name' => 'Текстиль'],
                ['slug' => 'decor', 'name' => 'Декор'],
            ],
        ],
        [
            'slug' => 'beauty',
            'name' => 'Красота',
            'icon' => 'beauty',
            'subs' => [
                ['slug' => 'fragrance', 'name' => 'Парфюмерия'],
                ['slug' => 'skincare', 'name' => 'Уход за кожей'],
                ['slug' => 'makeup', 'name' => 'Макияж'],
                ['slug' => 'hygiene', 'name' => 'Гигиена'],
            ],
        ],
        [
            'slug' => 'kids',
            'name' => 'Детям',
            'icon' => 'kids',
            'subs' => [
                ['slug' => 'toys', 'name' => 'Игрушки'],
                ['slug' => 'clothes', 'name' => 'Детская одежда'],
                ['slug' => 'school', 'name' => 'Школа и творчество'],
                ['slug' => 'strollers', 'name' => 'Коляски и автокресла'],
            ],
        ],
        [
            'slug' => 'sport',
            'name' => 'Спорт',
            'icon' => 'sport',
            'subs' => [
                ['slug' => 'shoes', 'name' => 'Кроссовки и обувь'],
                ['slug' => 'outdoor', 'name' => 'Туризм и отдых'],
                ['slug' => 'fitness', 'name' => 'Фитнес'],
                ['slug' => 'tourism', 'name' => 'Товары для похода'],
            ],
        ],
        [
            'slug' => 'auto',
            'name' => 'Авто',
            'icon' => 'auto',
            'subs' => [
                ['slug' => 'accessories', 'name' => 'Аксессуары'],
                ['slug' => 'care', 'name' => 'Автохимия'],
                ['slug' => 'tools', 'name' => 'Инструменты'],
                ['slug' => 'electronics', 'name' => 'Автоэлектроника'],
            ],
        ],
        [
            'slug' => 'pets',
            'name' => 'Зоотовары',
            'icon' => 'paw',
            'subs' => [
                ['slug' => 'beds', 'name' => 'Лежанки и домики'],
                ['slug' => 'food', 'name' => 'Корм и лакомства'],
                ['slug' => 'toys', 'name' => 'Игрушки'],
                ['slug' => 'care', 'name' => 'Уход'],
            ],
        ],
    ];

    /**
     * Seed the catalog categories.
     */
    public function run(): void
    {
        foreach (self::CATEGORIES as $rootPosition => $rootData) {
            $root = Category::query()->updateOrCreate(
                ['parent_id' => null, 'slug' => $rootData['slug']],
                [
                    'name' => $rootData['name'],
                    'icon' => $rootData['icon'],
                    'sort_order' => $rootPosition,
                    'is_active' => true,
                ],
            );

            foreach ($rootData['subs'] as $subPosition => $subData) {
                Category::query()->updateOrCreate(
                    ['parent_id' => $root->id, 'slug' => $subData['slug']],
                    [
                        'name' => $subData['name'],
                        'icon' => null,
                        'sort_order' => $subPosition,
                        'is_active' => true,
                    ],
                );
            }
        }
    }
}
