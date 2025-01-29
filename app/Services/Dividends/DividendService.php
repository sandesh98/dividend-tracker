<?php

namespace App\Services\Dividends;

use App\Models\Dividend;
use App\Repositories\DividendRepository;
use App\Repositories\StockRepository;

class DividendService
{

    public function __construct(
        readonly private StockRepository    $stockRepository,
        readonly private DividendRepository $dividendRepository
    ) {}

    public function getDividends(string $stock)
    {
        $transactions = $this->dividendRepository->getTransactionsGroupsByDateAndTime($stock);

        $dividendGroups = $transactions->groupBy(function ($item) {
            return $item->date . ' ' . $item->time;
        });

        $results = [];

        foreach ($dividendGroups as $group) {
            $amount = $group->firstWhere('description', 'Dividend')->amount ?? 0;
            $tax = $group->firstWhere('description', 'Dividendbelasting')->amount ?? 0;
            $fx = $group->first()->fx ?? 1;

            $currency = $this->stockRepository->getCurrency($stock);
            $dividendCalculator = DividendCalculatorFactory::create($currency);

            $results[] = $dividendCalculator->calculate($amount, $tax, $fx);
        }

        return round(array_sum($results), 2);

        return 100;
    }

    public function getDividendSum()
    {
        $stocks = $this->stockRepository->getAllStockNames();
        $sum = 0;

        foreach ($stocks as $stock) {
            $dividends = $this->getDividends($stock);
            $sum += $dividends;
        }

        return $sum;
    }
}
