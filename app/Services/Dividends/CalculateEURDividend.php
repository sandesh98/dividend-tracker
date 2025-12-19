<?php

namespace App\Services\Dividends;

use App\Value\CurrencyType;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Money;

class CalculateEURDividend extends DividendCalculator
{
    public function calculate($amount, $tax, $fx)
    {
        return $amount
            ->minus($tax)
            ->multipliedBy($fx);
    }
}
