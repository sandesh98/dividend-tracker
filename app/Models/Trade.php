<?php

namespace App\Models;

use App\Repositories\StockRepository;
use App\Services\StockPriceService;
use App\Services\StockService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;
    
    public static function loadTable()
    {
        $stockData = [];
        $uniqueStocks = app(StockRepository::class)->getAllStockNames();

        foreach($uniqueStocks as $display_name => $product) {
            $quantity = app(StockService::class)->getStockQuantity($product);
            $totalAmountInvested = app(StockService::class)->getTotalAmoundInvested($product);
            $averageStockPrice = app(StockService::class)->getAverageStockPrice($product);
            $isin = app(StockRepository::class)->findIsinByStock($product);
            $totalValue = app(StockService::class)->getTotalValue($product);

            $stockData[] = [
                'product' => $display_name,
                'isin' => $isin,
                'quantity' => $quantity,
                'averageStockPrice' => $averageStockPrice,
                'totalAmountInvested' => $totalAmountInvested,
                'totalValue' => $totalValue
            ];
        }

        return $stockData;
    }
}
