<?php

namespace App\Services\Dividends;

class CalculateEURDividend extends DividendCalculator {

    public function calculate(float $fx)
    {
        return $fx;
    }
    
}