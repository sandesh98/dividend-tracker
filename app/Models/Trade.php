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

        $groupedTrades = $trades->groupBy('order_id');

        $totalInvestment = 0;

        foreach ($groupedTrades as $tradeGroup) {

            $currency = $tradeGroup->first()->currency;

            if ($currency === 'EUR') {

                $transactionCost = optional($tradeGroup->firstWhere('description', 'LIKE', 'DEGIRO Transactiekosten en/of kosten van derden'))->total_transaction_value / 100;

                $buy = $tradeGroup->where('action', 'buy')->sum('total_transaction_value') / 100;
                $sell = $tradeGroup->where('action', 'sell')->sum('total_transaction_value') / 100;

                $totalInvestment += (($buy - $sell) + $transactionCost);

            } elseif ($currency === 'USD') {
                $fx = (float) $tradeGroup->pluck('fx')->filter()->first();

                $transactionCost = $tradeGroup
                    ->firstWhere('description', 'LIKE', 'DEGIRO Transactiekosten en/of kosten van derden')
                    ->total_transaction_value / 100;

                $buy = $tradeGroup->where('action', 'buy')->sum('total_transaction_value') / 100;
                $sell = $tradeGroup->where('action', 'sell')->sum('total_transaction_value') / 100 ?? 0;

                $totalInvestment += round(($buy - $sell) * (1 / $fx) + $transactionCost, 2);
            }
        }

        return $totalInvestment;
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
