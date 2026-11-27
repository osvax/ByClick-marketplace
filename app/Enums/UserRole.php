<?php

namespace App\Enums;

enum UserRole: string
{
    case Customer = 'customer';
    case Seller = 'seller';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Customer => 'Покупатель',
            self::Seller => 'Продавец',
            self::Admin => 'Администратор',
        };
    }
}
