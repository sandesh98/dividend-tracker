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

        // $stocksByName = Stock::getNames();

        // $stocks = Transaction::getUniqueStocksByName();

        // $stocksarray = Transaction::calculateStockAmounts($stocksByName);

        return view('portfolio.index', compact('availableCash', 'transactionCosts', 'stocksData'));
    }

    public function show()
    {
        return view('portfolio.show');
    }
}
