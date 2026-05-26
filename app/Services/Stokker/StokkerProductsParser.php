<?php

namespace App\Services\Stokker;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use SimpleXMLElement;

class StokkerProductsParser
{
    public function parse(string $xml): Collection
    {
        $previous = libxml_use_internal_errors(true);

        try {
            $rss = simplexml_load_string($xml, SimpleXMLElement::class, LIBXML_NONET);

            if (! $rss instanceof SimpleXMLElement) {
                throw new InvalidArgumentException('Invalid Stokker products XML.');
            }

            $products = [];

            foreach ($rss->channel->item as $item) {
                $products[] = $this->product($item);
            }

            return collect($products);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    private function product(SimpleXMLElement $item): StokkerProductData
    {
        $product = $item->children('g', true);
        [$price, $currency] = $this->price((string) $product->price);

        return new StokkerProductData(
            code: trim((string) $product->id),
            title: trim((string) $product->title),
            description: trim((string) $product->description),
            link: trim((string) $product->link),
            imageUrl: trim((string) $product->image_link),
            brand: trim((string) $product->brand),
            condition: trim((string) $product->condition),
            availability: trim((string) $product->availability),
            quantity: $this->number((string) $product->quantity_to_sell_on_facebook),
            price: $price,
            currency: $currency,
        );
    }

    private function price(string $value): array
    {
        $parts = explode(' ', (string) Str::of($value)->squish(), 2);

        return [
            $this->number($parts[0] ?? null),
            $this->currency($parts[1] ?? null),
        ];
    }

    private function currency(?string $value): ?string
    {
        $value = (string) Str::of($value ?? '')
            ->trim()
            ->upper();

        return $value === '' ? null : $value;
    }

    private function number(?string $value): ?string
    {
        $value = (string) Str::of($value ?? '')
            ->trim()
            ->replace(',', '.');

        return is_numeric($value) ? $value : null;
    }
}
