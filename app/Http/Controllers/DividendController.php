<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Services\Dividends\DividendService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

class DividendController extends Controller
{
    public function __construct(
        private readonly DividendService $dividendService,
    ) {}

    public function index()
    {
        $firstTrade = Trade::orderBy('date')->first() ?? Date::now()->year;
        $firstTradeYear = Carbon::createFromFormat('d-m-Y', $firstTrade->date)->year;
        $dividendByYear = [];

        $years = range(
            start: $firstTradeYear,
            end: Date::now()->year,
        );

        foreach ($years as $year) {
            $dividendByYear[$year] = $this->dividendService->getDividendPerYear($year);
        }

        return view('dividend.index', compact('years', 'dividendByYear'));
    }
}
