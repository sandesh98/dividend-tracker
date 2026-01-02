<?php

namespace App\Services\Transactions\Calculators;

use App\Models\CashMovement;
use App\Value\CurrencyType;
use App\Value\DescriptionType;
use Brick\Money\Money;

class CalculateWithdrawal
{
    /**
     * Calculate withdrawals.
     *
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function __invoke(): Money
    {
        $withdrawal = CashMovement::query()
            ->where('description', DescriptionType::Withdrawal)
            ->sum('total_transaction_value');

        return Money::ofMinor($withdrawal, CurrencyType::EUR->value);
    }
}
