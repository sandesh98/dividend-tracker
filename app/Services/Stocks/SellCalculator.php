<?php

namespace App\Services\Stocks;

use App\Value\CurrencyType;
use App\Value\DescriptionType;
use App\Value\TransactionType;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Collection;

class SellCalculator
{
    public function calculateSell(Collection $tradeGroup)
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
        return Money::of(200, CurrencyType::EUR->value);
    }

    private function calculateSellUSD(Collection $tradeGroup)
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
