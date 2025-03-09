<?php

namespace App\Services\Stocks;

use Illuminate\Support\Str;
use App\Repositories\StockRepository;
use App\Repositories\TradeRepository;
use App\Services\Dividends\DividendService;
use Illuminate\Database\Eloquent\Collection;
use Scheb\YahooFinanceApi\ApiClient as YahooClient;
use stdClass;

class StockService
{
    public function __construct(
        readonly private YahooClient        $yahooClient,
        readonly private StockRepository    $stockRepository,
        readonly private TradeRepository    $tradeRepository,
        readonly private DividendService    $dividendService,
    ) {}

    public function getStockQuantity(string $stock): float
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

    public function getTotalAmoundInvested(string $stock): float
    {
        $trades = $this->tradeRepository->getAllTradesFor($stock);
        $groupedTrades = $trades->groupBy('order_id');

        $totalInvestment = 0;

        foreach ($groupedTrades as $tradeGroup) {
            $totalInvestment += $this->calculateInvestment($tradeGroup);
        }

        return $totalInvestment;
    }

    public function getAverageStockPrice(string $stock)
    {
        $amountInvested = $this->getTotalAmoundInvested($stock);
        $stockQuantity = $this->getStockQuantity($stock);

        if ($stockQuantity <= 0) {
            return 0;
        }

        return round($amountInvested / $stockQuantity, 2);
    }

    public function getTotalValue(string $stock)
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

        return $totalValue - $totalAmountInvested;
    }

    public function getRealizedProfitLoss(string $stock): float
    {
        $dividends = $this->dividendService->getDividends($stock);
        $transactionCost = $this->tradeRepository->getTransactioncostsFor($stock);

        return $dividends - $transactionCost;
    }

    public function getLastPrice(string $stock): string
    {
        $product = $this->stockRepository->findByName($stock)->price;

        return Str::centsToEuro($product);
    }

    public function getAverageStockSellPrice(string $stock)
    {
        $trades = $this->tradeRepository->getAllTradesFor($stock);

        $sellTrades = $trades->groupBy('order_id')->filter(function ($group) {
            return $group->contains(fn($trade) => $trade->action === 'sell');
        });

        $trades = $sellTrades->mapWithKeys(function ($trade, $orderId) {
            $transactionCost = $trade->firstWhere('description', 'LIKE', 'DEGIRO Transactiekosten en/of kosten van derden');
            $transactionValue = $trade->firstWhere('action', 'LIKE', 'sell');

            return [
                $orderId => [
                    'transactionCost' => $transactionCost->total_transaction_value ?? 0,
                    'value' => $transactionValue->total_transaction_value ?? 0,
                    'quantity' => $transactionValue->quantity ?? 0,
                ],
            ];
        });

        $totalValue = $trades->sum(function ($item) {
            return ($item['transactionCost'] + $item['value'] * $item['quantity']);
        });

        $totalQuantity = $trades->sum('quantity');

        $averageSellPrice = $totalQuantity > 0 ? $totalValue / $totalQuantity : 0;

        return Str::centsToEuro($averageSellPrice);
    }

    public function getFirstTransactionDatetime($stock)
    {
        $date = $this->tradeRepository->getFirstTransactionDate($stock);
        $time = $this->tradeRepository->getFirstTransactionTime($stock);

        $result = new stdClass();
        $result->date = $date;
        $result->time = $time;

        return $result;
    }

    private function calculateInvestment(Collection $tradeGroup): float
    {
        $currency = $tradeGroup->first()->currency;

        return match ($currency) {
            'EUR' => $this->calculateInvestmentEUR($tradeGroup),
            'USD' => $this->calculateInvestmentUSD($tradeGroup),
            default => 0,
        };
    }

    private function calculateInvestmentEUR(Collection $tradeGroup): float
    {
        $transactionCost = optional($tradeGroup->firstWhere('description', 'LIKE', 'DEGIRO Transactiekosten en/of kosten van derden'))->total_transaction_value / 100 ?? 0;

        $buy = $tradeGroup->where('action', 'buy')->sum('total_transaction_value') / 100;
        $sell = $tradeGroup->where('action', 'sell')->sum('total_transaction_value') / 100;

        return ($buy - $sell) + $transactionCost;
    }

    private function calculateInvestmentUSD(Collection $tradeGroup): float
    {
        $fx = (float) $tradeGroup->pluck('fx')->filter()->first() ?: 1;

        $transactionCost = optional($tradeGroup->firstWhere('description', 'LIKE', 'DEGIRO Transactiekosten en/of kosten van derden'))->total_transaction_value / 100 ?? 0;

        $buy = $tradeGroup->where('action', 'buy')->sum('total_transaction_value') / 100;
        $sell = $tradeGroup->where('action', 'sell')->sum('total_transaction_value') / 100 ?? 0;

        return round(($buy - $sell) * (1 / $fx) + $transactionCost, 2);
    }
}
