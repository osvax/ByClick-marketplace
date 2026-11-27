<?php

namespace App\Enums;

enum PromoType: string
{
    case Percent = 'percent';
    case Fixed = 'fixed';

    public function label(): string
    {
        return match ($this) {
            self::Percent => 'Процент от суммы',
            self::Fixed => 'Фиксированная сумма',
        };
    }
}
