<?php

namespace App\Services\Stocks\Calculators;

use App\Models\Stock;
use App\Value\CurrencyType;
use App\Value\TransactionType;
use Brick\Money\Money;

readonly class CalculateNetSold
{
    /**
     * Get the total amount invested including fee's in cents for the given stock.
     */
    public function __invoke(Stock $stock): Money
    {
        $netSold = $stock->trades()
            ->where(function ($query) {
                $query->where('currency', CurrencyType::EUR->value);
                $query->where('action', TransactionType::Sell->value);
            })
            ->sum('total_transaction_value');

        return Money::ofMinor($netSold, CurrencyType::EUR->value);
    }
}
