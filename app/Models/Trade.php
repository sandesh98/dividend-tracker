<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    public static function getNames()
    {
        return Stock::distinct()->pluck('product', 'display_name');
    }

    public static function getIsin($stock)
    {
        return Stock::where('product', 'LIKE', $stock)->pluck('isin');
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
        $trades = self::where('product', 'LIKE', 'MASTERCARD INC-CL.A')->whereNotNull('action')->get();

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

        $groupedTrades = $trades->groupBy('order_id');

        foreach ($groupedTrades as $trade) {
            // Dit kan ook uit de loop gehaald worden volgens mij.
            if ($trade->first()->currency === 'EUR') {
                // ATM doet dit nog niks
                $buy = $trades->filter(function ($item) {
                    return $item->action === 'buy';
                })->sum('total_transaction_value');
        
        
                $sell = $trades->filter(function ($item) {
                    return $item->action === 'sell';
                })->sum('total_transaction_value');    
                
                return ($buy - $sell) / 100;
            }

            if ($trade->first()->currency === 'USD') {

                $fx = (int) ($trade->pluck('fx')->filter()[0] * 10000) / 10000;
                $transactioncost = $trade->firstWhere('description', 'LIKE' , 'DEGIRO Transactiekosten en/of kosten van derden')->total_transaction_value / 100;
                $transaction = $trade->firstWhere('action', 'LIKE', 'buy')->total_transaction_value / 100;

                return $transaction * (1 / $fx) + $transactioncost;                  
            }
        }



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

        foreach($uniqueStocks as $display_name => $product) {
            $quantity = self::getStockQuantity($product);
            $totalAmountInvested = self::getTotalAmoundInvested($product);
            $averageStockPrice = self::getAverageStockPrice($product);
            $isin = self::getIsin($product);

            $stockData[] = [
                'product' => $display_name,
                'isin' => $isin,
                'quantity' => $quantity,
                'averageStockPrice' => $averageStockPrice,
                'totalAmountInvested' => $totalAmountInvested
            ];
        }

        return $stockData;
    }
}
