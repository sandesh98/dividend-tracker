<?php

namespace Database\Seeders;

use App\Models\CashMovement;
use App\Models\Transaction;
use App\Value\CashMovementType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CashMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transaction::query()
            ->whereIn('description', [CashMovementType::Deposit, CashMovementType::Withdrawal])
            ->each(function (Transaction $transaction) {
                $cashMovement = new CashMovement;
                $cashMovement->occurred_at = Carbon::parse("{$transaction->date} {$transaction->time}");
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
