<?php

namespace App\Services\Table;

use App\Models\Stock;
use App\Services\Stocks\Calculators\CalculateAverageBuyPrice;
use App\Services\Stocks\Calculators\CalculateMarketValue;
use App\Services\Stocks\Calculators\CalculateProfitOrLoss;
use App\Services\Stocks\Calculators\CalculateQuantity;
use App\Services\Stocks\Calculators\CalculateTotalInvested;
use Illuminate\Support\Collection;
use App\Repositories\StockRepository;
use App\Services\Stocks\StockService;
use App\Repositories\DividendRepository;
use App\Services\Dividends\DividendService;

class TableService
{
    public function __construct(
        readonly private CalculateQuantity $quantity,
        readonly private CalculateTotalInvested $totalInvested,
        readonly private CalculateAverageBuyPrice $averageBuyPrice,
        readonly private CalculateMarketValue $marketValue,
        readonly private CalculateProfitOrLoss $profitOrLoss,


        readonly private DividendService $dividendService,
        readonly private StockRepository $stockRepository,
        readonly private StockService $stockService
    ) {
    }

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
            'averageStockSellPrice' => $averageStockSellPrice
        ];
    }
}
