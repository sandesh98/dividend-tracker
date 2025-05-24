<?php

namespace App\Services\Dividends;

use App\Value\CurrencyType;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Money;

class CalculateEURDividend extends DividendCalculator
{
    public function calculate(string $amount, string $tax, string $fx): Money
    {
        $netCents = BigDecimal::of($amount)
            ->minus($tax)
            ->multipliedBy($fx)
            ->toScale(0, RoundingMode::HALF_UP);

        return Money::ofMinor($netCents, CurrencyType::EUR->value);
    }
}
