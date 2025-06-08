<?php

namespace App\Services\Stocks;

use App\Value\CurrencyType;
use App\Value\DescriptionType;
use App\Value\TransactionType;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Collection;

class SellCalculator
{
    /**
     * Determine the sell value of a trade group based on its currency.
     *
     * @param Collection $tradeGroup
     * @return Money
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function calculateSell(Collection $tradeGroup): Money
    {
        $currency = $tradeGroup
            ->first()
            ->currency;

        return match ($currency) {
            'EUR' => $this->calculateSellEUR($tradeGroup),
            'USD' => $this->calculateSellUSD($tradeGroup),
            default => Money::of(0, CurrencyType::EUR->value),
        };
    }

    private function calculateSellEUR(Collection $tradeGroup)
    {
        $transactionCost = optional($tradeGroup->firstWhere('description', DescriptionType::DegiroTransactionCost->value))->total_transaction_value ?? 0;
        $fx = $tradeGroup->firstWhere('fx', '!=', null)->fx ?? 1;
        $totalPrice = $tradeGroup->firstWhere('action', TransactionType::Sell->value)->total_transaction_value;

        $value = BigDecimal::of($totalPrice)
            ->plus($transactionCost)
            ->multipliedBy($fx)
            ->toScale(0, RoundingMode::UP)
            ->toInt();

        return Money::ofMinor($value, CurrencyType::EUR->value);
    }

    /**
     * Calculate the sell value for a trade group in USD.
     *
     * @param Collection $tradeGroup
     * @return Money
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     * @throws DivisionByZeroException
     * @throws MathException
     */
    private function calculateSellUSD(Collection $tradeGroup): Money
    {
        $transactionCost = optional($tradeGroup->firstWhere('description', DescriptionType::DegiroTransactionCost->value))->total_transaction_value ?? 0;
        $fx = $tradeGroup->firstWhere('fx', '!=', null)->fx;
        $totalPrice = $tradeGroup->firstWhere('action', TransactionType::Sell->value)->total_transaction_value;

        $value = BigDecimal::of($totalPrice)
            ->plus($transactionCost)
            ->multipliedBy($fx)
            ->toScale(0, RoundingMode::UP)
            ->toInt();

        return Money::ofMinor($value, CurrencyType::EUR->value);

    }
}
