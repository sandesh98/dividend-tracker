<?php

namespace Database\Seeders;

use App\Models\Dividend;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Collection;

class DividendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $EURtransactions = $this->getEURTransactions();
        $USDtransactions = $this->getUSDTransactions();

        $this->processEURTransaction($EURtransactions);
        $this->processUSDTransaction($USDtransactions);
    }

    private function getEURTransactions(): Collection
    {
        return Transaction::where('mutation', 'LIKE', 'EUR')
            ->where(function ($query) {
                $query->where('description', 'LIKE', 'Dividend')
                    ->orWhere('description', 'LIKE', 'Dividendbelasting');
            })->get();
    }

    private function getUSDTransactions(): Collection
    {
        return Transaction::where('mutation', 'LIKE', 'USD')
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
    }

    private function setFX($fx): int
    {
        return empty($fx) ? 1 : $fx;
    }

    private function processEURTransaction(Collection $transactions)
    {
        foreach ($transactions as $transaction) {
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
    private function processUSDTransaction(Collection $transactions)
    {
        $currentFx = null;

        foreach ($transactions as $transaction) {
            if (!empty($transaction['fx']) && $transaction['description'] === 'Valuta Debitering') {
                $currentFx = $transaction['fx'];
                continue;
            }

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
    }
}
