<?php

namespace Stokker\PosSignature;

use Illuminate\Support\ServiceProvider;

class PosSignatureServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'pos-signature');
    }
}
