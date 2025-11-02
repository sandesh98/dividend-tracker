<?php

namespace Database\Seeders;

use App\Models\CashMovement;
use App\Models\Transaction;
use App\Value\DescriptionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CashMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Transaction::query()
            ->whereIn('description', [DescriptionType::Deposit, DescriptionType::Withdrawal])
            ->each(function (Transaction $transaction) {
                $cashMovement = new CashMovement();
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
     *
     * @param int $value
     * @return int
     */
    private function determineMutationValue(int $value): int
    {
        return $value < 0 ? abs($value) : $value;
    }
}
