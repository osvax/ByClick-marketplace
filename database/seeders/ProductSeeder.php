<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Demo catalog extracted from the marketplace template.
     *
     * Money is stored in kopecks: 4990 = 49.90 BYN.
     *
     * @var array<int, array{
     *     slug: string,
     *     name: string,
     *     brand: string,
     *     cat: string,
     *     sub: string,
     *     image: string,
     *     price: int,
     *     old_price: int|null,
     *     rating: float,
     *     reviews_count: int,
     *     sold_count: int,
     *     stock: int,
     *     description: string
     * }>
     */
    private const PRODUCTS = [
        [
            'slug' => 'naushniki-aura-pro',
            'name' => 'Наушники «Аура Pro», беспроводные',
            'brand' => 'Aura',
            'cat' => 'electronics',
            'sub' => 'audio',
            'image' => 'p1.svg',
            'price' => 4990,
            'old_price' => 7490,
            'rating' => 4.8,
            'reviews_count' => 128,
            'sold_count' => 256,
            'stock' => 18,
            'description' => 'Беспроводные наушники с активным шумоподавлением, мягкими амбушюрами и кейсом для переноски. До 30 часов работы без подзарядки.',
        ],
        [
            'slug' => 'krossovki-ritm',
            'name' => 'Кроссовки «Ритм», беговые',
            'brand' => 'Ritm',
            'cat' => 'sport',
            'sub' => 'shoes',
            'image' => 'p2.svg',
            'price' => 3450,
            'old_price' => 5100,
            'rating' => 4.6,
            'reviews_count' => 64,
            'sold_count' => 128,
            'stock' => 12,
            'description' => 'Лёгкие беговые кроссовки с амортизирующей подошвой и дышащим верхом. Подходят для ежедневных тренировок по асфальту.',
        ],
        [
            'slug' => 'kruzhka-utro',
            'name' => 'Кружка керамическая «Утро», 350 мл',
            'brand' => 'Utro',
            'cat' => 'home',
            'sub' => 'tableware',
            'image' => 'p3.svg',
            'price' => 690,
            'old_price' => 990,
            'rating' => 4.9,
            'reviews_count' => 212,
            'sold_count' => 424,
            'stock' => 40,
            'description' => 'Керамическая кружка ручной работы объёмом 350 мл. Держит тепло, подходит для микроволновки и посудомоечной машины.',
        ],
        [
            'slug' => 'lampa-lyumen',
            'name' => 'Лампа настольная «Люмен» с диммером',
            'brand' => 'Lumen',
            'cat' => 'home',
            'sub' => 'lighting',
            'image' => 'p4.svg',
            'price' => 2190,
            'old_price' => 3200,
            'rating' => 4.7,
            'reviews_count' => 47,
            'sold_count' => 94,
            'stock' => 15,
            'description' => 'Настольная лампа с плавной регулировкой яркости и тёплым светом. Регулируется по высоте и наклону.',
        ],
        [
            'slug' => 'ryukzak-vektor',
            'name' => 'Рюкзак городской «Вектор», 22 л',
            'brand' => 'Vektor',
            'cat' => 'clothes',
            'sub' => 'bags',
            'image' => 'p5.svg',
            'price' => 4200,
            'old_price' => 6000,
            'rating' => 4.8,
            'reviews_count' => 93,
            'sold_count' => 186,
            'stock' => 22,
            'description' => 'Городской рюкзак на 22 литра с отделением для ноутбука 15,6″ и влагозащитной тканью.',
        ],
        [
            'slug' => 'kamera-snimok',
            'name' => 'Камера моментальная «Снимок»',
            'brand' => 'Snimok',
            'cat' => 'electronics',
            'sub' => 'cameras',
            'image' => 'p6.svg',
            'price' => 6990,
            'old_price' => 9400,
            'rating' => 4.5,
            'reviews_count' => 38,
            'sold_count' => 76,
            'stock' => 9,
            'description' => 'Моментальная камера с мгновенной печатью снимков. В комплекте ремешок и стартовая кассета.',
        ],
        [
            'slug' => 'monstera-dzhungli',
            'name' => 'Монстера в кашпо «Джунгли»',
            'brand' => 'Jungli',
            'cat' => 'home',
            'sub' => 'plants',
            'image' => 'p7.svg',
            'price' => 1590,
            'old_price' => 2100,
            'rating' => 4.9,
            'reviews_count' => 156,
            'sold_count' => 312,
            'stock' => 7,
            'description' => 'Монстера в керамическом кашпо. Неприхотливое растение для дома и офиса, высота около 60 см.',
        ],
        [
            'slug' => 'klaviatura-klik-87',
            'name' => 'Клавиатура механическая «Клик 87»',
            'brand' => 'Klik',
            'cat' => 'electronics',
            'sub' => 'keyboards',
            'image' => 'p8.svg',
            'price' => 5490,
            'old_price' => 7800,
            'rating' => 4.7,
            'reviews_count' => 71,
            'sold_count' => 142,
            'stock' => 14,
            'description' => 'Механическая клавиатура на 87 клавиш с тактильными переключателями и подсветкой. Съёмный кабель USB-C.',
        ],
        [
            'slug' => 'chasy-puls-s2',
            'name' => 'Часы смарт «Пульс S2»',
            'brand' => 'Puls',
            'cat' => 'electronics',
            'sub' => 'gadgets',
            'image' => 'p9.svg',
            'price' => 8990,
            'old_price' => 12500,
            'rating' => 4.6,
            'reviews_count' => 88,
            'sold_count' => 176,
            'stock' => 11,
            'description' => 'Смарт-часы с пульсометром, GPS и защитой от воды. До 10 дней автономной работы.',
        ],
        [
            'slug' => 'kolonka-ekho-mini',
            'name' => 'Колонка портативная «Эхо Mini»',
            'brand' => 'Echo',
            'cat' => 'electronics',
            'sub' => 'audio',
            'image' => 'p10.svg',
            'price' => 3290,
            'old_price' => 4700,
            'rating' => 4.8,
            'reviews_count' => 54,
            'sold_count' => 108,
            'stock' => 26,
            'description' => 'Портативная колонка с чистым звуком и защитой IPX7. До 12 часов работы, есть ремешок.',
        ],
        [
            'slug' => 'termos-dorozhny',
            'name' => 'Термос «Дорожный», 500 мл',
            'brand' => 'Dorozhny',
            'cat' => 'sport',
            'sub' => 'outdoor',
            'image' => 'p11.svg',
            'price' => 1290,
            'old_price' => 1890,
            'rating' => 4.9,
            'reviews_count' => 119,
            'sold_count' => 238,
            'stock' => 33,
            'description' => 'Термос из нержавеющей стали объёмом 500 мл. Держит тепло до 12 часов, не протекает.',
        ],
        [
            'slug' => 'svitshot-oversize',
            'name' => 'Свитшот «Оверсайз», хлопок',
            'brand' => 'Oversize',
            'cat' => 'clothes',
            'sub' => 'clothes',
            'image' => 'p12.svg',
            'price' => 2890,
            'old_price' => 4200,
            'rating' => 4.7,
            'reviews_count' => 42,
            'sold_count' => 84,
            'stock' => 19,
            'description' => 'Свитшот свободного кроя из плотного хлопка. Мягкий внутри, не теряет форму после стирки.',
        ],
        [
            'slug' => 'parfyum-utro',
            'name' => 'Парфюм «Утро», 50 мл',
            'brand' => 'Utro',
            'cat' => 'beauty',
            'sub' => 'fragrance',
            'image' => 'p13.svg',
            'price' => 2990,
            'old_price' => 4200,
            'rating' => 4.8,
            'reviews_count' => 76,
            'sold_count' => 152,
            'stock' => 21,
            'description' => 'Древесно-пряный парфюм объёмом 50 мл. Стойкость до 8 часов, универсален для дня и вечера.',
        ],
        [
            'slug' => 'konstruktor-kosmos',
            'name' => 'Конструктор «Космос», 320 деталей',
            'brand' => 'Kosmos',
            'cat' => 'kids',
            'sub' => 'toys',
            'image' => 'p14.svg',
            'price' => 1890,
            'old_price' => 2700,
            'rating' => 4.9,
            'reviews_count' => 203,
            'sold_count' => 406,
            'stock' => 28,
            'description' => 'Конструктор на 320 деталей для сборки космической станции. Совместим с популярными наборами.',
        ],
        [
            'slug' => 'derzhatel-dlya-telefona-v-avto',
            'name' => 'Держатель для телефона в авто',
            'brand' => 'ByClick',
            'cat' => 'auto',
            'sub' => 'accessories',
            'image' => 'p15.svg',
            'price' => 890,
            'old_price' => 1390,
            'rating' => 4.5,
            'reviews_count' => 31,
            'sold_count' => 62,
            'stock' => 45,
            'description' => 'Держатель для телефона на присоске с поворотом на 360°. Подходит для лобового стекла и торпедо.',
        ],
        [
            'slug' => 'lezhanka-oblako',
            'name' => 'Лежанка для кошки «Облако»',
            'brand' => 'Oblako',
            'cat' => 'pets',
            'sub' => 'beds',
            'image' => 'p16.svg',
            'price' => 2190,
            'old_price' => 3100,
            'rating' => 4.7,
            'reviews_count' => 97,
            'sold_count' => 194,
            'stock' => 17,
            'description' => 'Мягкая лежанка-облако для кошек и небольших собак. Съёмный чехол стирается в машинке.',
        ],
    ];

    /**
     * Seed the demo catalog products.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ShopSeeder::class,
        ]);

        $shop = Shop::query()->where('slug', 'byclick-store')->firstOrFail();

        foreach (self::PRODUCTS as $index => $data) {
            $category = Category::query()
                ->where('slug', $data['sub'])
                ->whereHas('parent', fn ($query) => $query->where('slug', $data['cat']))
                ->firstOrFail();

            $product = Product::query()->updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'shop_id' => $shop->id,
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'brand' => $data['brand'],
                    'description' => $data['description'],
                    'specs' => [
                        'Бренд' => $data['brand'],
                        'Артикул' => '104-'.str_pad((string) (230 + $index), 3, '0', STR_PAD_LEFT),
                        'Гарантия' => '12 месяцев',
                        'Страна производства' => 'Китай',
                    ],
                    'price' => $data['price'],
                    'old_price' => $data['old_price'],
                    'stock' => $data['stock'],
                    'rating' => $data['rating'],
                    'reviews_count' => $data['reviews_count'],
                    'sold_count' => $data['sold_count'],
                    'is_active' => true,
                ],
            );

            $product->images()->updateOrCreate(
                ['path' => 'images/products/'.$data['image']],
                [
                    'alt' => $data['name'],
                    'sort_order' => 0,
                    'is_main' => true,
                ],
            );
        }
    }
}
