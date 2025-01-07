<?php

namespace App\Services;

use App\Repositories\StockRepository;
use App\Repositories\TradeRepository;
use Scheb\YahooFinanceApi\ApiClient as YahooClient;

class StockService
{
    public function __construct(
        readonly private YahooClient     $yahooClient,
        readonly private StockRepository $stockRepository,
        readonly private TradeRepository $tradeRepository
    )
    {
    }

    public function updateInformation(): void
    {
        $isins = $this->tradeRepository->allUniqueProductAndIsins();

        foreach ($isins as $product => $isin) {
            $stockInfo = $this->yahooClient->search($isin);

            if (empty($stockInfo)) {
                continue;
            }

            $this->stockRepository->updateOrCreate(
                [
                    'isin' => $isin,
                ],
                [
                    'product' => $product,
                    'display_name' => $stockInfo[0]->getName(),
                    'ticker' => $stockInfo[0]->getSymbol()
                ]
            );
        }
    }
}
