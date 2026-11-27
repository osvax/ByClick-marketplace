<?php

namespace App\Models;

use Database\Factories\FavoriteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string|null $session_id
 * @property int $product_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Product $product
 */
#[Fillable(['user_id', 'session_id', 'product_id'])]
class Favorite extends Model
{
    /** @use HasFactory<FavoriteFactory> */
    use HasFactory;

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Limit favorites to the current owner: a user or a guest session.
     *
     * @param  Builder<Favorite>  $query
     */
    #[Scope]
    protected function forCurrentOwner(Builder $query): void
    {
        if (auth()->check()) {
            $query->where('user_id', auth()->id());

            return;
        }

        $query->where('session_id', session()->getId());
    }

    /**
     * Attributes that identify the current owner of a favorite.
     *
     * @return array<string, int|string>
     */
    protected static function ownerAttributes(): array
    {
        return auth()->check()
            ? ['user_id' => (int) auth()->id()]
            : ['session_id' => session()->getId()];
    }

    /**
     * Product ids favorited by the current visitor (used by product cards).
     *
     * @return array<int, int>
     */
    public static function idsForCurrent(): array
    {
        return static::query()
            ->forCurrentOwner()
            ->pluck('product_id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * Number of favorites of the current visitor.
     */
    public static function countForCurrent(): int
    {
        return static::query()->forCurrentOwner()->count();
    }

    /**
     * Whether the current visitor has the product in favorites.
     */
    public static function isFavorited(int $productId): bool
    {
        return static::query()
            ->forCurrentOwner()
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Toggle a product in the current visitor's favorites.
     *
     * @return bool New state: true when the product was added.
     */
    public static function toggle(Product $product): bool
    {
        $favorite = static::query()
            ->forCurrentOwner()
            ->where('product_id', $product->id)
            ->first();

        if ($favorite !== null) {
            $favorite->delete();

            return false;
        }

        static::query()->create([
            ...static::ownerAttributes(),
            'product_id' => $product->id,
        ]);

        return true;
    }

    /**
     * Remove all favorites of the current visitor.
     */
    public static function clearForCurrent(): void
    {
        static::query()->forCurrentOwner()->delete();
    }
}
