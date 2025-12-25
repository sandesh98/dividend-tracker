<?php

namespace App\Services\Stocks\Calculators;

use App\Models\Stock;
use App\Models\Trade;
use App\Value\TransactionType;

class CalculateQuantity
{
    /**
     * Get quantity for the given stock.
     *
     * @param Stock $stock
     * @return int
     */
    public function __invoke(Stock $stock): int
    {
        $trades = $stock->trades()->get();

        $buy = $trades->filter(function (Trade $trade) {
            return $trade->action === TransactionType::Buy->value;
        })->sum('quantity');

        $sell = $trades->filter(function (Trade $item) {
            return $item->action === TransactionType::Sell->value;
        })->sum('quantity');

        $total = $buy - $sell;

        if ($total <= 0) {
            return 0;
        }

        return $total;
    }
}
