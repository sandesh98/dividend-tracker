<?php

namespace Database\Seeders;

use App\Models\Dividend;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DividendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $EURtransactions = Transaction::where('mutation', 'LIKE', 'EUR')->where(function ($query) {
            $query->where('description', 'LIKE', 'Dividend')
                  ->orWhere('description', 'LIKE', 'Dividendbelasting');
        })->get();

        $USDtransactions = Transaction::where('mutation', 'LIKE', 'USD')
            ->where(function ($query) {
                $query->where('description', 'LIKE', 'dividend')
                    ->orWhere('description', 'LIKE', 'dividendbelasting');
            })
            ->orWhere(function ($query) {
                $query->whereNotNull('fx')
                    ->where(function ($query) {
                        $query->whereNull('order_id');
                    });
                
            })
            ->get();

            $currentFx = null;
            // $processedDividends = [];
            
            foreach ($USDtransactions as $transaction) {
                if (!empty($transaction['fx']) && $transaction['description'] === 'Valuta Debitering') {
                    $currentFx = $transaction['fx'];
                    continue;
                }
            
                // Controleer of het een "Dividend" of "Dividendbelasting" is
                if (in_array($transaction['description'], ['Dividend', 'Dividendbelasting'])) {
                    Dividend::create([
                        'date' => $transaction['date'],
                        'time' => $transaction['time'],
                        'description' => $transaction['description'],
                        'product' => $transaction['product'],
                        'isin' => $transaction['isin'],
                        'mutation' => 'USD',
                        'amount' => $transaction['mutation_value'],
                        'fx' => $currentFx,
                    ]);
                }
            }

        foreach($EURtransactions as $transaction) {
            Dividend::create([
                'date' => $transaction->date,
                'time' => $transaction->time,
                'description' => $transaction->description,
                'product' => $transaction->product,
                'isin' => $transaction->isin,
                'fx' => $this->setFX($transaction->fx),
                'mutation' => 'EUR',
                'amount' => $transaction->mutation_value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function setFX($fx)
    {
        return empty($fx) ? 1 : $fx;
    }
}
