<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PortfolioController extends Controller
{
    public function index()
    {
        $stocks = Transaction::whereNotNull('isin')
            ->orderBy('product', 'asc')
            ->get()
            ->groupBy('product')
            ->keys();

        // dd($stocks);

        $stocksarray = [];

        foreach($stocks as $stock) {
            $results = DB::table('transactions')
            ->select(DB::raw("
                SUM(
                    CASE 
                        WHEN description LIKE '%verkoop%' THEN -CAST(REGEXP_SUBSTR(description, '[0-9]+') AS SIGNED)
                        WHEN description LIKE '%koop%' THEN CAST(REGEXP_SUBSTR(description, '[0-9]+') AS SIGNED)
                        ELSE 0
                    END
                ) as total_stocks
            "))
            ->where('product', 'LIKE', $stock)
            ->where(function ($query) {
                $query->where('description', 'LIKE', '%koop%')
                    ->orWhere('description', 'LIKE', '%verkoop%');
            })
            ->groupBy('isin')
            ->first();
        
            $stock_amount = $results ? $results->total_stocks : 0;

            $stocksarray[] = [
                'product' => $stock,
                'stock_amount' => $stock_amount
            ];
        }

        // dd($stocksarray);



        // Get available balance
        $available_balance = Transaction::where('description', 'LIKE', 'Valuta Creditering')->first()->balance_value;


        return view('portfolio.index', compact('available_balance', 'stocksarray'));
    }

    public function show()
    {
        return view('portfolio.show');
    }
}
