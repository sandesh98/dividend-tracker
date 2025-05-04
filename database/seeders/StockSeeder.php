<?php

namespace Database\Seeders;

use App\Models\Stock;
use App\Models\Transaction;
use App\Value\CurrencyType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stocks = Transaction::query()
            ->whereNotNull(['product', 'isin'])
            ->select('product', 'isin')
            ->distinct()
            ->get();

        foreach ($stocks as $stock) {
            Stock::firstOrCreate([
                'name' => $stock->product,
                'isin' => $stock->isin,
                'display_name' => $stock->product,
                'type' => 'S', // replace with real data
                'ticker' => 'KO', // replace with real data
                'currency' => CurrencyType::EUR, // replace with real data
                'price' => 1000
            ]);
        }
    }
}
