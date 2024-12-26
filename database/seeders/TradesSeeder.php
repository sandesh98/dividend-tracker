<?php

namespace Database\Seeders;

use App\Models\Trade;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TradesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = Transaction::whereNotNull('order_id')->get();

        foreach($transactions as $transaction) {
            Trade::create([
                'date' => $transaction->date,
                'time' => $transaction->time,
                'description' => $transaction->description,
                'currency' => $transaction->mutation,
                'total_transaction_value' => abs($transaction->mutation_value),
                'product' => $transaction->product,
                'isin' => $transaction->isin,
                'action' => $this->determineAction($transaction->description),
                'price_per_unit' => $this->determinePricePerUnit($transaction->description),
                'quantity' => $this->determineQuantity($transaction->description),
                'order_id' => $transaction->order_id,
                'fx' => $transaction->fx,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function determineAction($description)
    {
        // Input: Koop 13 @ 36,234 USD
        // Output: "Koop"
        if(Str::startsWith($description, 'Koop')) {
            return 'buy';
        }
        
        if (Str::startsWith($description, 'Verkoop')) {
            return 'sell';
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

    private function determinePricePerUnit($description)
    {
        // Input: Koop 13 @ 36,234 USD 
        // Output: "36,234"
        $value = Str::match('/@ ([\d,]+)/', $description);

        if (empty($value)) {
            return 0;
        }

        return (float) str_replace(',', '.', $value) * 100;
    }
}
