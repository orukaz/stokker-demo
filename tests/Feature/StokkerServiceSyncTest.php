<?php

use App\Models\Product;
use App\Services\Stokker\StokkerApi;
use App\Services\Stokker\StokkerProductData;
use App\Services\Stokker\StokkerProductsParser;
use App\Services\Stokker\StokkerService;
use Illuminate\Support\Facades\Log;

use function Pest\Laravel\mock;

test('it syncs parsed stokker products to database', function () {
    Product::create([
        'code' => '15450255&ECHO',
        'title' => 'Old title',
        'price' => '100.00',
    ]);

    $api = mock(StokkerApi::class);
    $api->expects('productsXml')
        ->with('SET', 'FO01')
        ->andReturn('<rss></rss>');

    $parser = mock(StokkerProductsParser::class);
    $parser->expects('parse')
        ->with('<rss></rss>')
        ->andReturn(collect([
            stokkerProductData(),
            stokkerProductData([
                'code' => '12345678&MAKITA',
                'title' => 'Akutrell Makita',
                'price' => '129.90',
            ]),
        ]));

    (new StokkerService($api, $parser))->syncProducts('SET', 'FO01');

    $product = Product::query()->where('code', '15450255&ECHO')->sole();

    $newProduct = Product::query()->where('code', '12345678&MAKITA')->sole();

    expect(Product::query()->count())->toBe(2)
        ->and($product->title)->toBe('Mootorsaag CS-310ES/30RC, Echo')
        ->and($product->description)->toBe('Tehnilised andmed')
        ->and($product->link)->toBe('https://www.stokker.ee/et/mootorsaed/15450255&ECHO/mootorsaag-cs-310es-30rc')
        ->and($product->image_url)->toBe('https://media.stokker.com/prod/thumb/l/154/15450255%26ECHO.jpg')
        ->and($product->brand)->toBe('Echo')
        ->and($product->condition)->toBe('new')
        ->and($product->availability)->toBe('in stock')
        ->and($product->quantity)->toBe('1511.0000')
        ->and($product->price)->toBe('209.0000')
        ->and($product->currency)->toBe('EUR')
        ->and($product->synced_at)->not->toBeNull()
        ->and($newProduct->title)->toBe('Akutrell Makita')
        ->and($newProduct->price)->toBe('129.9000');
});

test('it skips invalid stokker products', function () {
    Log::spy();

    $api = mock(StokkerApi::class);
    $api->expects('productsXml')
        ->with('SET', 'FO01')
        ->andReturn('<rss></rss>');

    $parser = mock(StokkerProductsParser::class);
    $parser->expects('parse')
        ->with('<rss></rss>')
        ->andReturn(collect([
            stokkerProductData(['code' => '']),
            stokkerProductData(),
        ]));

    (new StokkerService($api, $parser))->syncProducts('SET', 'FO01');

    Log::shouldHaveReceived('warning')
        ->once()
        ->with('Skipped invalid Stokker product.', Mockery::on(
            fn (array $context): bool => $context['code'] === ''
                && isset($context['errors']['code'])
        ));

    expect(Product::query()->count())->toBe(1)
        ->and(Product::query()->where('code', '15450255&ECHO')->exists())->toBeTrue();
});

/**
 * @param  array<string, string|null>  $overrides
 */
function stokkerProductData(array $overrides = []): StokkerProductData
{
    $attributes = array_merge([
        'code' => '15450255&ECHO',
        'title' => 'Mootorsaag CS-310ES/30RC, Echo',
        'description' => 'Tehnilised andmed',
        'link' => 'https://www.stokker.ee/et/mootorsaed/15450255&ECHO/mootorsaag-cs-310es-30rc',
        'imageUrl' => 'https://media.stokker.com/prod/thumb/l/154/15450255%26ECHO.jpg',
        'brand' => 'Echo',
        'condition' => 'new',
        'availability' => 'in stock',
        'quantity' => '1511.0000',
        'price' => '209.00',
        'currency' => 'EUR',
    ], $overrides);

    return new StokkerProductData(
        code: $attributes['code'],
        title: $attributes['title'],
        description: $attributes['description'],
        link: $attributes['link'],
        imageUrl: $attributes['imageUrl'],
        brand: $attributes['brand'],
        condition: $attributes['condition'],
        availability: $attributes['availability'],
        quantity: $attributes['quantity'],
        price: $attributes['price'],
        currency: $attributes['currency'],
    );
}
