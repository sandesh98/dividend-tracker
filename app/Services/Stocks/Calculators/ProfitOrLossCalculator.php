<?php

namespace App\Services\Stocks\Calculators;

use App\Models\Stock;
use Brick\Money\Money;

readonly class ProfitOrLossCalculator
{
    public function __construct(
        private MarketValueCalculator $marketValue,
        private TotalInvestedCalculator $totalInvested,
    ) {
    }

    /**
     * Get the total profit or loss for a given stock.
     *
     * @param Stock $stock
     * @return Money
     */
    public function calculate(Stock $stock): Money
    {
        $totalInvested = $this->totalInvested->calculate($stock);
        $marketValue = $this->marketValue->calculate($stock);

        return $marketValue->minus($totalInvested);
    }
}
