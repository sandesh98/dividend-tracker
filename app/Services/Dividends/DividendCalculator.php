<?php

namespace App\Services\Dividends;

abstract class DividendCalculator
{

    abstract public function calculate(float $amount, float $tax, float $fx);
}
