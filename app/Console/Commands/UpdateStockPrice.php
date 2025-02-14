<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Stocks\StockPriceService;
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
    public function handle(StockPriceService $stockPriceService)
    {

        $stockPriceService->updatePrice();

        $this->info('Done updating the price');
    }
}
