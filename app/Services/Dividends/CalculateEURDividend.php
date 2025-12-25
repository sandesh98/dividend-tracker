<?php

namespace App\Services\Dividends;

class CalculateEURDividend extends DividendCalculator
{
    public function calculate($amount, $tax, $fx)
    {
        return $amount
            ->minus($tax)
            ->multipliedBy($fx);
    }
}
