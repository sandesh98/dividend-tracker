<?php

namespace App\Services\Dividends;

use App\Value\CurrencyType;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Money;

class CalculateEURDividend extends DividendCalculator
{
    public function calculate(string $amount, string $tax, string $fx)
    {
        $money =  Money::of(
            BigDecimal::of($amount)
                ->minus($tax)
                ->multipliedBy($fx),
            CurrencyType::EUR->value
        );

        return $money->getAmount()->toInt();
    }
}
