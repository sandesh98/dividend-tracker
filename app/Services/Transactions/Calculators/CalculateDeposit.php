<?php

namespace App\Services\Transactions\Calculators;

use App\Models\CashMovement;
use App\Value\CurrencyType;
use App\Value\DescriptionType;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;

class CalculateDeposit
{
    /**
     * Calculate deposits.
     *
     * @throws UnknownCurrencyException
     */
    public function __invoke(): Money
    {
        $deposit = CashMovement::query()
            ->where('description', DescriptionType::Deposit)
            ->sum('total_transaction_value');

        return Money::ofMinor($deposit, CurrencyType::EUR->value);
    }
}
