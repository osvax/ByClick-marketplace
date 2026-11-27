<?php

namespace App\Models;

use App\Enums\DeliveryMethod;
use Database\Factories\CartFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string|null $session_id
 * @property int|null $promo_code_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PromoCode|null $promoCode
 */
#[Fillable(['user_id', 'session_id', 'promo_code_id'])]
class Cart extends Model
{
    /** @use HasFactory<CartFactory> */
    use HasFactory;

    /**
     * Get the cart for the current visitor without creating a database row.
     */
    public static function currentCart(): ?self
    {
        return static::query()
            ->when(
                auth()->check(),
                fn ($query) => $query->where('user_id', auth()->id()),
                fn ($query) => $query->where('session_id', session()->getId()),
            )
            ->first();
    }

    /**
     * Get or create the cart of the current visitor.
     *
     * Guests are identified by the Laravel session id; authenticated users
     * always have exactly one cart (unique user_id).
     */
    public static function current(): self
    {
        if (auth()->check()) {
            return static::query()->firstOrCreate(['user_id' => auth()->id()]);
        }

        return static::query()->firstOrCreate(['session_id' => session()->getId()]);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<PromoCode, $this> */
    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    /** @return HasMany<CartItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Add a product to the cart, respecting the available stock.
     * Returns null when the product cannot be added.
     */
    public function add(Product $product, int $quantity = 1): ?CartItem
    {
        $limit = $product->stock > 0 ? min(99, $product->stock) : 0;

        if ($limit < 1) {
            return null;
        }

        $item = $this->items()->firstOrCreate(
            ['product_id' => $product->id],
            ['quantity' => 0],
        );

        $item->quantity = min($limit, $item->quantity + max(1, $quantity));
        $item->save();

        return $item;
    }

    /**
     * Set the quantity of a cart item and clamp it to the product stock.
     */
    public function setQuantity(CartItem $item, int $quantity): void
    {
        $stock = $item->product->stock;
        $quantity = max(1, min(99, $quantity));

        if ($stock > 0) {
            $quantity = min($quantity, $stock);
        }

        $item->update(['quantity' => $quantity]);
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    /**
     * Remove all products from the cart and drop the applied promo code.
     */
    public function clearItems(): void
    {
        $this->items()->delete();
        $this->applyPromoCode(null);
    }

    /**
     * Attach (or clear) a promo code for this cart.
     */
    public function applyPromoCode(?PromoCode $promoCode): void
    {
        $this->promoCode()->associate($promoCode);
        $this->save();
    }

    /**
     * Get the cart total in kopecks. Eager load the items to avoid N+1 queries.
     */
    public function subtotal(): int
    {
        return (int) $this->items->sum(fn (CartItem $item): int => $item->lineTotal());
    }

    /**
     * Total before discounts, using old prices when available.
     */
    public function oldSubtotal(): int
    {
        return (int) $this->items->sum(
            fn (CartItem $item): int => ($item->product->old_price ?? $item->product->price) * $item->quantity,
        );
    }

    /**
     * Promo code discount in kopecks for the current cart contents.
     */
    public function discount(): int
    {
        return $this->promoCode?->discountFor($this->subtotal()) ?? 0;
    }

    /**
     * Delivery price for a specific delivery method.
     * Pickup points and postamats are always free.
     */
    public function deliveryPriceFor(?DeliveryMethod $method = null): int
    {
        if ($this->subtotal() === 0) {
            return 0;
        }

        if ($method !== null && $method !== DeliveryMethod::Courier) {
            return 0;
        }

        $freeFrom = (int) config('marketplace.delivery.free_from', 10000);

        return $this->subtotal() - $this->discount() >= $freeFrom
            ? 0
            : (int) config('marketplace.delivery.courier_price', 500);
    }

    /**
     * Delivery price using the default courier method.
     */
    public function deliveryPrice(): int
    {
        return $this->deliveryPriceFor(DeliveryMethod::Courier);
    }

    /**
     * Final cart total in kopecks: subtotal − discount + delivery.
     */
    public function total(): int
    {
        return max(0, $this->subtotal() - $this->discount() + $this->deliveryPrice());
    }

    /**
     * How much the customer saves compared to old prices.
     */
    public function savings(): int
    {
        return max(0, $this->oldSubtotal() - $this->total());
    }

    /**
     * Get the number of products in the cart. Eager load the items to avoid N+1 queries.
     */
    public function totalQuantity(): int
    {
        return (int) $this->items->sum('quantity');
    }
}
