<?php

namespace App\Services\Dividends;

class CalculateUSDDividend extends DividendCalculator
{
    public function calculate(float $amount, float $tax, float $fx)
    {
        return (($amount - $tax) / 100) * (1 / $fx);
    }
}
