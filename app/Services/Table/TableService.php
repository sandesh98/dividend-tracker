<?php

namespace App\Services\Table;

use App\Models\Stock;
use App\Repositories\StockRepository;
use App\Services\Dividends\DividendService;
use App\Services\Stocks\Calculators\CalculateAverageBuyPrice;
use App\Services\Stocks\Calculators\CalculateMarketValue;
use App\Services\Stocks\Calculators\CalculateProfitOrLoss;
use App\Services\Stocks\Calculators\CalculateQuantity;
use App\Services\Stocks\Calculators\CalculateTotalInvested;
use App\Services\Stocks\StockService;
use Illuminate\Support\Collection;

class TableService
{
    public function __construct(
        private readonly CalculateQuantity $quantity,
        private readonly CalculateTotalInvested $totalInvested,
        private readonly CalculateAverageBuyPrice $averageBuyPrice,
        private readonly CalculateMarketValue $marketValue,
        private readonly CalculateProfitOrLoss $profitOrLoss,

        private readonly DividendService $dividendService,
        private readonly StockRepository $stockRepository,
        private readonly StockService $stockService
    ) {}

    public function loadTable(): Collection
    {
        $data = collect($this->getPartitionedTable());

        return $data->partition(function ($stock) {
            return $stock['quantity'] > 0;
        });
    }

    public function getPartitionedTable(): array
    {
        $stockData = [];
        $stocks = Stock::all();

        foreach ($stocks as $stock) {
            $stockData[] = $this->getStockDetails($stock);
        }

        return $stockData;
    }

    public function getStockDetails(Stock $stock): array
    {
        $quantity = $this->quantity->__invoke($stock);
        $totalAmountInvested = $this->totalInvested->__invoke($stock);
        $averageStockPrice = $this->averageBuyPrice->__invoke($stock);
        $isin = $stock->isin;
        $marketValue = $this->marketValue->__invoke($stock);
        $profitLoss = $this->profitOrLoss->__invoke($stock);
        $realizedProfitLoss = $this->profitOrLoss->__invoke($stock);
        $lastPrice = $this->stockService->getLatestPrice($stock);
        $type = $stock->type;
        //        $dividend = $this->dividendService->getDividends($stock);
        $averageStockSellPrice = $this->stockService->getAverageStockSellPrice($stock);

        return [
            'stock' => $stock,
            'product' => $stock->display_name,
            'isin' => $stock->isin,
            'quantity' => $quantity,
            'averageStockPrice' => $averageStockPrice,
            'totalAmountInvested' => $totalAmountInvested,
            'marketValue' => $marketValue,
            'profitLoss' => $profitLoss,
            'realizedProfitLoss' => $realizedProfitLoss,
            'lastPrice' => $lastPrice,
            'type' => $type,
            //            'dividend' => $dividend,
            'averageStockSellPrice' => $averageStockSellPrice,
        ];
    }
}
