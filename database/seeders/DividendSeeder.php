<?php

namespace Database\Seeders;

use App\Models\Dividend;
use App\Models\Stock;
use App\Models\Transaction;
use App\Value\CurrencyType;
use App\Value\DividendType;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

class DividendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     *
     * @throws UnknownCurrencyException
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
     * @param  int|null  $fx
     * @return int
     */
    private function setFX(?int $fx): int
    {
        return empty($fx) ? 1 : $fx;
    }

    /**
     * Process the EUR dividend transactions
     *
     * @param  Collection  $transactions
     * @return void
     *
     * @throws UnknownCurrencyException
     */
    private function processEURTransaction(Collection $transactions): void
    {
        $stocks = Stock::all()->keyBy('isin');
        $now = Date::now();

        foreach ($transactions as $transaction) {
            $stock = $stocks->get($transaction->isin);

            $dividend = new Dividend([
                'paid_out_at' => Carbon::parse("{$transaction->date} {$transaction->time}"),
                'description' => DividendType::from($transaction->description),
                'fx' => $this->setFX($transaction->fx),
                'dividend_amount' => Money::of(
                    $this->setAmount($transaction->mutation_value),
                    CurrencyType::EUR->value
                ),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $dividend->stock()->associate($stock);

            $dividend->save();
        }
    }

    /**
     * Process the USD dividend transactions
     *
     * @param  Collection  $transactions
     * @return void
     *
     * @throws UnknownCurrencyException
     */
    private function processUSDTransaction(Collection $transactions): void
    {
        $currentFx = null;

        foreach ($transactions as $transaction) {
            if (! empty($transaction['fx']) && $transaction['description'] === 'Valuta Debitering') {
                $currentFx = $transaction['fx'];

                continue;
            }

            $stocks = Stock::all()->keyBy('isin');

            if (in_array($transaction['description'], [DividendType::Dividend->value, DividendType::DividendTax->value], true)) {
                $stock = $stocks->get($transaction['isin']);

                $dividend = new Dividend([
                    'paid_out_at' => Carbon::parse("{$transaction['date']} {$transaction['time']}"),
                    'description' => DividendType::from($transaction['description']),
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
     * @param  mixed  $amount
     * @return float|int
     */
    private function setAmount(mixed $amount): float|int
    {
        return $amount < 0 ? abs($amount) : $amount;
    }
}
