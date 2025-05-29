<?php

namespace App\Services\Dividends;

use App\Value\CurrencyType;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Money;

class CalculateUSDDividend extends DividendCalculator
{
    public function calculate(string $amount, string $tax, string $fx): Money
    {
        $netUsdCents = BigDecimal::of($amount)->minus($tax);

        $eurCents = $netUsdCents
            ->dividedBy($fx, 0, RoundingMode::HALF_UP);

        return Money::ofMinor($eurCents, CurrencyType::EUR->value);
    }
}
