<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    use HasFactory;

    public static function getUniqueStocksByName()
    {
        return self::whereNotNull('isin')
            ->orderBy('product', 'asc')
            ->get()
            ->unique('product');
    }

    public static function calculateStockAmounts($stocks)
    {
        return $stocks->map(function ($stock) {
            $stock->stock_amount = self::getStockAmount($stock->product);
            return $stock;
        });
    }

    public static function getStockAmount($product)
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

    public static function getAvailableBalance()
    {
        $transaction = self::where('description', 'LIKE', 'Valuta Creditering')->first();
        return $transaction ? $transaction->balance_value : 0;
    }

    public static function getTransactionCosts()
    {
        $totalTransactionCost = self::where('description', 'LIKE', 'DEGIRO Transactiekosten en/of kosten van derden')
            ->orWhere('description', 'LIKE', 'DEGIRO Aansluitingskosten%')
            ->sum(DB::raw('CAST(mutation_value AS DECIMAL(10,2))'));

        return (int) $totalTransactionCost;
    }
}
