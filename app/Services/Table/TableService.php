<?php

namespace App\Services\Table;

use Illuminate\Support\Collection;
use App\Repositories\StockRepository;
use App\Services\Stocks\StockService;
use App\Repositories\DividendRepository;
use App\Services\Dividends\DividendService;

class TableService
{
    public function __construct(
        readonly private DividendRepository $dividendRepository,
        readonly private DividendService    $dividendService,
        readonly private StockRepository    $stockRepository,
        readonly private StockService       $stockService
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
        $uniqueStocks = $this->stockRepository->getAllStockNames();

        foreach ($uniqueStocks as $displayName => $product) {
            $stockData[] = $this->getStockDetails($displayName, $product);
        }

        return $stockData;
    }

    public function getStockDetails(string $displayName, string $product): array
    {
        $quantity = $this->stockService->getStockQuantity($product);
        $totalAmountInvested = $this->stockService->getTotalAmoundInvested($product);
        $averageStockPrice = $this->stockService->getAverageStockPrice($product);
        $isin = $this->stockRepository->getIsinsByName($product);
        $totalValue = $this->stockService->getTotalValue($product);
        $profitLoss = $this->stockService->getProfitOrLoss($product);
        $rializedProfitLoss = $this->stockService->getrealizedProfitLoss($product);
        $lastPrice = $this->stockService->getLastPrice($product);
        $type = $this->stockRepository->getType($product);
        $dividend = $this->dividendService->getDividends($product);
        $averageStockSellPrice = $this->stockService->getAverageStockSellPrice($product);

        return [
            'product' => $displayName,
            'isin' => $isin,
            'quantity' => $quantity,
            'averageStockPrice' => $averageStockPrice,
            'totalAmountInvested' => $totalAmountInvested,
            'totalValue' => $totalValue,
            'profitLoss' => $profitLoss,
            'rializedProfitLoss' => $rializedProfitLoss,
            'lastPrice' => $lastPrice,
            'type' => $type,
            'dividend' => $dividend,
            'averageStockSellPrice' => $averageStockSellPrice
        ];
    }
}
