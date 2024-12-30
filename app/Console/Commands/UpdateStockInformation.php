<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Models\Trade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

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
        $isins = Trade::distinct()->pluck('isin', 'product');


        foreach ($isins as $product => $isin) {
            $response = Http::post('https://api.openfigi.com/v1/mapping', [
                [
                    'idType' => 'ID_ISIN',
                    'idValue' => $isin,
                ]
            ]);

            if ($response->ok()) {
                Stock::create([
                    'product' => $product,
                    'isin' => $isin,
                    'ticker' => $response->json()[0]['data'][0]['ticker']
                ]);
            } else {
                $this->warn('No information found about isin:' . $isin);
            }
        }

        $this->info('Done retrieving stock information');
    }
}
