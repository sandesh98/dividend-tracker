<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PortfolioController extends Controller
{
    public function index()
    {
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
        ->where('isin', 'LIKE', 'US1912161007')
        ->where(function ($query) {
            $query->where('description', 'LIKE', '%koop%')
                  ->orWhere('description', 'LIKE', '%verkoop%');
        })
        ->groupBy('isin')
        ->first();
    
        $stock_amount = $results ? $results->total_stocks : 0;

        $available_balance = Transaction::where('description', 'LIKE', 'Valuta Creditering')->first()->balance_value;

        return view('portfolio.index', compact('available_balance'));
    }

    public function show()
    {
        return view('portfolio.show');
    }
}
