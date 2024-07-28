<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PortfolioController extends Controller
{
    public function index()
    {
        $stocks = Transaction::getUniqueStocksByName();

        $stocksarray = Transaction::calculateStockAmounts($stocks);

        $available_balance = Transaction::getAvailableBalance();

        return view('portfolio.index', compact('available_balance', 'stocksarray'));
    }

    public function show()
    {
        return view('portfolio.show');
    }
}
