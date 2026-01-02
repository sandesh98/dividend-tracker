<?php

namespace App\Services\Transactions;

use App\Services\Transactions\Calculators\CalculateDeposit;
use App\Services\Transactions\Calculators\CalculateWithdrawal;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;

class CalculateAvailableCash
{
    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct(
        private readonly CalculateDeposit $calculateDeposit,
        private readonly CalculateWithdrawal $calculateWithdrawal,
    ) {}

    /**
     * Calculate available cash.
     *
     * @throws MoneyMismatchException
     * @throws UnknownCurrencyException
     */
    public function __invoke(): Money
    {
        $deposit = $this->calculateDeposit->__invoke();
        $withdrawal = $this->calculateWithdrawal->__invoke();

        return $deposit->minus($withdrawal);
    }
}
