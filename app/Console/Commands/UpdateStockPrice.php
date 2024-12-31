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

        $tickers = Stock::distinct()->pluck('ticker');

        foreach ($tickers as $ticker) {
            $quote = $client->getHistoricalQuoteData($ticker, '1wk', now()->previousWeekday(), now());

            Stock::where('ticker', 'LIKE', $ticker)->update([
                'price' => $quote[0]->getOpen(),
            ]); 
        }

        $this->info('updating the price');
    }
}
