<?php

test('stokker brand assets are available', function () {
    $assets = [
        resource_path('images/stokker-logo.svg'),
        resource_path('images/favicon.png'),
        public_path('favicon.ico'),
        public_path('favicon.svg'),
        public_path('apple-touch-icon.png'),
    ];

    foreach ($assets as $asset) {
        $this->assertFileExists($asset);
    }

    expect(file_get_contents(resource_path('images/stokker-logo.svg')))
        ->toContain('<svg')
        ->toContain('viewBox');

    expect(file_get_contents(public_path('favicon.svg')))
        ->toContain('<svg')
        ->toContain('data:image/png;base64,');

    $expectedImageSizes = [
        resource_path('images/favicon.png') => [180, 180],
        public_path('apple-touch-icon.png') => [180, 180],
    ];

    foreach ($expectedImageSizes as $asset => $dimensions) {
        $imageSize = getimagesize($asset);

        $this->assertIsArray($imageSize);

        expect([$imageSize[0], $imageSize[1]])->toBe($dimensions);
    }

    $faviconHeader = unpack('vreserved/vtype/vcount', file_get_contents(public_path('favicon.ico'), false, null, 0, 6));

    expect($faviconHeader)->toMatchArray([
        'reserved' => 0,
        'type' => 1,
        'count' => 3,
    ]);
});
