<?php

namespace App\Services\Stocks;

use App\Value\CurrencyType;
use App\Value\DescriptionType;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Collection;

class InvestmentCalculator
{
    /**
     * Calculate the investment for the given trade group.
     *
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function calculateInvestment(Collection $tradeGroup): Money
    {
        $currency = $tradeGroup->first()->currency;

        return match ($currency) {
            'EUR' => $this->calculateInvestmentEUR($tradeGroup),
            'USD' => $this->calculateInvestmentUSD($tradeGroup),
            default => Money::of(0, CurrencyType::EUR->value),
        };
    }

    /**
     * Calculate the eur investment for the given trade group.
     *
     * @return Money
     *
     * @throws MathException
     * @throws MoneyMismatchException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    private function calculateInvestmentEUR(Collection $tradeGroup)
    {
        $transactionCost = optional(
            $tradeGroup->firstWhere('description', DescriptionType::DegiroTransactionCost->value)
        )->total_transaction_value ?? 0;
        $buy = $tradeGroup->where('action', 'buy')->sum('total_transaction_value');
        $sell = $tradeGroup->where('action', 'sell')->sum('total_transaction_value');

        $transactionMoney = Money::ofMinor($transactionCost, CurrencyType::EUR->value);
        $sellMoney = Money::ofMinor($sell, CurrencyType::EUR->value);
        $buyMoney = Money::ofMinor($buy, CurrencyType::EUR->value);

        return $buyMoney
            ->minus($sellMoney)
            ->plus($transactionMoney);
    }

    /**
     *  Calculate the eur investment for the given trade group.
     *
     * @return Money
     *
     * @throws MathException
     * @throws MoneyMismatchException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    private function calculateInvestmentUSD(Collection $tradeGroup)
    {
        $fx = (float) $tradeGroup->pluck('fx')->filter()->first() ?: 1;

        $transactionCost = optional(
            $tradeGroup->firstWhere('description', DescriptionType::DegiroTransactionCost->value)
        )->total_transaction_value ?? 0;
        $buy = $tradeGroup->where('action', 'buy')->sum('total_transaction_value');
        $sell = $tradeGroup->where('action', 'sell')->sum('total_transaction_value') ?? 0;

        $transactionMoney = Money::ofMinor($transactionCost, CurrencyType::USD->value);
        $sellMoney = Money::ofMinor($sell, CurrencyType::USD->value);
        $buyMoney = Money::ofMinor($buy, CurrencyType::USD->value);

        $investmentInUSD = $buyMoney->minus($sellMoney)
            ->dividedBy($fx, roundingMode: RoundingMode::HALF_UP)
            ->plus($transactionMoney);

        return Money::of($investmentInUSD->getAmount(), CurrencyType::EUR->value);
    }
}
