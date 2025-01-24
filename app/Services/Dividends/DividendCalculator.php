<?php

namespace App\Services\Dividends;

abstract class DividendCalculator {

    abstract public function calculate(float $fx);

}