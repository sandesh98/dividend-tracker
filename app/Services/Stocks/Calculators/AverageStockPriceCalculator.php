<?php

namespace App\Services\Stocks\Calculators;

use App\Models\Stock;
use App\Value\CurrencyType;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;

readonly class AverageStockPriceCalculator
{
    public function __construct(
        private StockQuantityCalculator $stockQuantityCalculator,
        private TotalInvestedCalculator $totalInvestedCalculator,
    ) {
    }

    /**
     * Get the average buy price for a given stock.
     *
     * @param Stock $stock
     * @return Money
     * @throws UnknownCurrencyException
     */
    public function calculate(Stock $stock): Money
    {
        $amountInvested = $this->totalInvestedCalculator->calculate($stock);
        $stockQuantity = $this->stockQuantityCalculator->calculate($stock);

        if ($stockQuantity <= 0) {
            return Money::of(0, CurrencyType::EUR->value);
        }

        return $amountInvested->dividedBy($stockQuantity, RoundingMode::HALF_EVEN);
    }
}
