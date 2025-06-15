<?php

namespace App\Services\Stocks;

use App\Models\Stock;
use App\Services\Transactions\TransactionService;
use App\Value\CurrencyType;
use App\Value\TransactionType;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use App\Repositories\TradeRepository;
use App\Services\Dividends\DividendService;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

class StockService
{
    /**
     * Create a new StockService instance.
     *
     * @param DividendService $dividendService
     * @param TransactionService $transactionService
     * @param InvestmentCalculator $investmentCalculator
     * @param SellCalculator $sellCalculator
     */
    public function __construct(
        readonly private DividendService $dividendService,
        readonly private TransactionService $transactionService,
        readonly private InvestmentCalculator $investmentCalculator,
        readonly private SellCalculator $sellCalculator
    ) {
    }

    /**
     * Get quantity for the given stock.
     *
     * @param Stock $stock
     * @return int
     */
    public function getStockQuantity(Stock $stock): int
    {
        $trades = $stock->trades()->get();

        $buy = $trades->filter(function ($item) {
            return $item->action === TransactionType::Buy->value;
        })->sum('quantity');

        $sell = $trades->filter(function ($item) {
            return $item->action === TransactionType::Sell->value;
        })->sum('quantity');

        return ($buy - $sell);
    }

    /**
     * Get the total amount invested including fee's in cents for the given stock.
     *
     * @param Stock $stock
     * @return BigDecimal
     * @throws MathException
     * @throws MoneyMismatchException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function getTotalAmoundInvested(Stock $stock): BigDecimal
    {
        $trades = $stock->trades()
            ->get()
            ->groupBy('order_id');

        $totalInvestment = Money::of(0, CurrencyType::EUR->value);

        foreach ($trades as $trade) {
            $invested = $this->investmentCalculator->calculateInvestment($trade);
            $totalInvestment = $totalInvestment->plus($invested);
        }

        return $totalInvestment->getMinorAmount();
    }

    /**
     * Get the average stock price in cents for the given stock.
     *
     * @param Stock $stock
     * @return BigDecimal|int
     * @throws MathException
     * @throws MoneyMismatchException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function getAverageStockPrice(Stock $stock): BigDecimal|int
    {
        $amountInvested = $this->getTotalAmoundInvested($stock);
        $stockQuantity = $this->getStockQuantity($stock);

        if ($stockQuantity <= 0) {
            return 0;
        }

        return $amountInvested->dividedBy($stockQuantity, null, RoundingMode::UP);
    }

    /**
     * Get the market value in cents for the given stock.
     *
     * @param Stock $stock
     * @return BigDecimal|int
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function getMarketValue(Stock $stock): BigDecimal|int
    {
        $quantity = $this->getStockQuantity($stock);

        $price = $stock->price;

        if ($quantity < 0 && $price < 0) {
            return 0;
        }

        $value = $price * $quantity;

        return Money::ofMinor($value, CurrencyType::EUR->value)->getMinorAmount();
    }

    /**
     * Get the total profit of loss in cents for the given stock.
     *
     * @param Stock $stock
     * @return BigDecimal
     * @throws MathException
     * @throws MoneyMismatchException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function getProfitOrLoss(Stock $stock): BigDecimal
    {
        $totalValue = Money::ofMinor(
            $this->getMarketValue($stock),
            CurrencyType::EUR->value
        )->getMinorAmount();

        $totalAmountInvested = $this->getTotalAmoundInvested($stock);

        return $totalValue->minus($totalAmountInvested);
    }

    /**
     * Get the profit or loss without a dividend and transaction cost in cents for the given stock.
     *
     * @param Stock $stock
     * @return BigDecimal
     * @throws MathException
     * @throws MoneyMismatchException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function getRealizedProfitLoss(Stock $stock): BigDecimal
    {
        $profitOrLoss = $this->getProfitOrLoss($stock);
        $dividend = $this->dividendService->getDividends($stock);
        $transactionCost = $this->transactionService->getTransactionCosts($stock);

        return $profitOrLoss->minus($dividend)->minus($transactionCost);
    }

    /**
     * Get the last price in cents for the given stock.
     *
     * @param Stock $stock
     * @return BigDecimal
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function getLastPrice(Stock $stock): BigDecimal
    {
        return Money::ofMinor($stock->price, CurrencyType::EUR->value)->getMinorAmount();
    }

    /**
     * Get the average stock sell price in cents for the given stock.
     *
     * @param Stock $stock
     * @return BigDecimal
     * @throws MathException
     * @throws MoneyMismatchException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function getAverageStockSellPrice(Stock $stock): BigDecimal
    {
        $trades = $stock->trades()->get();

        $sellTrades = $trades->groupBy('order_id')->filter(function (Collection $group) {
            return $group->contains(fn($trade) => $trade->action === TransactionType::Sell->value);
        });

        $totalSellValue = Money::of(0, CurrencyType::EUR->value);
        $totalSoldQuantity = 0;

        foreach ($sellTrades as $tradeGroup) {
            $value = $this->sellCalculator->calculateSell($tradeGroup);
            $quantity = $tradeGroup->where('action', TransactionType::Sell->value)->sum('quantity');

            $totalSellValue = $totalSellValue->plus($value);
            $totalSoldQuantity += $quantity;
        }

        if ($totalSoldQuantity === 0) {
            return Money::of(0, CurrencyType::EUR->value)->getMinorAmount();
        }

        return $totalSellValue
            ->dividedBy($totalSoldQuantity, RoundingMode::UP)
            ->getMinorAmount();
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
}
