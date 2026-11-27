<?php

namespace App\Models;

use App\Enums\PromoType;
use Database\Factories\PromoCodeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property PromoType $type
 * @property int $value
 * @property int $min_total
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property int|null $usage_limit
 * @property int $used_count
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'code',
    'type',
    'value',
    'min_total',
    'starts_at',
    'ends_at',
    'usage_limit',
    'used_count',
    'is_active',
])]
class PromoCode extends Model
{
    /** @use HasFactory<PromoCodeFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => PromoType::class,
            'value' => 'integer',
            'min_total' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /** @return HasMany<Order, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** @param Builder<PromoCode> $query */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true)
            ->where(fn (Builder $query): Builder => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn (Builder $query): Builder => $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }
}
