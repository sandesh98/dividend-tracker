<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PortfolioController extends Controller
{
    public function index()
    {
        $transactionCosts = Transaction::getTransactionCosts();
        $stocks = Transaction::getUniqueStocksByName();

        $stocksarray = Transaction::calculateStockAmounts($stocks);

        $available_balance = Transaction::getAvailableBalance();

        return view('portfolio.index', compact('available_balance', 'stocksarray', 'transactionCosts'));
    }

    public function show()
    {
        return view('portfolio.show');
    }
}
