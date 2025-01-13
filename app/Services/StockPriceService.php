<?php

namespace App\Services;

use App\Models\Stock;
use App\Repositories\StockRepository;
use App\Repositories\TradeRepository;
use Scheb\YahooFinanceApi\ApiClient as YahooClient;

class StockPriceService {

    public function __construct(
        readonly private YahooClient     $yahooClient,
        readonly private StockRepository $stockRepository,
        readonly private TradeRepository $tradeRepository
    )
    {
    }

    public function updatePrice(): void
    {
        $tickers = $this->stockRepository->getTickers();

        foreach ($tickers as $ticker) {
            $this->updateStockPrice($ticker);
        }
    }

    public function updateStockPrice(string $ticker): void
    {
        $historicalPrice = $this->yahooClient->getHistoricalQuoteData($ticker, '1wk', now()->previousWeekday(), now());
        $currency = $this->yahooClient->getQuote($ticker)->getCurrency();

        $conversion = $this->convertExchangeRate($historicalPrice[0]->getOpen(), $currency);

        Stock::where('ticker', 'LIKE', $ticker)->update([
            'price' => $conversion['price'],
            'currency' => $conversion['currency']
        ]);

    }

    private function convertExchangeRate(float $initialPrice, string $currency): array
    {
        $exchangeRate = $this->yahooClient->getQuote('EURUSD=X')->getRegularMarketPrice();

        if ($currency === 'EUR') {
            return [
                'price' => $this->tradeRepository->convertPriceToCents($initialPrice),
                'currency' => 'EUR'
            ];
        }

        return [
            'price' => $this->tradeRepository->convertPriceToCents(($initialPrice / $exchangeRate)),
            'currency' => 'EUR'
        ];
    }
}