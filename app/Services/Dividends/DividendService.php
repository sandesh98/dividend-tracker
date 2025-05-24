<?php

namespace App\Services\Dividends;

use App\Models\Stock;
use App\Repositories\DividendRepository;
use App\Repositories\StockRepository;

class DividendService
{
    public function __construct(
        readonly private StockRepository    $stockRepository,
        readonly private DividendRepository $dividendRepository
    ) {}

    public function getDividends(Stock $stock)
    {
        $transactions = $stock->dividends()
            ->whereIn('description', ['Dividend', 'Dividendbelasting'])
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $dividendGroup = $transactions->groupBy(function ($item) {
            return $item->date . ' ' . $item->time;
        });

        $total = 0;

        foreach ($dividendGroup as $dividend) {
            $amount = $dividend->firstWhere('description', 'Dividend')->amount ?? 0;
            $tax = $dividend->firstWhere('description', 'Dividendbelasting')->amount ?? 0;
            $fx = $dividend->first()->fx ?? 1;
            $currency = $dividend->first()->mutation;

            $dividendCalculator = DividendCalculatorFactory::create($currency);

            $total += $dividendCalculator->calculate($amount, $tax, $fx);
        }

        return $total;
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
