<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    public static function getNames()
    {
        return Stock::groupBy('product')->pluck('product');
    }

    public static function getAverageStockPrice($stock)
    {
        $averageStockPrice = self::getTotalAmoundInvested($stock) / self::getStockQuantity($stock);

        if ($averageStockPrice < 0) {
            return 0;
        }

        return round($averageStockPrice, 2);
    }

    public static function getStockQuantity($stock)
    {
        $trades = self::where('product', 'LIKE', $stock)->whereNotNull('action')->get();

        $buy = $trades->filter(function ($item) {
            return $item->action === 'buy';
        })->sum('quantity');

        $sell = $trades->filter(function ($item) {
            return $item->action === 'sell';
        })->sum('quantity');

        return ($buy - $sell);
    }

    public static function getTotalAmoundInvested($stock)
    {
        $trades = self::where('product', 'LIKE', $stock)->get();

        $buy = $trades->filter(function ($item) {
            return $item->action === 'buy';
        })->sum('total_transaction_value');


        $sell = $trades->filter(function ($item) {
            return $item->action === 'sell';
        })->sum('total_transaction_value');

        return ($buy - $sell) / 100;
    }

    public static function loadTable()
    {
        $stockData = [];
        $uniqueStocks = self::getNames();

        foreach($uniqueStocks as $stock) {
            $quantity = self::getStockQuantity($stock);
            $totalAmountInvested = self::getTotalAmoundInvested($stock);
            $averageStockPrice = self::getAverageStockPrice($stock);

            $stockData[] = [
                'product' => $stock,
                'quantity' => $quantity,
                'averageStockPrice' => $averageStockPrice,
                'totalAmountInvested' => $totalAmountInvested
            ];
        }

        return $stockData;
    }
}
