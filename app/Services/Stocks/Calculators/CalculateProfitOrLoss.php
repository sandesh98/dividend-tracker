<?php

namespace App\Services\Stocks\Calculators;

use App\Models\Stock;
use Brick\Money\Money;

readonly class CalculateProfitOrLoss
{
    public function __construct(
        private CalculateMarketValue $marketValue,
        private CalculateTotalInvested $totalInvested,
    ) {}

    /**
     * Get the total profit or loss for a given stock.
     */
    public function __invoke(Stock $stock): Money
    {
        $totalInvested = $this->totalInvested->__invoke($stock);
        $marketValue = $this->marketValue->__invoke($stock);

        return $marketValue->minus($totalInvested);
    }
}
