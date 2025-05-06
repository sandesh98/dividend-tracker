<?php

namespace App\Services\Stocks;

use App\Models\Stock;
use App\Models\Trade;
use App\Value\CurrencyType;
use App\Value\TransactionType;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
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

    public function getStockQuantity(Stock $stock)
    {
        $trades = $stock->trades()->whereNotNull('quantity')->get();

        $buy = $trades->filter(function ($item) {
            return $item->action === TransactionType::Buy->value;
        })->sum('quantity');

        $sell = $trades->filter(function ($item) {
            return $item->action === TransactionType::Sell->value;
        })->sum('quantity');

        return ($buy - $sell);
    }

    public function getTotalAmoundInvested(Stock $stock)
    {
        $groupedTrades = $stock->trades()->get()->groupBy('order_id');

        $totalInvestment = Money::ofMinor(0, 'EUR');

        foreach ($groupedTrades as $tradeGroup) {
            $invested =  $this->calculateInvestment($tradeGroup);
            $totalInvestment = $totalInvestment->plus($invested);
        }

        return $totalInvestment->getAmount()->toFloat();
    }

    public function getAverageStockPrice(Stock $stock)
    {
        $amountInvested = $this->getTotalAmoundInvested($stock);
        $stockQuantity = $this->getStockQuantity($stock);

        if ($stockQuantity <= 0) {
            return 0;
        }

        return 100;

        return round($amountInvested / $stockQuantity, 2);
    }

    public function getTotalValue(Stock $stock)
    {
        $quantity = $this->getStockQuantity($stock);

        $price = $stock->price;

        if ($quantity < 0 && $price < 0) {
            return 0;
        }

        $value = $price * $quantity;

        return Money::ofMinor($value, CurrencyType::EUR->value)->getAmount();
    }

    public function getProfitOrLoss(Stock $stock)
    {
        $totalValue = $this->getTotalValue($stock);

        $value = Money::ofMinor($totalValue, CurrencyType::EUR->value)->getAmount();

        $totalAmountInvested = $this->getTotalAmoundInvested($stock);

        return 10000;

        dd($value, $totalAmountInvested);

        return $value->minus($totalAmountInvested);
    }

    public function getRealizedProfitLoss(Stock $stock)
    {
        $dividends = $this->dividendService->getDividends($stock);
        $transactionCost = $this->tradeRepository->getTransactioncostsFor($stock);

        return $dividends - $transactionCost;
    }

    public function getLastPrice(Stock $stock)
    {
        return Money::of($stock->price, CurrencyType::EUR->value)->getAmount();
    }

    public function getAverageStockSellPrice(Stock $stock)
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

    private function calculateInvestment(Collection $tradeGroup)
    {
        $currency = $tradeGroup->first()->currency;

        return match ($currency) {
            'EUR' => $this->calculateInvestmentEUR($tradeGroup),
            'USD' => $this->calculateInvestmentUSD($tradeGroup),
            default => Money::of(0, CurrencyType::EUR->value),
        };
    }

    private function calculateInvestmentEUR(Collection $tradeGroup)
    {
        $transactionCost = optional(
            $tradeGroup->firstWhere('description', 'DEGIRO Transactiekosten en/of kosten van derden')
        )->total_transaction_value ?? 0;
        $buy = $tradeGroup->where('action', 'buy')->sum('total_transaction_value');
        $sell = $tradeGroup->where('action', 'sell')->sum('total_transaction_value');

        $transactionMoney = Money::ofMinor($transactionCost, CurrencyType::EUR->value);
        $sellMoney = Money::ofMinor($sell, CurrencyType::EUR->value);
        $buyMoney = Money::ofMinor($buy, CurrencyType::EUR->value);

        return $buyMoney
            ->minus($sellMoney)
            ->plus($transactionMoney);
    }

    private function calculateInvestmentUSD(Collection $tradeGroup)
    {
        $fx = (float) $tradeGroup->pluck('fx')->filter()->first() ?: 1;

        $transactionCost = optional(
            $tradeGroup->firstWhere('description', 'LIKE', 'DEGIRO Transactiekosten en/of kosten van derden')
        )->total_transaction_value ?? 0;
        $buy = $tradeGroup->where('action', 'buy')->sum('total_transaction_value');
        $sell = $tradeGroup->where('action', 'sell')->sum('total_transaction_value') ?? 0;

        $transactionMoney = Money::ofMinor($transactionCost, CurrencyType::USD->value);
        $sellMoney = Money::ofMinor($sell, CurrencyType::USD->value);
        $buyMoney = Money::ofMinor($buy, CurrencyType::USD->value);

        $investmentInUSD = $buyMoney
            ->minus($sellMoney)
            ->dividedBy($fx, roundingMode: RoundingMode::HALF_UP)
            ->plus($transactionMoney);

        return Money::of($investmentInUSD->getAmount(), CurrencyType::EUR->value);
    }
}
