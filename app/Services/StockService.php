<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\Trade;
use App\Repositories\StockRepository;
use App\Repositories\TradeRepository;
use Scheb\YahooFinanceApi\ApiClient as YahooClient;

class StockService
{
    public function __construct(
        readonly private YahooClient     $yahooClient,
        readonly private StockRepository $stockRepository,
        readonly private TradeRepository $tradeRepository
    )
    {
    }

    public function updateInformation(): void
    {
        $isins = $this->tradeRepository->allUniqueProductAndIsins();

        foreach ($isins as $product => $isin) {
            $stockInfo = $this->yahooClient->search($isin);

            if (empty($stockInfo)) {
                continue;
            }

            $this->stockRepository->updateOrCreate(
                [
                    'isin' => $isin,
                ],
                [
                    'product' => $product,
                    'display_name' => $stockInfo[0]->getName(),
                    'ticker' => $stockInfo[0]->getSymbol()
                ]
            );
        }
    }

    public function getStockQuantity($stock)
    {
        $trades = Trade::where('product', 'LIKE', $stock)->whereNotNull('action')->get();

        $buy = $trades->filter(function ($item) {
            return $item->action === 'buy';
        })->sum('quantity');

        $sell = $trades->filter(function ($item) {
            return $item->action === 'sell';
        })->sum('quantity');

        return ($buy - $sell);
    }

    public function getTotalAmoundInvested($stock)
    {
        $trades = Trade::where('product', 'LIKE', $stock)->get();

        $groupedTrades = $trades->groupBy('order_id');

        $totalInvestment = 0;

        foreach ($groupedTrades as $tradeGroup) {

            $currency = $tradeGroup->first()->currency;

            if ($currency === 'EUR') {

                $transactionCost = optional($tradeGroup->firstWhere('description', 'LIKE', 'DEGIRO Transactiekosten en/of kosten van derden'))->total_transaction_value / 100;

                $buy = $tradeGroup->where('action', 'buy')->sum('total_transaction_value') / 100;
                $sell = $tradeGroup->where('action', 'sell')->sum('total_transaction_value') / 100;

                $totalInvestment += (($buy - $sell) + $transactionCost);

            } elseif ($currency === 'USD') {
                $fx = (float) $tradeGroup->pluck('fx')->filter()->first();

                $transactionCost = $tradeGroup
                    ->firstWhere('description', 'LIKE', 'DEGIRO Transactiekosten en/of kosten van derden')
                    ->total_transaction_value / 100;

                $buy = $tradeGroup->where('action', 'buy')->sum('total_transaction_value') / 100;
                $sell = $tradeGroup->where('action', 'sell')->sum('total_transaction_value') / 100 ?? 0;

                $totalInvestment += round(($buy - $sell) * (1 / $fx) + $transactionCost, 2);
            }
        }

        return $totalInvestment;
    }

    public function getAverageStockPrice($stock)
    {
        $amountInvested = $this->getTotalAmoundInvested($stock) ?? 0;
        $stockQuantity = $this->getStockQuantity($stock) ?? 0;

        if ($stockQuantity <= 0) {
            return 0;
        }

        $averageStockPrice = $amountInvested / $stockQuantity;

        if ($averageStockPrice < 0) {
            return 0;
        }

        return round($averageStockPrice, 2);
    }

    public function getTotalValue($stock)
    {
        $quantity = $this->getStockQuantity($stock);
        $price = Stock::where('product', 'LIKE', $stock)->first()->centsToEuros();

        if ($quantity < 0 && $price < 0) {
            return 0;
        }

        return $price * $quantity;
    }

    public function getProfitOrLoss($stock)
    {
        $totalValue = $this->getTotalValue($stock);
        $totalAmountInvested = $this->getTotalAmoundInvested($stock);

        return $totalValue - $totalAmountInvested;
    }
}