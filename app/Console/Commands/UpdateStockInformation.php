<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Models\Trade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Scheb\YahooFinanceApi\ApiClientFactory;

class UpdateStockInformation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retreive and update stock data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = ApiClientFactory::createApiClient();
        $isins = Trade::distinct()->pluck('isin', 'product');

        foreach ($isins as $product => $isin) {
            $stock = $client->search($isin);

            if (empty($stock)) {
                $this->error('No information found for ' . $isin);

                continue;
            }

            Stock::create([
                'product' => $product,
                'display_name' => $stock[0]->getName(),
                'isin' => $isin,
                'ticker' => $stock[0]->getSymbol()
            ]);
        }

        $this->info('Done retrieving stock information');
    }
}
