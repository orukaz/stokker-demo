<?php

namespace App\Services\Stokker;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StokkerService
{
    public function __construct(
        private StokkerApi $api,
        private StokkerProductsParser $parser,
    ) {}

    public function products(string $area, string $group): Collection
    {
        return $this->parser->parse(
            $this->api->productsXml($area, $group),
        );
    }

    public function syncProducts(string $area, string $group): void
    {
        $syncedAt = now();

        $this->products($area, $group)
            ->map(fn (StokkerProductData $product): ?array => $this->validateProduct($product))
            ->filter()
            ->map(fn (array $product): array => [
                ...$product,
                'synced_at' => $syncedAt,
            ])
            ->chunk(500)
            ->each(function (Collection $products): void {
                Product::upsert(
                    $products->values()->all(),
                    uniqueBy: ['code'],
                    update: [
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
                    ],
                );
            });
    }

    private function validateProduct(StokkerProductData $product): ?array
    {
        $validator = Validator::make($product->toArray(), [
            'code' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'link' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
            'brand' => ['nullable', 'string', 'max:255'],
            'condition' => ['nullable', 'string', 'max:255'],
            'availability' => ['nullable', 'string', 'max:255'],
            'quantity' => ['nullable', 'numeric'],
            'price' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        if ($validator->fails()) {
            Log::warning('Skipped invalid Stokker product.', [
                'code' => $product->code,
                'errors' => $validator->errors()->toArray(),
            ]);

            return null;
        }

        return $validator->validated();
    }
}
