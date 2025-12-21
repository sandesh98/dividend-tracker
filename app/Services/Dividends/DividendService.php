<?php

namespace App\Services\Dividends;

use App\Models\Dividend;
use App\Models\Stock;
use App\Models\Trade;
use App\Value\CurrencyType;
use App\Value\DividendType;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use Brick\Math\RoundingMode;
use Brick\Money\CurrencyConverter;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\ExchangeRateProvider;
use Brick\Money\ExchangeRateProvider\ConfigurableProvider;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

class DividendService
{
    /**
     * Get the total dividend amount for a given stock.
     *
     * @param Stock $stock
     * @param int|null $year
     * @return Money
     * @throws MathException
     * @throws MoneyMismatchException
     */
    public function getDividends(Stock $stock)
    {
        $total = Money::zero('EUR');

        $dividendGroup = $stock->dividends->groupBy('paid_out_at');

        foreach ($dividendGroup as $dividendTransaction) {
            $dividendAmount = $dividendTransaction
                ->firstWhere('description', DividendType::Dividend)
                ->dividend_amount ?? Money::zero('EUR');

            $dividendTax = $dividendTransaction
                ->firstWhere('description', DividendType::DividendTax)
                ->dividend_amount ?? Money::zero('EUR');

            $fx = $dividendTransaction->first()->fx ?? 1;

            if ($dividendAmount->getCurrency()->getCurrencyCode() === CurrencyType::EUR->value) {
                $money = $dividendAmount->minus($dividendTax)->multipliedBy($fx);
            }

            if ($dividendAmount->getCurrency()->getCurrencyCode() === CurrencyType::USD->value) {
                $provider = new ConfigurableProvider();
                $provider->setExchangeRate('USD', 'EUR', '1.0000');

                $money = $dividendAmount->minus($dividendTax);

                $converter = new CurrencyConverter($provider);
                $money = $converter->convert($money, 'EUR', roundingMode: RoundingMode::DOWN);
            }

            $total = $total->plus($money);
        }

        return $total;
    }


//    /**
//     * Get a total dividend sum for all stocks.
//     *
//     * @return BigDecimal
//     * @throws MathException
//     * @throws MoneyMismatchException
//     */
//    public function getDividendSum(): BigDecimal
//    {
//        $stocks = Stock::all();
//
//        $total = BigDecimal::zero();
//
//        foreach ($stocks as $stock) {
//            $dividend = $this->getDividends($stock);
//
//            $total = $total->plus($dividend->getAmount());
//        }
//
//        return $total;
//    }
//
//    public function getDividendSumPerYear(): array
//    {
//        $stocks = Stock::all();
//
//        $firstTradeYear = Trade::orderBy('date')->first() ?? Date::now()->year;
//        $startYear = Carbon::createFromFormat('d-m-Y', $firstTradeYear->date)->year;
//        $endYear = Date::now()->year;
//
//        $perYear = [];
//
//        foreach (range($startYear, $endYear) as $year) {
//            $yearTotal = BigDecimal::zero();
//
//            foreach ($stocks as $stock) {
//                $yearTotal = $yearTotal->plus($this->getDividends($stock, $year));
//            }
//
//            $perYear[$year] = $yearTotal->toInt(); // centen
//        }
//
//        return $perYear;
//    }
//
//    public function getDividendPerYear(int $year)
//    {
//        $stocks = Stock::all();
//
//        $total = [];
//
//        foreach ($stocks as $stock) {
//            $dividend = $this->getDividends($stock, $year);
//
//            if ($dividend->isZero()) {
//                continue;
//            }
//
//            $total[] = [
//                'name' => $stock->name,
//                'amount' => $dividend->toInt(),
//            ];
//        }
//
//        return $total;
//    }
}
