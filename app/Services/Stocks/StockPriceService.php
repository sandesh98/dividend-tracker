<?php

namespace App\Services\Stocks;

use App\Repositories\StockRepository;
use App\Repositories\TradeRepository;
use Scheb\YahooFinanceApi\ApiClient as YahooClient;

class StockPriceService
{
    public function __construct(
        readonly private YahooClient     $yahooClient,
        readonly private StockRepository $stockRepository,
        readonly private TradeRepository $tradeRepository
    ) {}

    public function updatePrice(): void
    {
        $tickers = $this->stockRepository->getAllTickers();

        foreach ($tickers as $ticker) {
            $this->updateStockPrice($ticker);
        }
    }

    private function updateStockPrice(string $ticker): void
    {
        $stock = $this->stockRepository->findByTicker($ticker);

        $historicalPrice = $this->yahooClient->getHistoricalQuoteData($ticker, '1wk', now()->previousWeekday(), now());
        $currency = $this->yahooClient->getQuote($ticker)->getCurrency();

        $conversion = $this->convertExchangeRate($historicalPrice[0]->getOpen(), $currency);

        $stock->update([
            'price' => $conversion['price'],
            'currency' => $conversion['currency']
        ]);
    }

    private function convertExchangeRate(float $initialPrice, string $currency): array
    {
        $exchangeRate = $this->yahooClient->getQuote('EURUSD=X')->getRegularMarketPrice();

        if ($currency === 'EUR') {
            return [
                'price' => $this->convertPriceToCents($initialPrice),
                'currency' => 'EUR'
            ];
        }

        return [
            'price' => $this->convertPriceToCents(($initialPrice / $exchangeRate)),
            'currency' => 'EUR'
        ];
    }

    public function convertPriceToCents(float $initialPrice): int
    {
        return round($initialPrice, 2) * 100;
    }
}
