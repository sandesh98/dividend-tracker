<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Models\Trade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

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
        $this->info('updating the price');
    }
}
