<?php

namespace App\Services\Dividends;

use App\Models\Stock;
use App\Repositories\DividendRepository;
use App\Repositories\StockRepository;
use App\Value\CurrencyType;
use App\Value\DividendType;
use Brick\Math\BigDecimal;
use Brick\Money\Money;

class DividendService
{
    public function __construct(
        readonly private StockRepository    $stockRepository,
        readonly private DividendRepository $dividendRepository
    ) {}
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
            $money = $dividendCalculator->calculate($amount, $tax, $fx); // retourneert Money

            $total = $total->plus($money);
        }

        return $total->getMinorAmount(); // BigDecimal in centen
    }

    public function getDividendSum()
    {
//        $stocks = $this->stockRepository->getAllStockNames();
//        $sum = 0;
//
//        foreach ($stocks as $stock) {
//            $dividends = $this->getDividends($stock);
//            $sum += $dividends;
//        }
//
//        return $sum;

        return 1000;
    }
}
