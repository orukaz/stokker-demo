<?php

use App\Services\Stokker\StokkerApi;
use App\Services\Stokker\StokkerProductsParser;
use App\Services\Stokker\StokkerService;

use function Pest\Laravel\mock;

test('it returns parsed products', function () {
    $xml = '<rss></rss>';
    $products = collect(['product']);

    $api = mock(StokkerApi::class);
    $api->expects('productsXml')
        ->with('SET', 'FO01')
        ->andReturn($xml);

    $parser = mock(StokkerProductsParser::class);
    $parser->expects('parse')
        ->with($xml)
        ->andReturn($products);

    expect((new StokkerService($api, $parser))->products('SET', 'FO01'))->toBe($products);
});
