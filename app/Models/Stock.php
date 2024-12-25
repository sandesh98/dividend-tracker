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
        return DB::table('transactions')
            ->select(DB::raw("
                SUM(
                    CASE 
                        WHEN description LIKE '%verkoop%' THEN -CAST(REGEXP_SUBSTR(description, '[0-9]+') AS SIGNED)
                        WHEN description LIKE '%koop%' THEN CAST(REGEXP_SUBSTR(description, '[0-9]+') AS SIGNED)
                        ELSE 0
                    END
                ) as total_stocks
            "))
            ->where('product', 'LIKE', $product)
            ->where(function ($query) {
                $query->where('description', 'LIKE', '%koop%')
                      ->orWhere('description', 'LIKE', '%verkoop%');
            })
            ->groupBy('product')
            ->first()
            ->total_stocks ?? 0;
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
