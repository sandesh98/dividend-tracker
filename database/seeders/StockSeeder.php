<?php

namespace Database\Seeders;

use App\Models\Stock;
use App\Models\Transaction;
use App\Value\CurrencyType;
use Brick\Money\Money;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

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
                'type' => Arr::random(['S', 'ETF']), // replace with real data
                'ticker' => Arr::random([
                    'VHYL', 'INGA.AS', 'VWRL', 'PQEFF', 'VUSA', 'KHC', 'KO', 'AAPL', 'MA', 'BESI.AS', 'MTTR', 'CSPX', 'ABN.AS',
                    'BFIT.AS', 'EBUS.AS', 'BBBYQ', 'MULN', 'GME', 'DGTL', 'SHEL', 'BB', 'SNDL'
                ]), // replace with real data
                'currency' => Arr::random([CurrencyType::EUR->value, CurrencyType::USD->value]), // replace with real data
                'price' => Money::of(random_int(1, 100), 'EUR')->getMinorAmount()->toInt(),
            ]);
        }
    }
}
