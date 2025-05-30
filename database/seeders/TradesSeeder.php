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
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class TradesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stocks = Stock::all()->keyBy('isin');
        $transactions = Transaction::whereNotNull('order_id')->get();

        foreach ($transactions as $transaction) {
            $stock = $stocks->get($transaction->isin);

            if (empty($transaction->date)) {
                continue;
            }

            $trade = new Trade([
                'date' => $transaction->date,
                'time' => $transaction->time,
                'description' => $transaction->description,
                'currency' => $transaction->mutation,
                'total_transaction_value' => abs($transaction->mutation_value),
                'action' => $this->determineAction($transaction->description),
                'price_per_unit' => $this->determinePricePerUnit($transaction->description, $transaction->mutation),
                'quantity' => $this->determineQuantity($transaction->description),
                'order_id' => $transaction->order_id,
                'fx' => $transaction->fx,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $trade->stock()->associate($stock);

            $trade->save();
        }
    }

    /**
     * Determine the action of the transaction
     *
     * @param $description
     * @return TransactionType|null
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
     * Determine the quantity of the transaction
     *
     * @param $description
     * @return int
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
     * Determine the price per unit of the transaction
     *
     * @param $description
     * @param $currency
     * @return int
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
