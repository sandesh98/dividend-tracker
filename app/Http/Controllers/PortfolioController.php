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

        $cocas = Stock::where('product', 'LIKE', '%ing groep%')
                    ->where(function($query) {
                        $query->where('description', 'LIKE', '%koop%')
                              ->orWhere('description', 'LIKE', '%verkoop%');
                    })->get();

        if ($cocas->first()->mutation === "EUR") {
            return (int) $cocas->sum('mutation_value') / 100;
        } else {
            return 'USD';
        }
        
        dd((int) $cocas /100);
        
        foreach ($cocas as $coca) {
            echo $coca['mutation_value'] . PHP_EOL;
        }

        return view('portfolio.index', compact('availableCash', 'transactionCosts', 'stocksData'));
    }

    public function show()
    {
        return view('portfolio.show');
    }
}
