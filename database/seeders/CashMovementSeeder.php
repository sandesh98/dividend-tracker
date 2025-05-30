<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CashMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = Transaction::query()
            ->where('description', 'iDEAL Deposit')
            ->orWhere('description', 'flatex terugstorting')
            ->get();

        foreach($transactions as $transaction) {
            DB::insert('INSERT INTO cash_movements (date, time, description, currency, total_transaction_value, created_at, updated_at) VALUES (:date, :time, :description, :currency, :total_transaction_value, :created_at, :updated_at)', [
                'date' => $transaction->date,
                'time' => $transaction->time,
                'description' => $transaction->description,
                'currency' => $transaction->mutation,
                'total_transaction_value' => $this->determineMutationValue($transaction->mutation_value),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Determine the mutation value.
     *
     * @param int $value
     * @return int
     */
    private function determineMutationValue(int $value): int
    {
        return $value < 0 ? abs($value) : $value;
    }
}
