<?php

namespace App\Services\Dividends;

use Brick\Math\RoundingMode;
use Brick\Money\Money;

class CalculateUSDDividend extends DividendCalculator
{
    public function calculate($amount, $tax, $fx): Money
    {
        return $amount->minus($tax)->multipliedBy($fx)->toCurrency('EUR');
        //        return $amount
        //            ->minus($tax)
        //            ->multipliedBy($fx, RoundingMode::HALF_UP)
        //            ->toCurrency('EUR', RoundingMode::HALF_UP);
    }
}
