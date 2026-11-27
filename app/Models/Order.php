<?php

namespace App\Models;

use App\Enums\DeliveryMethod;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $number
 * @property OrderStatus $status
 * @property string $customer_name
 * @property string $customer_phone
 * @property string $customer_email
 * @property DeliveryMethod $delivery_method
 * @property int|null $delivery_point_id
 * @property string|null $address
 * @property string|null $delivery_city
 * @property PaymentMethod $payment_method
 * @property string|null $comment
 * @property int $subtotal
 * @property int $discount
 * @property int $delivery_price
 * @property int $total
 * @property int|null $promo_code_id
 * @property Carbon|null $placed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'user_id',
    'number',
    'status',
    'customer_name',
    'customer_phone',
    'customer_email',
    'delivery_method',
    'delivery_point_id',
    'address',
    'delivery_city',
    'payment_method',
    'comment',
    'subtotal',
    'discount',
    'delivery_price',
    'total',
    'promo_code_id',
    'placed_at',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'delivery_method' => DeliveryMethod::class,
            'payment_method' => PaymentMethod::class,
            'subtotal' => 'integer',
            'discount' => 'integer',
            'delivery_price' => 'integer',
            'total' => 'integer',
            'placed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            if (blank($order->number)) {
                $order->number = static::generateNumber();
            }
        });
    }

    /**
     * Orders are addressed by their public number in URLs.
     */
    public function getRouteKeyName(): string
    {
        return 'number';
    }

    /**
     * Generate a unique public order number.
     */
    public static function generateNumber(): string
    {
        do {
            $number = 'BC-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (static::query()->where('number', $number)->exists());

        return $number;
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<OrderItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** @return BelongsTo<PromoCode, $this> */
    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    /** @return BelongsTo<DeliveryPoint, $this> */
    public function deliveryPoint(): BelongsTo
    {
        return $this->belongsTo(DeliveryPoint::class);
    }

    /** @param Builder<Order> $query */
    #[Scope]
    protected function withStatus(Builder $query, OrderStatus $status): void
    {
        $query->where('status', $status);
    }
}
