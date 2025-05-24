<?php

namespace App\Services\Dividends;

use App\Value\CurrencyType;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Money;

class CalculateUSDDividend extends DividendCalculator
{
    public function calculate(string $amount, string $tax, string $fx)
    {
        return BigDecimal::of($amount)
            ->minus($tax)
            ->dividedBy($fx, 0, RoundingMode::HALF_UP)
            ->toInt();
    }
}
