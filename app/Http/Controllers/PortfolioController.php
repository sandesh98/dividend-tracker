<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Services\Stocks\StockService;
use App\Services\Table\TableService;
use App\Services\Transactions\CalculateAvailableCash;
use App\Services\Transactions\CalculateTransactionCost;

class PortfolioController extends Controller
{
    public function __construct(
        private readonly CalculateTransactionCost $transactionCost,
        private readonly CalculateAvailableCash $availableCash,
        private readonly TableService $tableService,
    ) {}

    public function index()
    {
        $transactionCost = $this->transactionCost->__invoke();
        $availableCash = $this->availableCash->__invoke();
        [$active, $closed] = $this->tableService->loadTable();

        return view('portfolio.index', compact('availableCash', 'transactionCost', 'active', 'closed'));
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
