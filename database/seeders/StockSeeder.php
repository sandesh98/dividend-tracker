<?php

namespace Database\Seeders;

use App\Models\Stock;
use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stocks = Transaction::whereNotNull('product')
                    ->where('product', 'NOT LIKE', 'FLATEX EURO BANKACCOUNT')
                    ->get();

        foreach ($stocks as $stock) {
            Stock::create([
                'date' => $stock->date,
                'time' => $stock->time,
                'value_date' => $stock->value_date,
                'product' => $stock->product,
                'isin' => $stock->isin,
                'description' => $stock->description,
                'fx' => $stock->fx,
                'mutation' => $stock->mutation,
                'mutation_value' => $stock->mutation_value,
                'balance' => $stock->balance,
                'balance_value' => $stock->balance_value,
                'order_id' => $stock->order_id
            ]);
        }
    }
}
