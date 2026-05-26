<?php

use App\Services\Stokker\StokkerApi;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();

    config(['services.stokker.base_url' => 'https://api.stokker.test']);
});

test('it requests products xml with given parameters', function () {
    $xml = '<rss></rss>';

    Http::fake([
        'api.stokker.test/feed/products*' => Http::response($xml),
    ]);

    expect((new StokkerApi)->productsXml('LV', 'AB01'))->toBe($xml);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://api.stokker.test/feed/products?DataAreaID=LV&ig=AB01'
        && $request->hasHeader('Accept', 'text/xml'));
});

test('it throws when the products feed request fails', function () {
    Http::fake([
        'api.stokker.test/feed/products*' => Http::response('Server error', 500),
    ]);

    expect(fn () => (new StokkerApi)->productsXml('SET', 'FO01'))->toThrow(RequestException::class);
});
