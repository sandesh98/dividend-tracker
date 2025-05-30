<?php

namespace App\Services\Transactions;

use App\Models\CashMovement;
use App\Models\Stock;
use App\Models\Trade;
use App\Value\CurrencyType;
use App\Value\DescriptionType;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;

class TransactionService
{
    /**
     * Get available cash in cents.
     *
     * @return BigDecimal
     * @throws MathException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws MoneyMismatchException
     * @throws UnknownCurrencyException
     */
    public function getAvailableCash(): BigDecimal
    {
        // TODO: Alle gemaakte kosten moeten hier nog van afgetrokken worden.
        $deposits = CashMovement::query()
            ->where('description', DescriptionType::Deposit->value)
            ->sum('total_transaction_value');

        $withdrawals = CashMovement::query()
            ->where('description', DescriptionType::Withdrawal->value)
            ->sum('total_transaction_value');

        $balance = Money::ofMinor($deposits, CurrencyType::EUR->value)
            ->minus(Money::ofMinor($withdrawals, CurrencyType::EUR->value));

        return $balance->getMinorAmount();
    }

    /**
     * Get transaction costs in cents.
     *
     * @param Stock|null $stock
     * @return BigDecimal
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function getTransactionCosts(Stock $stock = null): BigDecimal
    {
        $query = $stock
            ? $stock->trades()
            : Trade::query();

        $sum = $query
            ->where('description', DescriptionType::DegiroTransactionCost->value)
            ->sum('total_transaction_value');

        $money = Money::ofMinor($sum, CurrencyType::EUR->value);

        return $money->getMinorAmount();
    }

}
