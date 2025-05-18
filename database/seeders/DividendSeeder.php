<?php

namespace Database\Seeders;

use App\Models\Dividend;
use App\Models\Transaction;
use App\Value\CurrencyType;
use App\Value\TransactionType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Collection;

class DividendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $eurTransactions = $this->getEURTransactions();
        $usdTransactions = $this->getUSDTransactions();

        $this->processEURTransaction($eurTransactions);
        $this->processUSDTransaction($usdTransactions);
    }

    /**
     * Get all EUR dividend transactions
     *
     * @return Collection
     */
    private function getEURTransactions(): Collection
    {
        return Transaction::query()
            ->where('mutation', CurrencyType::EUR->value)
            ->where(function ($query) {
                $query->where('description', TransactionType::Dividend->value)
                    ->orWhere('description', TransactionType::DividendTax->value);
            })->get();
    }

    /**
     * Get all USD dividend transactions
     *
     * @return Collection
     */
    private function getUSDTransactions(): Collection
    {
        return Transaction::query()
            ->where('mutation', CurrencyType::USD->value)
            ->where(function ($query) {
                $query->where('description', TransactionType::Dividend->value)
                    ->orWhere('description', TransactionType::DividendTax->value);
            })
            ->orWhere(function ($query) {
                $query->whereNotNull('fx')
                    ->where(function ($query) {
                        $query->whereNull('order_id');
                    });
            })
            ->get();
    }

    /**
     * Determine the FX value for the transaction
     *
     * @param $fx
     * @return int
     */
    private function setFX($fx): int
    {
        return empty($fx) ? 1 : $fx;
    }

    /**
     * Process the EUR dividend transactions
     *
     * @param Collection $transactions
     * @return void
     */
    private function processEURTransaction(Collection $transactions): void
    {
        foreach ($transactions as $transaction) {
            Dividend::create([
                'date' => $transaction->date,
                'time' => $transaction->time,
                'description' => $transaction->description,
                'product' => $transaction->product,
                'isin' => $transaction->isin,
                'fx' => $this->setFX($transaction->fx),
                'mutation' => CurrencyType::EUR->value,
                'amount' => $this->setAmount($transaction->mutation_value),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Process the USD dividend transactions
     *
     * @param Collection $transactions
     * @return void
     */
    private function processUSDTransaction(Collection $transactions): void
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
                    'mutation' => CurrencyType::USD->value,
                    'amount' => $this->setAmount($transaction['mutation_value']),
                    'fx' => $currentFx,
                ]);
            }
        }
    }

    /**
     * Set the dividend amount to a positive value
     *
     * @param $amount
     * @return float|int
     */
    private function setAmount($amount): float|int
    {
        // In the case of dividend tax we want to set its value to a positive value/
        // This makes working with the data easier.
        if ($amount < 0) {
            return abs($amount);
        }

        return $amount;
    }
}
