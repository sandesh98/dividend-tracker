<?php

namespace Database\Seeders;

use App\Models\Dividend;
use App\Models\Stock;
use App\Models\Transaction;
use App\Value\CurrencyType;
use App\Value\DividendType;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
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
                $query->where('description', DividendType::Dividend->value)
                    ->orWhere('description', DividendType::DividendTax->value);
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
                $query->where('description', DividendType::Dividend->value);
                $query->orWhere('description', DividendType::DividendTax->value);
            })
            ->orWhere(function ($query) {
                $query->whereNotNull('fx');
                $query->where(function ($query) {
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
        $stocks = Stock::all()->keyBy('isin');

        foreach ($transactions as $transaction) {
            $stock = $stocks->get($transaction->isin);

            $dividend = new Dividend([
                'date' => Carbon::parse($transaction->date),
                'time' => $transaction->time,
                'description' => $transaction->description,
                'fx' => $this->setFX($transaction->fx),
                'dividend_amount' => Money::of(
                    $this->setAmount($transaction->mutation_value),
                    CurrencyType::EUR->value
                ),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $dividend->stock()->associate($stock);

            $dividend->save();
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

            $stocks = Stock::all()->keyBy('isin');

            if (in_array($transaction['description'], ['Dividend', 'Dividendbelasting'])) {
                $stock = $stocks->get($transaction['isin']);

                $dividend = new Dividend([
                    'date' => Carbon::parse($transaction['date']),
                    'time' => $transaction['time'],
                    'description' => $transaction['description'],
                    'dividend_amount' => Money::of(
                        $this->setAmount($transaction->mutation_value),
                        CurrencyType::USD->value
                    ),
                    'fx' => $currentFx,
                ]);

                $dividend->stock()->associate($stock);

                $dividend->save();
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
        // In the case of dividend tax we want to set its value to a positive value.
        // This makes working with the data easier.
        if ($amount < 0) {
            return abs($amount);
        }

        return $amount;
    }
}
