<?php

namespace App\Services\Stokker;

use Illuminate\Support\Str;

final readonly class StokkerProductData
{
    public function __construct(
        public string $code,
        public string $title,
        public string $description,
        public string $link,
        public string $imageUrl,
        public string $brand,
        public string $condition,
        public string $availability,
        public ?string $quantity,
        public ?string $price,
        public ?string $currency,
    ) {}

    public function toArray(): array
    {
        return collect(get_object_vars($this))
            ->mapWithKeys(fn (mixed $value, string $key): array => [Str::snake($key) => $value])
            ->all();
    }
}
