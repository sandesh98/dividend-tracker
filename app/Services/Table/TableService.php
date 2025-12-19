<?php

namespace App\Services\Table;

use App\Models\Stock;
use Illuminate\Support\Collection;
use App\Repositories\StockRepository;
use App\Services\Stocks\StockService;
use App\Repositories\DividendRepository;
use App\Services\Dividends\DividendService;

class TableService
{
    public function __construct(
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
        $quantity = $this->stockService->getStockQuantity($stock);
        $totalAmountInvested = $this->stockService->getTotalAmoundInvested($stock);
        $averageStockPrice = $this->stockService->getAverageStockPrice($stock);
        $isin = $this->stockRepository->getIsinsByName($stock);
        $marketValue = $this->stockService->getMarketValue($stock);
        $profitLoss = $this->stockService->getProfitOrLoss($stock);
        $realizedProfitLoss = $this->stockService->getrealizedProfitLoss($stock);
        $lastPrice = $this->stockService->getLastPrice($stock);
        $type = $this->stockRepository->getType($stock);
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
