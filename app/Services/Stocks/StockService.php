<?php

namespace App\Services\Stocks;

use App\Models\Stock;
use App\Services\Dividends\DividendService;
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
use Illuminate\Database\Eloquent\Collection;

readonly class StockService
{
    /**
     * Create a new StockService instance.
     */
    public function __construct(
        private DividendService $dividendService,
        private TransactionService $transactionService,
        private InvestmentCalculator $investmentCalculator,
        private SellCalculator $sellCalculator
    ) {}

    /**
     * Get the latest price in cents for the given stock.
     *
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function getLatestPrice(Stock $stock): BigDecimal
    {
        return Money::ofMinor($stock->price, CurrencyType::EUR->value)->getMinorAmount();
    }

    /**
     * Get the average stock sell price in cents for the given stock.
     *
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
            return $group->contains(fn ($trade) => $trade->action === TransactionType::Sell->value);
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
}
