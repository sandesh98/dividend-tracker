<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Services\Table\TableService;
use App\Services\Dividends\DividendService;
use App\Services\Stocks\StockService;
use App\Services\Transactions\TransactionService;

class PortfolioController extends Controller
{
    public function __construct(
        readonly private TransactionService $transactionService,
        readonly private DividendService $dividendService,
        readonly private TableService $tableService,
    ) {
    }

    public function index()
    {
        $transactionCosts = $this->transactionService->getTransactionsCostsSum();
        $availableCash = $this->transactionService->getAvailableCash();
        $dividend = $this->dividendService->getDividendSum();
        [$active, $closed] = $this->tableService->loadTable();

        return view('portfolio.index', compact('availableCash', 'transactionCosts', 'dividend', 'active', 'closed'));
    }

    public function show(Stock $stock)
    {
//        $stock = Stock::where('isin', 'LIKE', $isin)->first();

//        $quantity = $this->stockService->getStockQuantity($stock->product);
//        $averageStockPrice = $this->stockService->getAverageStockPrice($stock->product);
//        $date = $this->stockService->getFirstTransactionDatetime($stock->product);

//        return view('portfolio.show', compact('stock', 'quantity', 'averageStockPrice', 'date'));
    }
}
