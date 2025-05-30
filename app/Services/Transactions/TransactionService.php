<?php

namespace App\Services\Transactions;

use App\Models\CashMovement;
use App\Models\Stock;
use App\Models\Trade;
use App\Repositories\TransactionRepository;
use App\Value\CurrencyType;
use App\Value\DescriptionType;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * Get available cash.
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
     * Get transaction costs in cents for the given stock.
     *
     * @param Stock $stock
     * @return BigDecimal
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function getTransactionCosts(Stock $stock): BigDecimal
    {
        $trades = $stock->trades()
            ->where('description', DescriptionType::DegiroTransactionCost->value)
            ->sum('total_transaction_value');

        $costs = Money::ofMinor($trades, CurrencyType::EUR->value);

        return $costs->getMinorAmount();
    }


    /**
     * Get the sum of the transaction costs.
     *
     * @return integer
     * @throws MathException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function getTransactionsCostsSum(): int
    {
        $transactions = Trade::query()
            ->where('description', DescriptionType::DegiroTransactionCost->value)
            ->sum('total_transaction_value');

        $transactionCost = Money::ofMinor($transactions, CurrencyType::EUR->value);

        return $transactionCost->getMinorAmount()->toInt();
    }
}
