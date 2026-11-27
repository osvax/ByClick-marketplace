<?php

namespace App\Enums;

enum DeliveryMethod: string
{
    case Courier = 'courier';
    case Pickup = 'pickup';
    case Postamat = 'postamat';

    public function label(): string
    {
        return match ($this) {
            self::Courier => 'Курьером',
            self::Pickup => 'Пункт выдачи',
            self::Postamat => 'Постамат',
        };
    }
}
