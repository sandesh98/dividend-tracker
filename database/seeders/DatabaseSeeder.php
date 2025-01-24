<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TransactionSeeder::class,
            ManualTransactionsSeeder::class,
            TradesSeeder::class,
        ]);

        Artisan::call('stock:update');
        Artisan::call('stock:price');
    }
}
