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
        $stockData = Trade::loadTable();

        $data = collect($stockData);

        [$active, $closed] = $data->partition(function ($stock) {
            return $stock['quantity'] > 0;
        });


        return view('portfolio.index', compact('availableCash', 'transactionCosts', 'active', 'closed'));
    }

    public function show($isin)
    {
        $stock = Stock::where('isin', 'LIKE', $isin)->first();

        return view('portfolio.show', compact('stock'));
    }
}
