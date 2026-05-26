<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('stokker:sync-products');
    }
}
