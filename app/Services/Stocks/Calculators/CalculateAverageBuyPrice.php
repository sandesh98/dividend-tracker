<?php

namespace App\Services\Stocks\Calculators;

use App\Models\Stock;
use App\Value\CurrencyType;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;

readonly class CalculateAverageBuyPrice
{
    public function __construct(
        private CalculateQuantity      $stockQuantityCalculator,
        private CalculateTotalInvested $totalInvestedCalculator,
    ) {
    }

    /**
     * Get the average buy price for a given stock.
     *
     * @param Stock $stock
     * @return Money
     * @throws UnknownCurrencyException
     */
    public function __invoke(Stock $stock): Money
    {
        $amountInvested = $this->totalInvestedCalculator->__invoke($stock);
        $stockQuantity = $this->stockQuantityCalculator->__invoke($stock);

        if ($stockQuantity <= 0) {
            return Money::of(0, CurrencyType::EUR->value);
        }

        return $amountInvested->dividedBy($stockQuantity, RoundingMode::HALF_EVEN);
    }
}
