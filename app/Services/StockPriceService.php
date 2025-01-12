<?php

namespace App\Services;

use App\Models\Stock;
use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;

class StockPriceService {

    private ApiClient $client;

    public function __construct()
    {
        $this->client = ApiClientFactory::createApiClient();
    }

    public function updatePrice()
    {
        $tickers = Stock::distinct()->pluck('ticker');

        $tickers->each(function ($ticker) {
            $this->updateStockPrice($ticker);
        });
    }

    public function updateStockPrice($ticker)
    {
        $historicalPrice = $this->client->getHistoricalQuoteData($ticker, '1wk', now()->previousWeekday(), now());
        $currency = $this->client->getQuote($ticker)->getCurrency();

        Stock::where('ticker', 'LIKE', $ticker)->update([
            'price' => $this->setPriceToCents($historicalPrice[0]->getOpen()),
            'currency' => $this->setCurrency($currency)
        ]);

    }

    private function setPriceToCents($initialPrice): int
    {
        return (int) round($initialPrice * 100);
    }

    private function setCurrency($currency): string
    {
        return $currency === 'EUR' ? 'EUR' : 'USD';
    }
}