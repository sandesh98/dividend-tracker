<?php

namespace App\Services\Stocks\Calculators;

use App\Models\Stock;
use App\Value\CurrencyType;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;

class CalculateMarketValue
{
    public function __construct(
        private CalculateQuantity $stockQuantityCalculator,
    ) {}

    /**
     * Get the market value for the given stock.
     *
     * @throws UnknownCurrencyException
     */
    public function __invoke(Stock $stock): Money
    {
        $quantity = $this->stockQuantityCalculator->__invoke($stock);
        $price = $stock->price;

        if ($quantity <= 0 || $price <= 0) {
            return Money::of(0, CurrencyType::EUR->value);
        }

        return Money::ofMinor($price, CurrencyType::EUR->value)->multipliedBy($quantity);
    }
}
