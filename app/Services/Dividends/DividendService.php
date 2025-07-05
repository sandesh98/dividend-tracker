<?php

namespace App\Services\Dividends;

use App\Models\Dividend;
use App\Models\Stock;
use App\Models\Trade;
use App\Value\CurrencyType;
use App\Value\DividendType;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

class DividendService
{
    /**
     * Get total dividend amount for given stock.
     *
     * @param Stock $stock
     * @param int|null $year
     * @return BigDecimal
     * @throws MathException
     * @throws MoneyMismatchException
     */
    public function getDividends(Stock $stock, ?int $year = null): BigDecimal
    {
        $transactions = $stock->dividends()
            ->when($year, fn($query) => $query->whereYear('date', $year))
            ->whereIn('description', [
                DividendType::Dividend->value,
                DividendType::DividendTax->value
            ])
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
            $money = $dividendCalculator->calculate($amount, $tax, $fx);

            $total = $total->plus($money);
        }

        return $total->getMinorAmount();
    }


    /**
     * Get total dividend sum for all stocks.
     *
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

    public function getDividendSumPerYear(): array
    {
        $stocks = Stock::all();

        $firstTradeYear = Trade::orderBy('date')->first() ?? Date::now()->year;
        $startYear = Carbon::createFromFormat('d-m-Y', $firstTradeYear->date)->year;
        $endYear = Date::now()->year;

        $perYear = [];

        foreach (range($startYear, $endYear) as $year) {
            $yearTotal = BigDecimal::zero();

            foreach ($stocks as $stock) {
                $yearTotal = $yearTotal->plus($this->getDividends($stock, $year));
            }

            $perYear[$year] = $yearTotal->toInt(); // centen
        }

        return $perYear;
    }

    public function getDividendPerYear(int $year)
    {
        $stocks = Stock::all();

        $total = [];

        foreach ($stocks as $stock) {
            $dividend = $this->getDividends($stock, $year);

            if ($dividend->isZero()) {
                continue;
            }

            $total[] = [
                'name' => $stock->name,
                'amount' => $dividend->toInt(),
            ];
        }

        return $total;
    }
}
