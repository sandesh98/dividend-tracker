<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Repositories\StockRepository;
use App\Repositories\TradeRepository;
use Scheb\YahooFinanceApi\ApiClient as YahooClient;

class StockService
{
    public function __construct(
        readonly private YahooClient     $yahooClient,
        readonly private StockRepository $stockRepository,
        readonly private TradeRepository $tradeRepository
    ) {}

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
                    'ticker' => $stockInfo[0]->getSymbol(),
                    'type' => $stockInfo[0]->getType()
                ]
            );
        }
    }

    public function getStockQuantity($stock)
    {
        $trades = $this->tradeRepository->getAllTradesFor($stock)->whereNotNull('action');

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
        $trades = $this->tradeRepository->getAllTradesFor($stock);

        $groupedTrades = $trades->groupBy('order_id');

        $totalInvestment = 0;

        foreach ($groupedTrades as $tradeGroup) {

            $currency = $tradeGroup->first()->currency;

            if ($currency === 'EUR') {

                $transactionCost = optional($tradeGroup->firstWhere('description', 'LIKE', 'DEGIRO Transactiekosten en/of kosten van derden'))->total_transaction_value;

                $buy = $tradeGroup->where('action', 'buy')->sum('total_transaction_value');
                $sell = $tradeGroup->where('action', 'sell')->sum('total_transaction_value');

                $totalInvestment += (($buy - $sell) + $transactionCost) / 100;
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
        $amountInvested = $this->getTotalAmoundInvested($stock);
        $stockQuantity = $this->getStockQuantity($stock);

        if ($stockQuantity <= 0) {
            return 0;
        }

        return round($amountInvested / $stockQuantity, 2);
    }

    public function getTotalValue($stock)
    {
        $quantity = $this->getStockQuantity($stock);

        $price = $this->stockRepository->findByName($stock)->getPrice();

        if ($quantity < 0 && $price < 0) {
            return 0;
        }

        $value = $price * $quantity;

        return Str::centsToEuro($value);
    }

    public function getProfitOrLoss(string $stock)
    {
        $totalValue = $this->getTotalValue($stock);
        $totalAmountInvested = $this->getTotalAmoundInvested($stock);

        // dd($totalValue, $totalAmountInvested);

        return $totalValue - $totalAmountInvested;
    }

    public function getLastPrice(string $stock): string
    {
        $product = $this->stockRepository->findByName($stock)->price;

        return Str::centsToEuro($product);
    }
}
