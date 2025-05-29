<?php

namespace App\Services\Dividends;

use App\Models\Stock;
use App\Value\CurrencyType;
use App\Value\DividendType;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Money;

class DividendService
{
    /**
     * Get total dividend amount for given stock.
     *
     * @param Stock $stock
     * @return BigDecimal
     * @throws MathException
     * @throws MoneyMismatchException
     */
    public function getDividends(Stock $stock): BigDecimal
    {
        $transactions = $stock->dividends()
            ->whereIn('description', [DividendType::Dividend->value, DividendType::DividendTax->value])
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $dividendGroup = $transactions->groupBy(fn($item) => $item->date . ' ' . $item->time);

        $total = Money::zero(CurrencyType::EUR->value);

        foreach ($dividendGroup as $dividend) {
            $amount = $dividend->firstWhere('description', DividendType::Dividend->value)->amount ?? 0;
            $tax = $dividend->firstWhere('description', DividendType::DividendTax->value)->amount ?? 0;
            $fx = $dividend->first()->fx ?? 1;
            $currency = $dividend->first()->currency;

            $dividendCalculator = DividendCalculatorFactory::create($currency);

            /** @var Money $money */
            $money = $dividendCalculator->calculate($amount, $tax, $fx);

            $total = $total->plus($money);
        }

        return $total->getMinorAmount();
    }

    /**
     * @return BigDecimal
     * @throws MathException
     * @throws MoneyMismatchException
     */
    public function getDividendSum(): BigDecimal
    {
        $stocks = Stock::all();

        $total = BigDecimal::zero();

        foreach ($stocks as $stock) {
            $dividend = $this->getDividends($stock);
            $total = $total->plus($dividend);
        }

        return $total;
    }
}
