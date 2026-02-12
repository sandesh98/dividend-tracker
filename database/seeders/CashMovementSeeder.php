<?php

namespace Database\Seeders;

use App\Models\CashMovement;
use App\Models\Transaction;
use App\Value\CashMovementType;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

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
            ->whereIn('description', [
                CashMovementType::Deposit,
                CashMovementType::Withdrawal,
            ])
            ->each(function (Transaction $transaction) {
                $cashMovement = new CashMovement;
                $cashMovement->occurred_at = Carbon::parse("{$transaction->date} {$transaction->time}");
                $cashMovement->description = $transaction->description;
                $cashMovement->amount = Money::of(
                    $this->parseMutationValue($transaction->mutation_value),
                    $transaction->mutation
                );
                $cashMovement->save();
            });
    }

    /**
     * Parse the mutation value.
     *
     * @param  int  $value
     * @return int
     */
    private function parseMutationValue(int $value): int
    {
        return $value < 0 ? abs($value) : $value;
    }
}
