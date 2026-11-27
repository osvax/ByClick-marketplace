<?php

namespace App\Enums;

enum ShopStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'На модерации',
            self::Active => 'Активен',
            self::Suspended => 'Заблокирован',
        };
    }
}
