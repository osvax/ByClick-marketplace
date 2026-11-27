<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $shop_id
 * @property int $category_id
 * @property string $slug
 * @property string $name
 * @property string|null $brand
 * @property string|null $search_text
 * @property string|null $description
 * @property array<string, string>|null $specs
 * @property int $price
 * @property int|null $old_price
 * @property int $stock
 * @property float $rating
 * @property int $reviews_count
 * @property int $sold_count
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'shop_id',
    'category_id',
    'slug',
    'name',
    'brand',
    'description',
    'specs',
    'price',
    'old_price',
    'stock',
    'rating',
    'reviews_count',
    'sold_count',
    'is_active',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'specs' => 'array',
            'price' => 'integer',
            'old_price' => 'integer',
            'stock' => 'integer',
            'rating' => 'float',
            'reviews_count' => 'integer',
            'sold_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Keep the normalized search string in sync with the product name and brand.
     */
    protected static function booted(): void
    {
        static::saving(function (Product $product): void {
            $product->search_text = mb_strtolower(trim($product->name.' '.($product->brand ?? '')));
        });
    }

    /** @return BelongsTo<Shop, $this> */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return HasMany<ProductImage, $this> */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /** @return HasOne<ProductImage, $this> */
    public function mainImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }

    /** @return HasMany<Review, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** @return HasMany<Question, $this> */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /** @return HasMany<Favorite, $this> */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function hasDiscount(): bool
    {
        return $this->old_price !== null && $this->old_price > $this->price;
    }

    public function discountPercent(): int
    {
        if (! $this->hasDiscount()) {
            return 0;
        }

        return (int) round((1 - $this->price / $this->old_price) * 100);
    }

    /** @param Builder<Product> $query */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** @param Builder<Product> $query */
    #[Scope]
    protected function inCategory(Builder $query, Category $category): void
    {
        $categoryIds = $category->children()->pluck('id')->push($category->id);

        $query->whereIn('category_id', $categoryIds);
    }

    /** @param Builder<Product> $query */
    #[Scope]
    protected function priceBetween(Builder $query, ?int $min, ?int $max): void
    {
        $query->when($min !== null, fn (Builder $query): Builder => $query->where('price', '>=', $min));
        $query->when($max !== null, fn (Builder $query): Builder => $query->where('price', '<=', $max));
    }
}
