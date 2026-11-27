<?php

namespace App\Enums;

enum OrderStatus: string
{
    case New = 'new';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Новый',
            self::Confirmed => 'Подтверждён',
            self::Processing => 'Собирается',
            self::Shipped => 'Передан в доставку',
            self::Delivered => 'Доставлен',
            self::Cancelled => 'Отменён',
        };
    }

    /**
     * CSS modifier for the status badge in order lists.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Delivered => 'va_status--done',
            self::Cancelled => 'va_status--cancel',
            default => 'va_status--way',
        };
    }
}
