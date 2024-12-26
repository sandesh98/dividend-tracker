<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Stock extends Model
{
    use HasFactory;

    public static function getNames()
    {
        return self::distinct()->pluck('product');
    }

    public static function getStockQuantity($product)
    {
        return 1;
    }

    public static function getTotalAmoundInvested($product)
    {
        return;
    }

    public static function getAllStockData()
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
