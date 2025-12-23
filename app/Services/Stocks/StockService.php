<?php

namespace App\Services\Stocks;

use App\Models\Stock;
use App\Services\Stocks\Calculators\AverageStockPriceCalculator;
use App\Services\Stocks\Calculators\MarketValueCalculator;
use App\Services\Stocks\Calculators\StockQuantityCalculator;
use App\Services\Stocks\Calculators\TotalInvestedCalculator;
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
use App\Services\Dividends\DividendService;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

readonly class StockService
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
        private StockQuantityCalculator $stockQuantity,
        private AverageStockPriceCalculator $averageStockPrice,
        private TotalInvestedCalculator $totalInvested,
        private MarketValueCalculator $marketValue,
        private DividendService $dividendService,
        private TransactionService $transactionService,
        private InvestmentCalculator $investmentCalculator,
        private SellCalculator $sellCalculator
    ) {
    }

    /**
     * Get to quantity for a given stock.
     *
     * @param Stock $stock
     * @return int
     */
    public function quantity(Stock $stock): int
    {
        return $this->stockQuantity->calculate($stock);
    }

    /**
     * Get the total amount invested including fee's in cents for the given stock.
     *
     * @param Stock $stock
     * return BigDecimal
     */
    public function totalAmountInvested(Stock $stock): BigDecimal
    {
        return $this->totalInvested->calculate($stock)->getMinorAmount();
    }

    /**
     * Get the average stock price in cents for the given stock.
     *
     * @param Stock $stock
     * @return BigDecimal
     * @throws MathException
     * @throws MoneyMismatchException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function getAverageStockPrice(Stock $stock): BigDecimal
    {
        return $this->averageStockPrice->calculate($stock)->getMinorAmount();
    }

    /**
     * Get the market value in cents for the given stock.
     *
     * @param Stock $stock
     * @return BigDecimal
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function getMarketValue(Stock $stock): BigDecimal
    {
        return $this->marketValue->calculate($stock)->getMinorAmount();
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

        $totalAmountInvested = $this->totalAmountInvested($stock);

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
//        $dividend = $this->dividendService->getDividends($stock);
        $transactionCost = $this->transactionService->getTransactionCosts($stock);

        return $profitOrLoss->minus($transactionCost);
//        return $profitOrLoss->minus($dividend)->minus($transactionCost);
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
