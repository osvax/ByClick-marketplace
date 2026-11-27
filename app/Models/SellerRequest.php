<?php

namespace App\Models;

use App\Enums\SellerRequestStatus;
use Database\Factories\SellerRequestFactory;
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
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property int|null $category_id
 * @property string|null $comment
 * @property SellerRequestStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'name', 'phone', 'email', 'category_id', 'comment', 'status'])]
class SellerRequest extends Model
{
    /** @use HasFactory<SellerRequestFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SellerRequestStatus::class,
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @param Builder<SellerRequest> $query */
    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->where('status', SellerRequestStatus::New);
    }
}
