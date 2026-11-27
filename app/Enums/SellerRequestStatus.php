<?php

namespace App\Enums;

enum SellerRequestStatus: string
{
    case New = 'new';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Новая',
            self::Approved => 'Одобрена',
            self::Rejected => 'Отклонена',
        };
    }
}
