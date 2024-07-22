<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index()
    {
        $available_balance = Transaction::where('description', 'LIKE', 'Valuta Creditering')->first()->balance_value;

        return view('portfolio.index', compact('available_balance'));
    }

    public function show()
    {
        return view('portfolio.show');
    }
}
