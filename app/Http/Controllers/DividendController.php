<?php

namespace App\Http\Controllers;

use App\Models\Dividend;
use App\Models\Trade;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class DividendController extends Controller
{
    public function index()
    {

        $firstTrade = Trade::orderBy('date')->first() ?? Date::now()->year;
        $firstTradeYear = Carbon::createFromFormat('d-m-Y', $firstTrade->date)->year;

        $years = range(
            start: $firstTradeYear,
            end: Date::now()->year,
        );

        return view('dividend.index', compact('years'));
    }
}
