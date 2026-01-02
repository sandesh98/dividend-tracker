<?php

namespace App\Services\Transactions;

use App\Models\Trade;
use App\Value\CurrencyType;
use App\Value\DescriptionType;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;

class CalculateTransactionCost
{
    /**
     * Calculate transaction costs.
     *
     * @throws UnknownCurrencyException
     */
    public function __invoke(): Money
    {
        $cost = Trade::query()
            ->where('description', DescriptionType::DegiroTransactionCost->value)
            ->sum('total_transaction_value');

        return Money::ofMinor($cost, CurrencyType::EUR->value);
    }
}
