<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PortfolioController extends Controller
{
    public function index()
    {
        $transactionCosts = Transaction::getTransactionCosts();
        $availableCash = Transaction::getAvailableCash();
        $stocksData = Stock::getAllStockData();

        $cocas = Stock::where('product', 'LIKE', '%coca%')
                    ->where(function($query) {
                        $query->where('description', 'LIKE', '%koop%')
                              ->orWhere('description', 'LIKE', '%verkoop%');
                    })->get();

        if ($cocas->first()->mutation === "EUR") {
            return (int) $cocas->sum('mutation_value') / 100;
        } else {
            $stockByOrderId = Stock::where('product', 'LIKE', '%cocas%')->get();
            // return $stockByOrderId;
        }
    

        return view('portfolio.index', compact('availableCash', 'transactionCosts', 'stocksData'));
    }

    public function show($stock)
    {
        $stockName = $stock;
        $stock = Stock::where('product', 'LIKE', $stock)->get();

        // dd($stock);

        return view('portfolio.show', compact('stockName'));
    }
}
