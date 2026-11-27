<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Question;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    /**
     * Demo reviews and questions for the first catalog product.
     *
     * @var array<int, array{email: string, rating: int, title: string, text: string, pros: string|null, cons: string|null, helpful_count: int}>
     */
    private const REVIEWS = [
        [
            'email' => 'anna@example.com',
            'rating' => 5,
            'title' => 'Отличный звук за свои деньги',
            'text' => 'Пользуюсь месяц: шумоподавление работает, в метро почти ничего не слышно. Кейс компактный, в карман куртки помещается.',
            'pros' => 'Шумоподавление, кейс, время работы',
            'cons' => null,
            'helpful_count' => 24,
        ],
        [
            'email' => 'dmitry@example.com',
            'rating' => 4,
            'title' => 'Хорошо, но амбушюры плотные',
            'text' => 'Звук детальный, бас не гудит. Через два часа начинают давить амбушюры, приходится делать паузу.',
            'pros' => 'Звук, подключение к двум устройствам',
            'cons' => 'Плотные амбушюры',
            'helpful_count' => 11,
        ],
        [
            'email' => 'maria@example.com',
            'rating' => 5,
            'title' => 'Беру второй раз',
            'text' => 'Первые подарила, теперь взяла себе. К телевизору подключаются без проблем, заряд держат как заявлено.',
            'pros' => 'Удобно сидят, стабильный Bluetooth',
            'cons' => null,
            'helpful_count' => 8,
        ],
    ];

    /**
     * Seed demo reviews and questions.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProductSeeder::class,
        ]);

        $product = Product::query()->where('slug', 'naushniki-aura-pro')->first();

        if ($product === null) {
            return;
        }

        foreach (self::REVIEWS as $data) {
            $user = User::query()->where('email', $data['email'])->first();

            if ($user === null) {
                continue;
            }

            Review::query()->updateOrCreate(
                ['product_id' => $product->id, 'user_id' => $user->id],
                [
                    'rating' => $data['rating'],
                    'title' => $data['title'],
                    'text' => $data['text'],
                    'pros' => $data['pros'],
                    'cons' => $data['cons'],
                    'helpful_count' => $data['helpful_count'],
                    'is_published' => true,
                ],
            );
        }

        $anna = User::query()->where('email', 'anna@example.com')->first();
        $dmitry = User::query()->where('email', 'dmitry@example.com')->first();

        $questions = [
            [
                'user_id' => $anna?->id,
                'text' => 'Подключаются ли наушники к телевизору?',
                'answer' => 'Да, если у телевизора есть Bluetooth. Наушники подключаются как обычное устройство и запоминают его.',
            ],
            [
                'user_id' => $dmitry?->id,
                'text' => 'Идёт ли жёсткий кейс в комплекте?',
                'answer' => 'В комплекте жёсткий кейс для переноски. Внутри — мягкий вкладыш, его можно аккуратно протереть влажной салфеткой.',
            ],
        ];

        foreach ($questions as $data) {
            Question::query()->updateOrCreate(
                ['product_id' => $product->id, 'text' => $data['text']],
                [
                    'user_id' => $data['user_id'],
                    'answer' => $data['answer'],
                    'answered_at' => now(),
                    'is_published' => true,
                ],
            );
        }
    }
}
