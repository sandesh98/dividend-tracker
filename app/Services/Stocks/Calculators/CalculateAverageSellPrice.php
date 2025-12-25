<?php

namespace App\Services\Stocks\Calculators;

use App\Models\Stock;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;

readonly class CalculateAverageSellPrice
{
    public function __construct(
        private CalculateQuantity $quantity,
        private CalculateNetSold $totalSold,
    ) {}

    /**
     * Get the average sell price for a given stock.
     *
     * @throws UnknownCurrencyException
     */
    public function __invoke(Stock $stock): Money
    {
        $sold = $this->totalSold->__invoke($stock);
        $quantity = $this->quantity->__invoke($stock);

        return $sold->dividedBy($quantity, RoundingMode::HALF_EVEN);
    }
}
