<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Demo accounts: one per role plus three customers for reviews and orders.
     *
     * @var array<int, array{name: string, email: string, phone: string, role: UserRole}>
     */
    private const USERS = [
        [
            'name' => 'Администратор ByClick',
            'email' => 'admin@byclick.by',
            'phone' => '+375 (29) 000-00-01',
            'role' => UserRole::Admin,
        ],
        [
            'name' => 'ByClick Store',
            'email' => 'seller@byclick.by',
            'phone' => '+375 (29) 000-00-02',
            'role' => UserRole::Seller,
        ],
        [
            'name' => 'Валентин',
            'email' => 'customer@byclick.by',
            'phone' => '+375 (29) 000-00-03',
            'role' => UserRole::Customer,
        ],
        [
            'name' => 'Анна Ковалёва',
            'email' => 'anna@example.com',
            'phone' => '+375 (29) 111-11-11',
            'role' => UserRole::Customer,
        ],
        [
            'name' => 'Дмитрий Орлов',
            'email' => 'dmitry@example.com',
            'phone' => '+375 (29) 222-22-22',
            'role' => UserRole::Customer,
        ],
        [
            'name' => 'Мария Лебедева',
            'email' => 'maria@example.com',
            'phone' => '+375 (29) 333-33-33',
            'role' => UserRole::Customer,
        ],
    ];

    /**
     * Seed the demo users.
     */
    public function run(): void
    {
        foreach (self::USERS as $data) {
            $user = User::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'role' => $data['role'],
                    'password' => 'password',
                    'email_verified_at' => now(),
                ],
            );

            $user->forceFill([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'role' => $data['role'],
            ])->save();
        }
    }
}
