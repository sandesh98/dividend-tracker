<?php

namespace App\Services\Dividends;

abstract class DividendCalculator
{
    abstract public function calculate(string $amount, string $tax, string $fx);
}
