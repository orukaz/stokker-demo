<?php

use App\Services\Stokker\StokkerProductData;
use App\Services\Stokker\StokkerProductsParser;

test('it parses products xml', function () {
    $xml = <<<'XML'
<?xml version="1.0"?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
    <channel>
        <item>
            <g:id><![CDATA[15450255&ECHO]]></g:id>
            <g:title><![CDATA[Mootorsaag CS-310ES/30RC, Echo]]></g:title>
            <g:description><![CDATA[Tehnilised andmed]]></g:description>
            <g:link><![CDATA[https://www.stokker.ee/et/mootorsaed/15450255&ECHO/mootorsaag-cs-310es-30rc]]></g:link>
            <g:image_link><![CDATA[https://media.stokker.com/prod/thumb/l/154/15450255%26ECHO.jpg]]></g:image_link>
            <g:brand><![CDATA[Echo]]></g:brand>
            <g:condition>new</g:condition>
            <g:availability>in stock</g:availability>
            <g:quantity_to_sell_on_facebook>1511.0000</g:quantity_to_sell_on_facebook>
            <g:price>209.00 eur</g:price>
        </item>
    </channel>
</rss>
XML;

    $products = (new StokkerProductsParser)->parse($xml);

    expect($products)->toHaveCount(1)
        ->and($products->first())->toEqual(new StokkerProductData(
            code: '15450255&ECHO',
            title: 'Mootorsaag CS-310ES/30RC, Echo',
            description: 'Tehnilised andmed',
            link: 'https://www.stokker.ee/et/mootorsaed/15450255&ECHO/mootorsaag-cs-310es-30rc',
            imageUrl: 'https://media.stokker.com/prod/thumb/l/154/15450255%26ECHO.jpg',
            brand: 'Echo',
            condition: 'new',
            availability: 'in stock',
            quantity: '1511.0000',
            price: '209.00',
            currency: 'EUR',
        ));
});

test('it rejects invalid xml', function () {
    expect(fn () => (new StokkerProductsParser)->parse('<rss>'))->toThrow(InvalidArgumentException::class);
});
