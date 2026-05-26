<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'code',
    'title',
    'description',
    'link',
    'image_url',
    'brand',
    'condition',
    'availability',
    'quantity',
    'price',
    'currency',
    'synced_at',
])]
class Product extends Model
{
    public function favorites(): HasMany
    {
        return $this->hasMany(ProductFavorite::class);
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'product_favorites')
            ->wherePivotNotNull('user_id')
            ->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'price' => 'decimal:4',
            'synced_at' => 'datetime',
        ];
    }
}
