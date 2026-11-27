<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Online = 'online';
    case Cash = 'cash';
    case Card = 'card';

    public function label(): string
    {
        return match ($this) {
            self::Online => 'Онлайн-оплата',
            self::Cash => 'Наличными при получении',
            self::Card => 'Картой при получении',
        };
    }
}
