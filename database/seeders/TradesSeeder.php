<?php

namespace Database\Seeders;

use App\Models\Stock;
use App\Models\Trade;
use App\Models\Transaction;
use App\Value\TransactionType;
use Brick\Math\RoundingMode;
use Brick\Money\Context;
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

    private function determineAction($description)
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

    private function determineQuantity($description)
    {
        // Input: Koop 13 @ 36,234 USD
        // Output: 13
        $value = Str::match('/\b(?:Koop|Verkoop) (\d+)/i', $description);

        if (empty($value)) {
            return 1;
        }

        return $value;
    }

    private function determinePricePerUnit($description, $currency)
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
