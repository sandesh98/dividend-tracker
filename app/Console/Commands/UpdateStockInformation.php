<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Stocks\StockService;

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
    public function handle(StockService $stockService)
    {
        $stockService->updateInformation();

        $this->info('Done retrieving stock information');
    }
}
