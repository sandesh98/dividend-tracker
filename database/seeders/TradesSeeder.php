<?php

namespace Database\Seeders;

use App\Models\Stock;
use App\Models\Trade;
use App\Models\Transaction;
use App\Value\TransactionType;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TradesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stocks = Stock::all()->keyBy('isin');

        $transactions = Transaction::query()
            ->whereNotNull('order_id')
            ->get();

        foreach ($transactions as $transaction) {
            $stock = $stocks->get($transaction->isin);

            if ($transaction->date === null) {
                continue;
            }

            $trade = new Trade;
            $trade->date = $transaction->date;
            $trade->time = $transaction->time;
            $trade->description = $transaction->description;
            $trade->currency = $transaction->mutation;
            $trade->total_transaction_value = abs($transaction->mutation_value);
            $trade->action = $this->determineAction($transaction->description);
            $trade->price_per_unit = $this->determinePricePerUnit($transaction->description, $transaction->mutation);
            $trade->quantity = $this->determineQuantity($transaction->description);
            $trade->order_id = $transaction->order_id;
            $trade->fx = $transaction->fx;

            $trade->stock()->associate($stock);
            $trade->save();
        }
    }

    /**
     * Determine the action of the transaction.
     */
    private function determineAction($description): ?TransactionType
    {
        // Input: Koop 13 @ 36,234 USD
        // Output: "Koop"
        if (Str::startsWith($description, 'Koop')) {
            return TransactionType::Buy;
        }

        if (Str::startsWith($description, 'Verkoop')) {
            return TransactionType::Sell;
        }

        return null;
    }

    /**
     * Determine the quantity of the transaction.
     *
     * return int
     */
    private function determineQuantity($description): int
    {
        // Input: Koop 13 @ 36,234 USD
        // Output: 13
        $value = Str::match('/\b(?:Koop|Verkoop) (\d+)/i', $description);

        if (empty($value)) {
            return 1;
        }

        return $value;
    }

    /**
     * Determine the price per unit of the transaction.
     *
     * @throws MathException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    private function determinePricePerUnit($description, $currency): int
    {
        // Input: Koop 13 @ 36,234 USD
        // Output: "36,234"
        $value = Str::match('/@ ([\d,]+)/', $description);

        if (empty($value)) {
            return 0;
        }

        $normalizedValue = str_replace(',', '.', $value);

        $money = Money::of($normalizedValue, $currency, roundingMode: RoundingMode::HALF_UP);

        return $money->getMinorAmount()->toInt();
    }
}
