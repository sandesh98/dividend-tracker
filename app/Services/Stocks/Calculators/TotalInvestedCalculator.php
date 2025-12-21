<?php

namespace App\Services\Stocks\Calculators;

use App\Models\Stock;
use App\Services\Stocks\InvestmentCalculator;
use App\Value\CurrencyType;
use App\Value\DescriptionType;
use App\Value\TransactionType;
use Brick\Money\Money;

readonly class TotalInvestedCalculator
{
    /**
     * Get the total amount invested including fee's in cents for the given stock.
     *
     * @param Stock $stock
     * @return Money
     */
    public function calculate(Stock $stock): Money
    {
        $eurTrades = $stock->trades()
            ->where('currency', CurrencyType::EUR->value)
            ->get();

        $buyTotal = 0;
        $sellTotal = 0;
        $debit = 0;
        $credit = 0;
        $costs = 0;

        foreach ($eurTrades as $trade) {
            $amount = $trade->total_transaction_value;

            match (true) {
                $trade->description === DescriptionType::CurrencyDebit->value => $debit += $amount,
                $trade->description === DescriptionType::CurrencyCredit->value => $credit += $amount,
                $trade->description === DescriptionType::DegiroTransactionCost->value => $costs += $amount,
                $trade->action === TransactionType::Buy->value => $buyTotal += $amount,
                $trade->action === TransactionType::Sell->value => $sellTotal += $amount,
                default => null,
            };
        }

        $netInvestment = ($buyTotal + $debit) - ($sellTotal + $credit) + $costs;

        return Money::ofMinor($netInvestment, CurrencyType::EUR->value);
    }
}
