<?php

namespace App\Services\Stokker;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class StokkerApi
{
    private string $baseUrl;

    private PendingRequest $client;

    public function __construct(
        ?PendingRequest $client = null,
        ?string $baseUrl = null,
    ) {
        $config = config('services.stokker');

        $this->baseUrl = rtrim($baseUrl ?? $config['base_url'] ?? 'https://api.stokker.com', '/');
        $this->client = $client ?? $this->client($config);
    }

    public function productsXml(string $area, string $group): string
    {
        return $this->client
            ->get('feed/products', [
                'DataAreaID' => $area,
                'ig' => $group,
            ])
            ->throw()
            ->body();
    }

    private function client(array $config): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->accept('text/xml')
            ->timeout($config['timeout'] ?? 30)
            ->connectTimeout($config['connect_timeout'] ?? 10);
    }
}
