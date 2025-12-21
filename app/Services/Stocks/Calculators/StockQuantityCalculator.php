<?php

namespace App\Services\Stocks\Calculators;

use App\Models\Stock;
use App\Value\TransactionType;

class StockQuantityCalculator
{
    /**
     * Get quantity for the given stock.
     *
     * @param Stock $stock
     * @return int
     */
    public function calculate(Stock $stock): int
    {
        $trades = $stock->trades()->get();

        $buy = $trades->filter(function ($item) {
            return $item->action === TransactionType::Buy->value;
        })->sum('quantity');

        $sell = $trades->filter(function ($item) {
            return $item->action === TransactionType::Sell->value;
        })->sum('quantity');

        return ($buy - $sell);
    }
}
