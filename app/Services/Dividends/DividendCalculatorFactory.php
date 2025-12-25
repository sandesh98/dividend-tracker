<?php

namespace App\Services\Dividends;

class DividendCalculatorFactory
{
    public static function create(string $currency): CalculateEURDividend|CalculateUSDDividend
    {
        return match ($currency) {
            'EUR' => new CalculateEURDividend,
            'USD' => new CalculateUSDDividend,
            default => throw new \InvalidArgumentException("Unsupported currency: $currency"),
        };
    }
}
