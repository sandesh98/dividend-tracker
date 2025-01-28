<?php

namespace App\Models;

use App\Repositories\StockRepository;
use App\Services\Dividends\DividendService;
use App\Services\StockService;
use Database\Seeders\StockSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    public static function loadTable()
    {
        $stockData = [];
        $uniqueStocks = app(StockRepository::class)->getAllStockNames();

        foreach ($uniqueStocks as $display_name => $product) {
            $quantity = app(StockService::class)->getStockQuantity($product);
            $totalAmountInvested = app(StockService::class)->getTotalAmoundInvested($product);
            $averageStockPrice = app(StockService::class)->getAverageStockPrice($product);
            $isin = app(StockRepository::class)->getIsinsByName($product);
            $totalValue = app(StockService::class)->getTotalValue($product);
            $profitLoss = app(StockService::class)->getProfitOrLoss($product);
            $lastPrice = app(StockService::class)->getLastPrice($product);
            $type = app(StockService::class)->getType($product);
            $dividend = app(DividendService::class)->getDividends($product);

            $stockData[] = [
                'product' => $display_name,
                'isin' => $isin,
                'quantity' => $quantity,
                'averageStockPrice' => $averageStockPrice,
                'totalAmountInvested' => $totalAmountInvested,
                'totalValue' => $totalValue,
                'profitLoss' => $profitLoss,
                'lastPrice' => $lastPrice,
                'type' => $type,
                'dividend' => $dividend
            ];
        }

        return $stockData;
    }
}
