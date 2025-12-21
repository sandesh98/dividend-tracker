<?php

namespace App\Services\Stocks\Calculators;

use App\Models\Stock;
use App\Services\Stocks\InvestmentCalculator;
use Brick\Math\RoundingMode;

readonly class AverageStockPriceCalculator
{
    public function __construct(
        private StockQuantityCalculator $stockQuantityCalculator,
        private InvestmentCalculator $investmentCalculator,
    ) {}

    /**
     * Get quantity for the given stock.
     *
     * @param Stock $stock
     * @return int
     */
    public function calculate(Stock $stock): int
    {
        // hoeveel ik zelf heb ingelegd / quantity


        $stocks = $stock->trades()->get();
        $amountInvested = $this->investmentCalculator->calculateInvestment($stocks);
        $stockQuantity = $this->stockQuantityCalculator->calculate($stock);

        if ($stockQuantity <= 0) {
            return 0;
        }

        return $amountInvested->dividedBy($stockQuantity, null, RoundingMode::UP);
    }
}
