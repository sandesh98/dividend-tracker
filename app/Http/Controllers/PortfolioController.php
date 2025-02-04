<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Trade;
use App\Services\Table\TableService;
use App\Services\Dividends\DividendService;
use App\Services\TransactionService;

class PortfolioController extends Controller
{
    public function __construct(
        readonly private TransactionService $transactionService,
        readonly private DividendService $dividendService,
        readonly private TableService $tableService
    ) {}

    public function index()
    {
        $transactionCosts = $this->transactionService->getTransactionscostsSum();
        $availableCash = $this->transactionService->getAvailableCash();
        $dividend = $this->dividendService->getDividendSum();
        [$active, $closed] = $this->tableService->loadTable();

        return view('portfolio.index', compact('availableCash', 'transactionCosts', 'dividend', 'active', 'closed'));
    }

    public function show($isin)
    {
        $stock = Stock::where('isin', 'LIKE', $isin)->first();

        return view('portfolio.show', compact('stock'));
    }
}
