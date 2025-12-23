<?php

namespace App\Services\Stocks\Calculators;

use App\Models\Stock;
use App\Value\CurrencyType;
use App\Value\TransactionType;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;

class MarketValueCalculator
{
    public function __construct(
        private StockQuantityCalculator $stockQuantityCalculator,
    ) {
    }

    /**
     * Get the market value for the given stock.
     *
     * @param Stock $stock
     * @return Money
     * @throws UnknownCurrencyException
     */
    public function calculate(Stock $stock): Money
    {
        $quantity = $this->stockQuantityCalculator->calculate($stock);
        $price = $stock->price;

        if ($quantity <= 0 || $price <= 0) {
            return Money::of(0, CurrencyType::EUR->value);
        }

        return Money::ofMinor($price, CurrencyType::EUR->value)->multipliedBy($quantity);
    }
}
