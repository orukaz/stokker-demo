<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'visitor_id', 'product_id'])]
class ProductFavorite extends Model
{
    /**
     * @param  array{user_id?: int, visitor_id?: string}  $owner
     */
    public static function countForOwner(array $owner): int
    {
        if ($owner === []) {
            return 0;
        }

        return self::query()
            ->where($owner)
            ->count();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
