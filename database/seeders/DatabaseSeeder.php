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
            DividendSeeder::class
        ]);


        Artisan::call('stock:update');
        $this->command->info('Done updating stock information');
        Artisan::call('stock:price');
        $this->command->info('Done updating stocks price');
    }
}
