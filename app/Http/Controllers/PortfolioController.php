<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Trade;
use App\Models\Transaction;

class PortfolioController extends Controller
{
    public function index()
    {

        $transactionCosts = Trade::where('description', 'LIKE', 'DEGIRO Transactiekosten en/of kosten van derden')
                                ->pluck('total_transaction_value')
                                ->sum();

        $availableCash = Transaction::getAvailableCash();
        $stocksData = Trade::loadTable();

        return view('portfolio.index', compact('availableCash', 'transactionCosts', 'stocksData'));
    }

    public function show($stock)
    {
        $stockName = $stock;
        $stock = Trade::where('product', 'LIKE', $stock)->get();

        return view('portfolio.show', compact('stockName'));
    }
}
