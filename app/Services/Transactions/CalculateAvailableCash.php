<?php

namespace App\Services\Transactions;

use App\Services\Transactions\Calculators\CalculateDeposit;
use App\Services\Transactions\Calculators\CalculateWithdrawal;
use Brick\Money\Money;

class CalculateAvailableCash
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        private readonly CalculateDeposit $calculateDeposit,
        private readonly CalculateWithdrawal $calculateWithdrawal,
    ) {}

    /**
     * Calculate available cash.
     *
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function __invoke(): Money
    {
        $deposit = $this->calculateDeposit->__invoke();
        $withdrawal = $this->calculateWithdrawal->__invoke();

        return $deposit->minus($withdrawal);
    }
}
