<?php

namespace App\Http\Controllers;

use App\Models\Dividend;
use App\Models\Stock;
use App\Models\Trade;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Services\Dividends\DividendService;
use App\Services\TransactionService;

class PortfolioController extends Controller
{
    public function __construct(
        readonly private TransactionService $transactionService,
        readonly private DividendService $dividendService
    ) {}

    public function index()
    {
        $transactionCosts = Trade::where('description', 'LIKE', 'DEGIRO Transactiekosten en/of kosten van derden')
            ->pluck('total_transaction_value')
            ->sum();

        $availableCash = $this->transactionService->getAvailableCash();
        $dividend = $this->dividendService->getDividendSum();


        $stockData = Trade::loadTable();

        $data = collect($stockData);

        [$active, $closed] = $data->partition(function ($stock) {
            return $stock['quantity'] > 0;
        });


        return view('portfolio.index', compact('availableCash', 'transactionCosts', 'dividend', 'active', 'closed'));
    }

    public function show($isin)
    {
        $stock = Stock::where('isin', 'LIKE', $isin)->first();

        return view('portfolio.show', compact('stock'));
    }
}
