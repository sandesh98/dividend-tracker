<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    public static function getNames()
    {
        return self::distinct()->pluck('product');
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

        // dd($trades);

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

            $stockData[] = [
                'product' => $stock,
                'quantity' => $quantity,
                'totalAmountInvested' => $totalAmountInvested
            ];
        }

        return $stockData;
    }
}
