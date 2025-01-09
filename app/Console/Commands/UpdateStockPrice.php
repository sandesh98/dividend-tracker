<?php

namespace App\Console\Commands;

use App\Models\Stock;
use Illuminate\Console\Command;
use Scheb\YahooFinanceApi\ApiClientFactory;

class UpdateStockPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retreive and update stock price';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = ApiClientFactory::createApiClient();

        // $bubba = $client->getHistoricalDividendData('AAPL', now()->subYears(5), now());
        // dd($bubba);
        $tickers = Stock::distinct()->pluck('ticker');

        foreach ($tickers as $ticker) {
            $quote = $client->getHistoricalQuoteData($ticker, '1wk', now()->previousWeekday(), now());

            Stock::where('ticker', 'LIKE', $ticker)->update([
                'price' => $this->setPriceToCents($quote[0]->getOpen()),
                'currency' => $this->setCurrency($client->getQuote($ticker)->getCurrency())
            ]); 
        }

        $this->info('Done updating the price');
    }

    public function setPriceToCents($initialPrice)
    {
        return (int) round($initialPrice * 100);
    }

    private function setCurrency($currency)
    {
        return $currency === 'EUR' ? 'EUR' : 'USD';
    }
}
