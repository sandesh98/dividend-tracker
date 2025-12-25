<?php

namespace Database\Seeders;

use App\Models\CashMovement;
use App\Models\Transaction;
use App\Value\DescriptionType;
use Illuminate\Database\Seeder;

class CashMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transaction::query()
            ->whereIn('description', [DescriptionType::Deposit, DescriptionType::Withdrawal])
            ->each(function (Transaction $transaction) {
                $cashMovement = new CashMovement;
                $cashMovement->date = $transaction->date;
                $cashMovement->time = $transaction->time;
                $cashMovement->description = $transaction->description;
                $cashMovement->currency = $transaction->mutation;
                $cashMovement->total_transaction_value = $this->determineMutationValue($transaction->mutation_value);
                $cashMovement->save();
            });
    }

    /**
     * Determine the mutation value.
     */
    private function determineMutationValue(int $value): int
    {
        return $value < 0 ? abs($value) : $value;
    }
}
