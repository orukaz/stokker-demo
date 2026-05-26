<?php

namespace App\Console\Commands;

use App\Services\Stokker\StokkerService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('stokker:sync-products')]
#[Description('Sync Stokker products')]
final class SyncStokkerProductsCommand extends Command
{
    private const string AREA = 'SET';

    private const string GROUP = 'FO01';

    public function handle(StokkerService $stokker): int
    {
        $stokker->syncProducts(self::AREA, self::GROUP);

        $this->info('Stokker products synced.');

        return self::SUCCESS;
    }
}
